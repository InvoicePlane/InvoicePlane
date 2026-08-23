<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

#[AllowDynamicProperties]
class Sessions extends Base_Controller
{
    /**
     * Maximum allowed password reset token expiry time in minutes (24 hours)
     * This enforces a security upper limit on how long tokens can remain valid.
     */
    private const MAX_PASSWORD_RESET_EXPIRY_MINUTES = 1440;

    /**
     * UTC timezone instance for consistent timestamp handling
     * Reused across password reset operations to avoid repeated instantiation.
     *
     * @var DateTimeZone
     */
    private static $utc_timezone;

    public function index()
    {
        redirect('sessions/login');
    }

    public function login()
    {
        $view_data = [
            'login_logo' => get_setting('login_logo'),
        ];

        if ($this->input->post('btn_login')) {
            if ($this->authenticate($this->input->post('email'), $this->input->post('password'))) {
                if ($this->session->userdata('user_type') == 1) {
                    redirect('dashboard');
                } elseif ($this->session->userdata('user_type') == 2) {
                    redirect('guest');
                }
            } else {
                // Generic message for all failure cases to prevent account/status enumeration.
                $this->session->set_flashdata('alert_error', trans('loginalert_credentials_incorrect'));
                redirect('sessions/login');
            }
        }

        $this->load->view('session_login', $view_data);
    }

    /**
     * @param $email_address
     * @param $password
     */
    public function authenticate($email_address, $password): bool
    {
        $this->load->model('mdl_sessions');

        // IP-based rate limiting mirrors the password-reset throttle.
        if ($this->_is_ip_rate_limited_login()) {
            $this->load->helper('file_security');
            log_message('warning', 'Login IP rate limit exceeded from: ' . sanitize_for_logging($this->input->ip_address()));

            return false;
        }

        // Per-account lockout (email-keyed).
        $login_log = $this->_login_log_check($email_address);
        if (empty($login_log) || $login_log->log_count < 10) {
            if ($this->mdl_sessions->auth($email_address, $password)) {
                $this->_login_log_reset($email_address);
                $this->_reset_ip_login_attempts();

                return true;
            }

            $this->_login_log_addfailure($email_address);
            $this->_record_ip_login_attempt();
        }

        return false;
    }

    public function logout()
    {
        $this->session->sess_destroy();

        redirect('sessions/login');
    }

