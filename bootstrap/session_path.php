<?php

if ( ! function_exists('resolve_session_save_path')) {
    /**
     * Normalise a configured session save path so an empty, whitespace-only or
     * unset value behaves exactly like "not configured".
     *
     * A bare `SESS_SAVE_PATH=` line in ipconfig.php is parsed by phpdotenv as a
     * *defined* variable whose value is "" (not unset). env('SESS_SAVE_PATH', …)
     * then returns "" and never applies its default, so config.php would set
     * $config['sess_save_path'] = "". CodeIgniter's Session_files_driver treats a
     * set-but-empty save path as configured, runs ini_set('session.save_path', '')
     * — clobbering the php.ini / php-fpm.d / vhost value — and session open then
     * does mkdir('') and fails. User-visible result: login fails and the manual
     * installer is stuck forever on setup/language.
     *
     * Collapsing ""/"   "/null to the fallback restores the intended
     * "leave it empty to use PHP's default" behaviour. A genuine explicit path is
     * returned unchanged apart from trailing-slash trimming (matching what
     * Session_files_driver does with it anyway).
     *
     * @param string|null $configured raw value, typically env('SESS_SAVE_PATH')
     * @param string|null $fallback   directory to use when $configured is blank;
     *                                defaults to sys_get_temp_dir()
     */
    function resolve_session_save_path(?string $configured, ?string $fallback = null): string
    {
        if ($fallback === null || trim($fallback) === '') {
            $fallback = sys_get_temp_dir();
        }

        $fallback = rtrim($fallback, '/\\');

        if ($configured === null || trim($configured) === '') {
            return $fallback;
        }

        $trimmed = rtrim(trim($configured), '/\\');

        return $trimmed === '' ? $fallback : $trimmed;
    }
}
