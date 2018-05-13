<?php

/*
| -------------------------------------------------------------------
| Aauth Config
| -------------------------------------------------------------------
| A library Basic Authorization for CodeIgniter 2.x and 3.x
|
| -------------------------------------------------------------------
| EXPLANATION
| -------------------------------------------------------------------
|
|   ['no_permission']                   If user don't have permisssion to see the page he will be redirected
|                                       the page specified.
|
|   ['admin_group']                     Name of admin group
|   ['default_group']                   Name of default group, the new user is added in it
|   ['public_group']                    Name of Public group , people who not logged in
|
|   ['db_profile']                      The configuration database profile (definied in config/database.php)
|
|   ['users']                           The table which contains users
|   ['groups']                          The table which contains groups
|   ['user_to_group']                   The table which contains join of users and groups
|   ['perms']                           The table which contains permissions
|   ['perm_to_group']                   The table which contains permissions for groups
|   ['perm_to_user']                    The table which contains permissions for users
|   ['pms']                             The table which contains private messages
|   ['user_variables']                  The table which contains users variables
|   ['login_attempts']                  The table which contains login attempts
|
|   ['remember']                        Remember time (in relative format) elapsed after connecting and automatic
|                                       LogOut for usage with Cookies.
|                                       Relative Format (e.g. '+ 1 week', '+ 1 month', '+ first day of next month')
|                                       for details see http://php.net/manual/de/datetime.formats.relative.php
|
|   ['max']                             Maximum char long for Password
|   ['min']                             Minimum char long for Password
|
|   ['additional_valid_chars']          Additional valid chars for username. Non alphanumeric characters that
|                                       are allowed by default
|
|   ['ddos_protection']                 Enables the DDoS Protection, user will be banned temporary when he
                                       exceed the login 'try'
|
|   ['recaptcha_active']                Enables reCAPTCHA (for details see www.google.com/recaptcha/admin)
|   ['recaptcha_login_attempts']        Login Attempts to display reCAPTCHA
|   ['recaptcha_siteKey']               The reCAPTCHA siteKey
|   ['recaptcha_secret']                The reCAPTCHA secretKey
|
|   ['totp_active']                     Enables the Time-based One-time Password Algorithm
|   ['totp_only_on_ip_change']          TOTP only on IP Change
|   ['totp_reset_over_reset_password']  TOTP reset over reset Password
|   ['totp_two_step_login']             Enables TOTP two step login
|   ['totp_two_step_login_redirect']    Redirect path to TOTP Verification page used by control() & is_allowed()
|
|   ['max_login_attempt']               Login attempts time interval (default 10 times in one hour)
|   ['max_login_attempt_time_period']   Period of time for max login attempts (default "5 minutes")
|   ['remove_successful_attempts']      Enables removing login attempt after successful login
|
|   ['login_with_name']                 Login Identificator, if TRUE username needed to login else email address.
|
|   ['email']                           Sender email address, used for remind_password, send_verification
                                       and reset_password
|   ['name']                            Sender name, used for remind_password, send_verification and reset_password
|   ['email_config']                    Array of Config for CI's Email Library
|
|   ['verification']                    User Verification, if TRUE sends a verification email on account creation.
|   ['verification_link']               Link for verification without site_url or base_url
|   ['reset_password_link']             Link for reset_password without site_url or base_url
|
|   ['hash']                            Name of selected hashing algorithm (e.g. "md5", "sha256", "haval160,4", etc..)
|                                       Please, run hash_algos() for know your all supported algorithms
|   ['use_password_hash']               Enables to use PHP's own password_hash() function with BCrypt
|   ['password_hash_algo']              password_hash algorithm (PASSWORD_DEFAULT, PASSWORD_BCRYPT)
|                                       for details see http://php.net/manual/de/password.constants.php
|   ['password_hash_options']           password_hash options array
|                                       for details see http://php.net/manual/en/function.password-hash.php
|
|   ['pm_encryption']                   Enables PM Encryption, needs configured CI Encryption Class.
|                                       for details see: http://www.codeigniter.com/userguide2/libraries/encryption.html
|   ['pm_cleanup_max_age']              PM Cleanup max age (in relative format), PM's are older than max age get
|                                       deleted with 'cleanup_pms()'
|                                       Relative Format (e.g. '2 week', '1 month')
|                                       for details see http://php.net/manual/de/datetime.formats.relative.php
|
*/

$config_aauth = [];

$config_aauth['default'] = [
    'no_permission' => false,

    'admin_group' => 'admin',
    'default_group' => 'default',
    'public_group' => 'public',

    'db_profile' => 'default',

    'users' => 'users',
    'groups' => 'groups',
    'group_to_group' => 'subgroup_groups',
    'user_to_group' => 'user_groups',
    'perms' => 'permissions',
    'perm_to_group' => 'group_permissions',
    'perm_to_user' => 'user_permissions',
    'pms' => 'pms',
    'user_variables' => 'user_variables',
    'login_attempts' => 'login_attempts',

    'remember' => ' +5 days',

    'max' => 255,
    'min' => 5,

    'additional_valid_chars' => [],

    'ddos_protection' => true,

    'recaptcha_active' => false,
    'recaptcha_login_attempts' => 4,
    'recaptcha_siteKey' => '',
    'recaptcha_secret' => '',

    'totp_active' => false,
    'totp_only_on_ip_change' => false,
    'totp_reset_over_reset_password' => false,
    'totp_two_step_login_active' => false,
    'totp_two_step_login_redirect' => '/account/twofactor_verification/',

    'max_login_attempt' => 5,
    'max_login_attempt_time_period' => '5 minutes',
    'remove_successful_attempts' => true,

    'login_with_name' => false,

    'email' => 'admin@admin.com',
    'name' => 'Emre Akay',
    'email_config' => false,

    'verification' => false,
    'verification_link' => '/account/verification/',
    'reset_password_link' => '/account/reset_password/',

    'hash' => 'sha256',
    'use_password_hash' => true,
    'password_hash_algo' => PASSWORD_BCRYPT,
    'password_hash_options' => [],

    'pm_encryption' => false,
    'pm_cleanup_max_age' => "3 months",
];

$config['aauth'] = $config_aauth['default'];