    /**
     * @return mixed
     */
    public function passwordreset($token = null)
    {
        // Shared, XSS/open-redirect-safe referer + CSRF helpers (not autoloaded).
        if ( ! function_exists('get_safe_referer')) {
            $this->load->helper('security');
        }

        // Check if a token was provided
        if ($token) {
            if (preg_match("/[^[:alnum:]\-_]/", $token)) {
                log_message('error', 'Incoming token is not alphanumeric (hash: ' . hash('sha256', $token) . ')');
                redirect('/');
            }

            //prevent brute force attacks by counting times a token is used
            $login_log_check = $this->_login_log_check($token);
            if ( ! empty($login_log_check) && $login_log_check->log_count > 10) {
                redirect(get_safe_referer('', 'sessions/passwordreset'));
            } else {
                //the use of a token counts as a failure
                $this->_login_log_addfailure($token);
            }

            $this->db->where('user_passwordreset_token', $token);
            $user = $this->db->get('ip_users');
            $user = $user->row();

            if (empty($user)) {
                // Unknown token: show the same generic "expired, request a new one" message as
                // the expiry paths so the response never reveals whether the token matched a
                // user, was malformed, or had expired.
                $this->session->set_flashdata('alert_error', trans('password_reset_token_expired'));
                redirect('sessions/passwordreset');
            }

            // Reject (and clear) the token if it has expired
            $this->_reject_expired_password_reset_token($user);

            //if token is valid, delete the failure attempt from
            //the login_log table
            $this->_login_log_reset($token);

            $formdata = [
                'token'   => $token,
                'user_id' => $user->user_id,
            ];

            return $this->load->view('session_new_password', $formdata);
        }

        // Check if the form for a new password was used
        if ($this->input->post('btn_new_password')) {
            // Validate the CSRF token before any state change. The new-password form emits
            // _csrf_field(); this mirrors Admin_Controller::ensure_valid_post_request(), which
            // Sessions (a Base_Controller, not an Admin_Controller) cannot call.
            if ( ! verify_csrf_token()) {
                $this->session->set_flashdata('alert_error', trans('invalid_request'));
                redirect(get_safe_referer('', 'sessions/passwordreset'));
            }

            $new_password = $this->input->post('new_password', true);
            $user_id      = $this->input->post('user_id', true);

            if (empty($user_id) || empty($new_password)) {
                $this->session->set_flashdata('alert_error', trans('loginalert_no_password'));
                redirect(get_safe_referer('', 'sessions/passwordreset'));
            }

            $this->load->model('users/mdl_users');

            // Check for the reset token
            $user = $this->mdl_users->get_by_id($user_id);

            // Unknown user_id and a wrong token must be indistinguishable, otherwise the
            // differing messages let an attacker enumerate valid user_ids on this POST. Both
            // return the same generic reset message used by the token-link flow.
            if (empty($user)) {
                $this->session->set_flashdata('alert_error', trans('password_reset_token_expired'));
                redirect(get_safe_referer('', 'sessions/passwordreset'));
            }

            if (empty($user->user_passwordreset_token) || ! hash_equals((string) $user->user_passwordreset_token, (string) $this->input->post('token'))) {
                $this->session->set_flashdata('alert_error', trans('password_reset_token_expired'));
                redirect(get_safe_referer('', 'sessions/passwordreset'));
            }

            // Enforce token expiry on the password-change POST as well, otherwise an expired
            // token that is still stored on the user row could be used to change the password.
            $this->_reject_expired_password_reset_token($user);

            // Call the save_change_password() function from users model
            $this->mdl_users->save_change_password(
                $user_id,
                $new_password
            );

            // Clear the password reset token and expiry
            $this->_clear_password_reset_token($user_id);

            // Delete failed login attempts from login_log table
            $user = $this->db->where('user_id', $user_id)->get('ip_users')->row();
            $this->_login_log_reset($user->user_email);

            // Redirect back to the login form
            redirect('sessions/login');
        }

        // Check if the password reset form was used
        if ($this->input->post('btn_reset', true)) {
            $this->load->helper('file_security');
            $email = $this->input->post('email', true);

            // Validate email format first
            if ( ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                log_message('error', trans('log_invalid_email_format') . ' (hash: ' . hash('sha256', (string) $email) . ') from IP: ' . sanitize_for_logging($this->input->ip_address()));
                redirect('sessions/login');
            }

            if (empty($email)) {
                log_message('warning', trans('log_empty_email_submitted') . ' from IP: ' . sanitize_for_logging($this->input->ip_address()));
                redirect('sessions/login');
            }

            // Security: Block automated tools and bots
            if ($this->_is_bot_request()) {
                log_message('warning', trans('log_password_reset_bot_detected') . ': ' . sanitize_for_logging($this->input->ip_address()) . ' User-Agent: ' . sanitize_for_logging($this->input->user_agent()));
                redirect('sessions/login');
            }

            // Security: Check IP-based rate limiting first (prevents email enumeration)
            if ($this->_is_ip_rate_limited_password_reset()) {
                log_message('warning', trans('log_password_reset_ip_rate_limit') . ' from: ' . sanitize_for_logging($this->input->ip_address()));
                redirect('sessions/login');
            }

            // Security: Prevent brute force attacks by counting password reset attempts per email
            if ($this->_is_email_rate_limited_password_reset($email)) {
                log_message('warning', trans('log_password_reset_email_rate_limit') . ' (hash: ' . hash('sha256', $email) . ') from IP: ' . sanitize_for_logging($this->input->ip_address()));
                redirect('sessions/login');
            }

            // Record the password reset attempt (both IP and email)
            $this->_record_password_reset_attempt();
            $this->_record_email_password_reset_attempt($email);

            // Test if a user with this email exists
            $this->db->where('user_email', $email);
            $user = $this->db->get('ip_users')->row();

            // Security: Always show the same message regardless of whether email exists
            // or the account is active - this prevents both email and active-status enumeration
            if ($user && (int) $user->user_active === 1) {
                // User exists and is active - send actual reset email
                // Use cryptographically secure token generation (fixes CVE-2021-29023)
                $this->load->helper('ip_security');
                $token = generate_password_reset_token();

                // Calculate token expiry time (default: 15 minutes from now)
                $expiry_minutes = (int) env('PASSWORD_RESET_TOKEN_EXPIRY_MINUTES', 15);

                // Validate expiry_minutes is within acceptable range (1-1440 minutes)
                // Maximum is defined by MAX_PASSWORD_RESET_EXPIRY_MINUTES (24 hours) for security
                if ($expiry_minutes < 1 || $expiry_minutes > self::MAX_PASSWORD_RESET_EXPIRY_MINUTES) {
                    // Invalid value, use default of 15 minutes
                    $expiry_minutes = 15;
                    log_message('warning', 'Invalid PASSWORD_RESET_TOKEN_EXPIRY_MINUTES value, using default 15 minutes');
                }

                try {
                    // Initialize UTC timezone if not already done
                    if ( ! isset(self::$utc_timezone)) {
                        self::$utc_timezone = new DateTimeZone('UTC');
                    }

                    // Use UTC timezone for consistent timestamp storage
                    $expiry_time = new DateTime('now', self::$utc_timezone);
                    $expiry_time->modify('+' . $expiry_minutes . ' minutes');
                    $expiry_timestamp = $expiry_time->format('Y-m-d H:i:s');
                } catch (Exception $e) {
                    // Fallback to simple timestamp calculation if DateTime fails
                    // Use gmdate() to maintain UTC consistency
                    log_message('error', 'DateTime creation failed, using fallback: ' . $e->getMessage());
                    $expiry_timestamp = gmdate('Y-m-d H:i:s', time() + ($expiry_minutes * 60));
                }

                // Save the token and expiry to the database
                $db_array = [
                    'user_passwordreset_token'        => $token,
                    'user_passwordreset_token_expiry' => $expiry_timestamp,
                ];

                $this->db->where('user_email', $email);
                $this->db->update('ip_users', $db_array);

                // Send the email with reset link
                $this->load->helper('mailer');

                // Prepare some variables for the email
                $email_resetlink = site_url('sessions/passwordreset/' . $token);
                $email_message   = $this->load->view('emails/passwordreset', [
                    'resetlink' => $email_resetlink,
                ], true);

                $email_from = get_setting('smtp_mail_from');
                if (empty($email_from)) {
                    $email_from = 'system@' . preg_replace("/^[\w]{2,6}:\/\/([\w\d\.\-]+).*$/", '$1', base_url());
                }

                // Mail the reset link with the pre-configured mailer if possible
                if (mailer_configured()) {
                    $this->load->helper('mailer/phpmailer');

                    if ( ! phpmail_send($email_from, $email, trans('password_reset'), $email_message)) {
                        $email_failed = true;
                    }
                } else {
                    $this->load->library('email');

                    // Set email configuration
                    $config['mailtype'] = 'html';
                    $this->email->initialize($config);

                    // Set the email params
                    $this->email->from($email_from);
                    $this->email->to($email);
                    $this->email->subject(trans('password_reset'));
                    $this->email->message($email_message);

                    // Send the reset email
                    if ( ! $this->email->send()) {
                        $email_failed = true;
                        log_message('error', $this->email->print_debugger());
                    }
                }

                // Show appropriate message
                if (isset($email_failed)) {
                    $this->session->set_flashdata('alert_error', trans('password_reset_failed'));
                } else {
                    $this->session->set_flashdata('alert_success', trans('email_successfully_sent'));
                }
            } else {
                // User doesn't exist or is inactive - show same success message to prevent enumeration
                // DO NOT send email to prevent abuse and RBL issues
                $this->session->set_flashdata('alert_success', trans('email_successfully_sent'));
                $log_key = $user
                    ? 'log_password_reset_inactive_user'
                    : 'log_password_reset_nonexistent_email';
                log_message('info', trans($log_key) . ' (hash: ' . hash('sha256', $email) . ') from IP: ' . sanitize_for_logging($this->input->ip_address()));
            }

            redirect('sessions/login');
        }

        return $this->load->view('session_passwordreset');
    }

