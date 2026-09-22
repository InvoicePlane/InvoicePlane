<?php

/**
 * Canonical baseline seed for the MySQL/MariaDB test database.
 *
 * Inserts the minimal rows the application needs to boot (settings, an admin
 * user, a default invoice group) into the MariaDB test database. Shared by the
 * CI schema-build step and
 * by the per-test reset in tests/Concerns/InteractsWithDatabase.php so the two
 * never drift.
 */
function ip_seed_baseline(PDO $pdo): void
{
    $settings = [
        'default_language'          => 'english',
        'currency_symbol'           => '$',
        'currency_symbol_placement' => 'before',
        'date_format'               => 'Y-m-d',
        'time_format'               => 'H:i',
        'pdf_engine'                => 'pdfmake',
        'pdf_paper_size'            => 'a4',
        'pdf_paper_orientation'     => 'portrait',
        'read_only_toggle'          => 'paid',
        'next_invoice_number'       => '1',
        'next_quote_number'         => '1',
        'invoicenumber_prefix'      => '',
        'quotenumber_prefix'        => '',
        'disable_setup'             => '1',
        'sumex'                     => '0',
    ];

    $stmt = $pdo->prepare('INSERT INTO `ip_settings` (`setting_key`, `setting_value`) VALUES (?, ?)');
    foreach ($settings as $key => $value) {
        $stmt->execute([$key, $value]);
    }

    $now = date('Y-m-d H:i:s');

    // user_psalt must be non-empty: Mdl_Sessions::auth() treats a blank psalt as
    // a pre-1.2.0 account and only ever compares the password as a raw md5, so a
    // bcrypt user_password with no psalt can never log in through the real
    // browser login form (the PHPUnit suite sidesteps this by setting the
    // session directly in actingAsAdmin()). The per-test user builder in
    // InteractsWithDatabase already seeds a psalt for the same reason.
    $pdo->prepare(
        'INSERT INTO `ip_users` (`user_name`, `user_email`, `user_password`, `user_psalt`, `user_type`, `user_active`, `user_date_created`, `user_date_modified`)'
        . ' VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
    )->execute(['Admin', 'admin@test.local', password_hash('password', PASSWORD_DEFAULT), bin2hex(random_bytes(10)), 1, 1, $now, $now]);

    $pdo->prepare(
        'INSERT INTO `ip_invoice_groups`'
        . ' (`invoice_group_name`, `invoice_group_identifier_format`, `invoice_group_next_id`, `invoice_group_left_pad`)'
        . ' VALUES (?, ?, ?, ?)'
    )->execute(['Default', '{{{id}}}', 1, 0]);
}
