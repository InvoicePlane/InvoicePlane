<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use Jumbojett\OpenIDConnectClient;
use Jumbojett\OpenIDConnectClientException;

/**
 * OIDC Library for InvoicePlane
 *
 * Handles OpenID Connect authentication for Single Sign-On
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2024 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */
class Oidc
{
    private $CI;
    private $oidc;
    private $enabled = false;
    private $providerUrl;
    private $clientId;
    private $clientSecret;
    private $scopes;
    private $verifySsl;
    private $requireEmailVerified;
    private $tokenAuthMethods;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('settings/mdl_settings');
        $this->CI->load->library('crypt');

        $this->loadSettings();
    }

    /**
     * Load OIDC settings from database or environment
     */
    private function loadSettings()
    {
        // Check environment variables first, then fall back to database settings
        // Environment takes precedence - if OIDC_ENABLED is set, use it; otherwise check database
        if (env('OIDC_ENABLED') !== null) {
            $this->enabled = env_bool('OIDC_ENABLED', 'false');
        } else {
            $this->enabled = get_setting('oidc_enabled') == '1';
        }

        $this->providerUrl = env('OIDC_PROVIDER_URL') ?: get_setting('oidc_provider_url');
        $this->clientId = env('OIDC_CLIENT_ID') ?: get_setting('oidc_client_id');

        // Client secret may be encrypted in database
        $clientSecretEnv = env('OIDC_CLIENT_SECRET');
        if ($clientSecretEnv) {
            $this->clientSecret = $clientSecretEnv;
        } else {
            $encryptedSecret = get_setting('oidc_client_secret');
            $this->clientSecret = $encryptedSecret ? $this->CI->crypt->decode($encryptedSecret) : '';
        }

        $this->scopes = get_setting('oidc_scopes', 'openid email profile');
        $this->verifySsl = get_setting('oidc_verify_ssl', '1') == '1';
        $this->requireEmailVerified = get_setting('oidc_require_email_verified', '1') == '1';
        $this->tokenAuthMethods = get_setting('oidc_token_auth_methods', 'client_secret_post,client_secret_basic');
    }

    /**
     * Check if OIDC is enabled and properly configured
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled
            && ! empty($this->providerUrl)
            && ! empty($this->clientId)
            && ! empty($this->clientSecret);
    }

    /**
     * Check if local password login is allowed
     *
     * @return bool
     */
    public function isLocalLoginAllowed()
    {
        // If OIDC is not enabled, local login is always allowed
        if ( ! $this->isEnabled()) {
            return true;
        }

        return get_setting('oidc_allow_local_login', '1') == '1';
    }

    /**
     * Check if anonymous users should be redirected into OIDC automatically
     *
     * @return bool
     */
    public function isAutoRedirectLoginEnabled()
    {
        if ( ! $this->isEnabled()) {
            return false;
        }

        return get_setting('oidc_auto_redirect_login', '0') == '1';
    }

    /**
     * Check if auto-creation of users is enabled
     *
     * @return bool
     */
    public function isAutoCreateEnabled()
    {
        return get_setting('oidc_auto_create_users', '0') == '1';
    }

    /**
     * Get the default user type for auto-created users
     *
     * @return int 1 for admin, 2 for guest
     */
    public function getDefaultUserType()
    {
        return (int) get_setting('oidc_default_user_type', '2');
    }

    /**
     * Get the SSO button text
     *
     * @return string
     */
    public function getButtonText()
    {
        $text = get_setting('oidc_button_text');
        return ! empty($text) ? $text : trans('oidc_login');
    }

    /**
     * Initialize the OIDC client
     *
     * @return OpenIDConnectClient
     * @throws OpenIDConnectClientException
     */
    private function initClient()
    {
        if ($this->oidc === null) {
            $this->oidc = new OpenIDConnectClient(
                $this->providerUrl,
                $this->clientId,
                $this->clientSecret
            );

            // Set redirect URI
            $this->oidc->setRedirectURL(site_url('sessions/oidc_callback'));

            // Set scopes
            $scopeArray = array_filter(array_map('trim', explode(' ', $this->scopes)));
            if ( ! empty($scopeArray)) {
                $this->oidc->addScope($scopeArray);
            }

            // Token endpoint authentication methods
            $methodArray = array_filter(array_map('trim', explode(',', $this->tokenAuthMethods)));
            if (empty($methodArray)) {
                $methodArray = ['client_secret_post', 'client_secret_basic'];
            }
            $this->oidc->setTokenEndpointAuthMethodsSupported($methodArray);

            // SSL verification
            if ( ! $this->verifySsl) {
                $this->oidc->setVerifyHost(false);
                $this->oidc->setVerifyPeer(false);
            }
        }

        return $this->oidc;
    }

    /**
     * Start the OIDC authentication flow
     * Redirects the user to the identity provider
     *
     * @throws OpenIDConnectClientException
     */
    public function authenticate()
    {
        if ( ! $this->isEnabled()) {
            throw new Exception('OIDC is not enabled or not properly configured');
        }

        $oidc = $this->initClient();
        $oidc->authenticate();
    }

    /**
     * Handle the OIDC callback and get user information
     *
     * @return array User information from the identity provider
     * @throws OpenIDConnectClientException
     */
    public function handleCallback()
    {
        if ( ! $this->isEnabled()) {
            throw new Exception('OIDC is not enabled or not properly configured');
        }

        $oidc = $this->initClient();
        $oidc->authenticate();

        // Get user info
        $userInfo = $oidc->requestUserInfo();

        // Build name from available claims
        $name = null;
        if ( ! empty($userInfo->name)) {
            $name = trim($userInfo->name);
        }
        if (empty($name)) {
            $givenName = isset($userInfo->given_name) ? trim($userInfo->given_name) : '';
            $familyName = isset($userInfo->family_name) ? trim($userInfo->family_name) : '';
            $name = trim($givenName . ' ' . $familyName);
        }

        return [
            'sub' => $oidc->getVerifiedClaims('sub'),
            'email' => $userInfo->email ?? null,
            'email_verified' => $userInfo->email_verified ?? null,
            'name' => $name ?: null,
            'given_name' => $userInfo->given_name ?? null,
            'family_name' => $userInfo->family_name ?? null,
            'preferred_username' => $userInfo->preferred_username ?? null,
        ];
    }

    /**
     * Find or create a user based on OIDC claims
     *
     * SECURITY NOTE: This method links existing local accounts to OIDC identities
     * based on email address matching. This trusts the configured IdP to honestly
     * report email addresses and email_verified status. Ensure the IdP is fully
     * trusted before enabling OIDC, as a compromised or misconfigured IdP could
     * potentially claim any email and take over the corresponding local account.
     *
     * @param array $claims User claims from OIDC provider
     * @return object|null User object or null if not found and auto-create is disabled
     */
    public function findOrCreateUser($claims)
    {
        // OIDC subject is required - reject if missing or empty
        if (empty($claims['sub'])) {
            log_message('error', 'OIDC: Cannot process user without sub claim');
            return null;
        }

        $this->CI->load->model('users/mdl_users');

        // First, try to find by OIDC subject (most reliable)
        $user = $this->findUserByOidcSub($claims['sub']);

        if ($user) {
            return $user;
        }

        // Try to find by email - links existing account to OIDC identity
        // This trusts the IdP's email_verified claim (see security note above)
        if ( ! empty($claims['email'])) {
            // Check email verification - properly normalize to boolean
            // Handles true, 1, "true", "1" as verified; false, 0, "false", "0", null as unverified
            if ($this->requireEmailVerified && ! $this->isEmailVerified($claims['email_verified'] ?? null)) {
                return null;
            }

            $user = $this->findUserByEmail($claims['email']);

            if ($user) {
                // Link existing user to OIDC
                $this->linkUserToOidc($user->user_id, $claims['sub']);
                return $user;
            }
        }

        // User not found - create if auto-create is enabled
        if ($this->isAutoCreateEnabled() && ! empty($claims['email'])) {
            return $this->createUserFromClaims($claims);
        }

        return null;
    }

    /**
     * Find a user by OIDC subject ID
     *
     * @param string $sub OIDC subject identifier
     * @return object|null
     */
    private function findUserByOidcSub($sub)
    {
        return $this->CI->db
            ->where('user_oidc_sub', $sub)
            ->get('ip_users')
            ->row();
    }

    /**
     * Find a user by email address
     *
     * @param string $email
     * @return object|null
     */
    private function findUserByEmail($email)
    {
        return $this->CI->db
            ->where('user_email', $email)
            ->get('ip_users')
            ->row();
    }

    /**
     * Link an existing user to an OIDC subject
     *
     * @param int $userId
     * @param string $sub OIDC subject identifier
     */
    private function linkUserToOidc($userId, $sub)
    {
        $this->CI->db
            ->where('user_id', $userId)
            ->update('ip_users', [
                'user_oidc_sub' => $sub,
                'user_auth_provider' => 'oidc',
            ]);
    }

    /**
     * Create a new user from OIDC claims
     *
     * @param array $claims
     * @return object|null The created user or null on failure
     */
    private function createUserFromClaims($claims)
    {
        // Double-check for existing user to prevent race condition duplicates
        $existingUser = $this->findUserByEmail($claims['email']);
        if ($existingUser) {
            // Link existing user to OIDC and return
            $this->linkUserToOidc($existingUser->user_id, $claims['sub']);
            return $existingUser;
        }

        $name = trim($claims['name'] ?? '');
        if (empty($name)) {
            // Check preferred_username is non-empty before using it
            $preferredUsername = trim($claims['preferred_username'] ?? '');
            $name = ! empty($preferredUsername) ? $preferredUsername : $claims['email'];
        }

        $data = [
            'user_type' => $this->getDefaultUserType(),
            'user_active' => 1,
            'user_email' => $claims['email'],
            'user_name' => $name,
            'user_password' => null,
            'user_psalt' => null,
            'user_auth_provider' => 'oidc',
            'user_oidc_sub' => $claims['sub'],
            'user_language' => 'system',
            'user_date_created' => date('Y-m-d H:i:s'),
            'user_date_modified' => date('Y-m-d H:i:s'),
        ];

        $inserted = $this->CI->db->insert('ip_users', $data);
        if ( ! $inserted) {
            log_message('error', 'OIDC: Failed to insert user with email: ' . $claims['email']);
            return null;
        }

        $userId = $this->CI->db->insert_id();
        if ( ! $userId) {
            log_message('error', 'OIDC: Insert succeeded but no insert_id returned for email: ' . $claims['email']);
            return null;
        }

        return $this->CI->db
            ->where('user_id', $userId)
            ->get('ip_users')
            ->row();
    }

    /**
     * Check if the email_verified claim indicates a verified email
     * Properly normalizes various representations to boolean
     *
     * @param mixed $emailVerified The email_verified claim value
     * @return bool
     */
    private function isEmailVerified($emailVerified)
    {
        if ($emailVerified === null) {
            return false;
        }

        // Use filter_var to properly handle true, "true", 1, "1", etc.
        return filter_var($emailVerified, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === true;
    }

    /**
     * Set session data for an authenticated user
     *
     * @param object $user User object from database
     */
    public function setUserSession($user)
    {
        // Regenerate session ID to prevent session fixation attacks
        $this->CI->session->sess_regenerate(true);

        $sessionData = [
            'user_type' => $user->user_type,
            'user_id' => $user->user_id,
            'user_name' => $user->user_name,
            'user_email' => $user->user_email,
            'user_company' => $user->user_company ?? '',
            'user_language' => $user->user_language ?? 'system',
        ];

        $this->CI->session->set_userdata($sessionData);
    }
}