    /**
     * Returns true when the current IP has exceeded the login attempt threshold.
     */
    private function _is_ip_rate_limited_login(): bool
    {
        $max_attempts   = (int) env('LOGIN_IP_MAX_ATTEMPTS', 20);
        $window_minutes = (int) env('LOGIN_IP_WINDOW_MINUTES', 15);
        $session_key    = 'login_attempts_ip_' . md5($this->input->ip_address());
        $attempts       = $this->session->userdata($session_key) ?: [];
        $cutoff         = time() - ($window_minutes * 60);
        $attempts       = array_values(array_filter($attempts, fn ($t) => $t > $cutoff));

        return count($attempts) >= $max_attempts;
    }

    /**
     * Records one failed login attempt for the current IP.
     */
    private function _record_ip_login_attempt(): void
    {
        $session_key = 'login_attempts_ip_' . md5($this->input->ip_address());
        $attempts    = $this->session->userdata($session_key) ?: [];
        $attempts[]  = time();
        $this->session->set_userdata($session_key, $attempts);
    }

    /**
     * Clears IP-based login attempt counter on successful authentication.
     */
    private function _reset_ip_login_attempts(): void
    {
        $session_key = 'login_attempts_ip_' . md5($this->input->ip_address());
        $this->session->unset_userdata($session_key);
    }

    /**
     * Checks if the login_log table has records for the
     * given.
     *
     * @param string $username
     *
     * @return object
     */
    private function _login_log_check($username)
    {
        $login_log_query = $this->db->where('login_name', $username)->get('ip_login_log')->row();

        // Security: the lockout threshold in authenticate() stops recording failures once
        // log_count reaches 10, so it never exceeds 10 - this must check >= 10, not > 10, or
        // the unlock branch below is unreachable and the lockout never expires. The window
        // check reuses _login_log_is_within_window() (Unix-timestamp based) instead of
        // DateInterval::$h, which is only the 0-23 hour *component* of the difference, not
        // the total elapsed hours.
        if ( ! empty($login_log_query) && $login_log_query->log_count >= 10
            && ! $this->_login_log_is_within_window($login_log_query, 12 * 3600)) {
            $this->_login_log_reset($username);

            return;
        }

        return $login_log_query;
    }

    /**
     * Check if IP address has exceeded rate limit for password resets.
     *
     * @return bool True if rate limited, false otherwise
     */
    private function _is_ip_rate_limited_password_reset()
    {
        $max_attempts   = (int) env('PASSWORD_RESET_IP_MAX_ATTEMPTS', 5);
        $window_minutes = (int) env('PASSWORD_RESET_IP_WINDOW_MINUTES', 60);
        $ip_address     = $this->input->ip_address();
        $login_log      = $this->_login_log_check($this->_password_reset_ip_log_key($ip_address));

        if ( ! empty($login_log) && $login_log->log_count >= $max_attempts && $this->_login_log_is_within_window($login_log, $window_minutes * 60)) {
            $this->load->helper('file_security');
            log_message('info', trans('log_ip_rate_limit_check') . ': ' . (int) $login_log->log_count . ' attempts from IP: ' . sanitize_for_logging($ip_address));

            return true;
        }

        return false;
    }

    /**
     * Record a password reset attempt for the current IP.
     */
    private function _record_password_reset_attempt()
    {
        $window_minutes = (int) env('PASSWORD_RESET_IP_WINDOW_MINUTES', 60);

        $this->_record_password_reset_log_attempt(
            $this->_password_reset_ip_log_key($this->input->ip_address()),
            $window_minutes * 60
        );
    }

    /**
     * Check if email-based rate limit exceeded for password resets.
     *
     * @param string $email Email address to check
     *
     * @return bool True if rate limited, false otherwise
     */
    private function _is_email_rate_limited_password_reset($email)
    {
        $max_attempts = (int) env('PASSWORD_RESET_EMAIL_MAX_ATTEMPTS', 3);
        $window_hours = (int) env('PASSWORD_RESET_EMAIL_WINDOW_HOURS', 1);
        $login_log    = $this->_login_log_check($this->_password_reset_email_log_key($email));

        if ( ! empty($login_log) && $login_log->log_count >= $max_attempts && $this->_login_log_is_within_window($login_log, $window_hours * 3600)) {
            log_message('info', trans('log_email_rate_limit_check') . ': ' . (int) $login_log->log_count . ' attempts (hash: ' . hash('sha256', $email) . ')');

            return true;
        }

        return false;
    }

    /**
     * Record a password reset attempt for a specific email.
     *
     * @param string $email Email address
     */
    private function _record_email_password_reset_attempt($email)
    {
        $window_hours = (int) env('PASSWORD_RESET_EMAIL_WINDOW_HOURS', 1);

        $this->_record_password_reset_log_attempt(
            $this->_password_reset_email_log_key($email),
            $window_hours * 3600
        );
    }

    private function _password_reset_ip_log_key(string $ip_address): string
    {
        return 'password_reset_ip:' . hash('sha256', $ip_address);
    }

    private function _password_reset_email_log_key(string $email): string
    {
        return 'password_reset_email:' . hash('sha256', mb_strtolower($email));
    }

    private function _record_password_reset_log_attempt(string $login_name, int $window_seconds): void
    {
        $login_log = $this->_login_log_check($login_name);

        if ( ! empty($login_log) && ! $this->_login_log_is_within_window($login_log, $window_seconds)) {
            $this->_login_log_reset($login_name);
            $login_log = null;
        }

        $this->_login_log_addfailure($login_name);
    }

    private function _login_log_is_within_window(object $login_log, int $window_seconds): bool
    {
        try {
            $timestamp = new DateTime($login_log->log_create_timestamp);
        } catch (Exception) {
            return false;
        }

        return $timestamp->getTimestamp() > (time() - $window_seconds);
    }

    /**
     * Check if the current request is from an automated tool or bot.
     *
     * @return bool True if bot/automated tool detected, false otherwise
     */
    private function _is_bot_request()
    {
        $user_agent = $this->input->user_agent();

        // List of common automated tools and bots
        $bot_signatures = [
            'curl',
            'wget',
            'python-requests',
            'go-http-client',
            'java/',
            'apache-httpclient',
            'okhttp',
            'httpclient',
            'bot',
            'spider',
            'crawler',
            'scraper',
            'postman',
            'insomnia',
            'paw/',
        ];

        // Check if user agent is empty (common with automated tools)
        if (empty($user_agent)) {
            return true;
        }

        // Check if user agent contains any bot signatures (case-insensitive)
        $user_agent_lower = mb_strtolower($user_agent);
        foreach ($bot_signatures as $signature) {
            if (str_contains($user_agent_lower, $signature)) {
                return true;
            }
        }

        return false;
    }

    /**
     * If the username has a record in the login_log
     * table the count is incremented by 1, otherwise
     * a record for the given user is created.
     *
     * @param string $username
     */
    private function _login_log_addfailure($username)
    {
        if (empty($login_log_check = $this->_login_log_check($username))) {
            //create the log
            $this->db->insert('ip_login_log', [
                'login_name'           => $username,
                'log_count'            => 1,
                'log_create_timestamp' => date('c'),
            ]);
        } else {
            //update the log
            $this->db->set([
                'log_count'            => $login_log_check->log_count + 1,
                'log_create_timestamp' => date('c'),
            ])
                ->where('login_name', $username)
                ->update('ip_login_log');
        }
    }

    /**
     * The record of the given user is deleted from the
     * login_log table.
     *
     * @param string $username
     */
    private function _login_log_reset($username)
    {
        $this->db->delete('ip_login_log', ['login_name' => $username]);
    }

    /**
     * Rejects an expired (or malformed) password reset token.
     *
     * Shared by the token-link (GET) and password-change (POST) flows so both enforce the
     * same lifetime. When the token has expired or its stored expiry cannot be parsed, the
     * token is cleared and the request is redirected back to the reset page. When the token
     * is still valid this returns and execution continues.
     *
     * @param object $user The user row (must expose user_id and user_passwordreset_token_expiry)
     */
    private function _reject_expired_password_reset_token($user): void
    {
        if (empty($user->user_passwordreset_token_expiry)) {
            return;
        }

        try {
            // Initialize UTC timezone if not already done
            if ( ! isset(self::$utc_timezone)) {
                self::$utc_timezone = new DateTimeZone('UTC');
            }

            // Use UTC timezone for consistent timestamp comparison. Parse strictly:
            // new DateTime() accepts out-of-range values such as "25:99:99", and
            // createFromFormat() silently normalizes non-canonical strings such as
            // "2026-8-10 9:05:07" (single-digit fields) without a warning. The stored expiry is
            // always written canonically as Y-m-d H:i:s, so require that exact anchored shape,
            // then reject any parser warning or error before the elapsed-time check.
            $raw_expiry   = (string) $user->user_passwordreset_token_expiry;
            $expiry_time  = DateTime::createFromFormat('!Y-m-d H:i:s', $raw_expiry, self::$utc_timezone);
            $parse_errors = DateTime::getLastErrors();
            if (
                ! preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $raw_expiry)
                || $expiry_time === false
                || ($parse_errors !== false
                    && ($parse_errors['warning_count'] > 0 || $parse_errors['error_count'] > 0))
            ) {
                throw new Exception('Invalid password reset token expiry');
            }
            $current_time = new DateTime('now', self::$utc_timezone);

            if ($current_time > $expiry_time) {
                // Token has expired, clear it from database
                $this->_clear_password_reset_token($user->user_id);

                $this->load->helper('file_security');
                log_message('info', 'Expired password reset token used for user ID: ' . sanitize_for_logging($user->user_id));
                $this->session->set_flashdata('alert_error', trans('password_reset_token_expired'));
                redirect('sessions/passwordreset');
            }
        } catch (Exception $e) {
            // Invalid or malformed expiry: clear the token for safety. Log the specifics
            // server-side, but show the user the same generic "expired, request a new one"
            // message as the normal expiry path so the response never reveals which internal
            // check failed (malformed vs. expired vs. unknown token).
            $this->load->helper('file_security');
            log_message('error', 'Invalid password reset token expiry format for user ID: ' . sanitize_for_logging($user->user_id));
            $this->_clear_password_reset_token($user->user_id);
            $this->session->set_flashdata('alert_error', trans('password_reset_token_expired'));
            redirect('sessions/passwordreset');
        }
    }

    /**
     * Clears the password reset token and expiry for a user.
     * Helper method to avoid code duplication.
     *
     * @param int $user_id The user ID (will be type-cast to ensure it's an integer)
     */
    private function _clear_password_reset_token($user_id): void
    {
        // Ensure user_id is an integer for safety
        $user_id = (int) $user_id;

        $this->db->where('user_id', $user_id);
        $this->db->update('ip_users', [
            'user_passwordreset_token'        => '',
            'user_passwordreset_token_expiry' => null,
        ]);
    }
}
