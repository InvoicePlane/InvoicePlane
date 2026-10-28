<?php

/**
 * Seed the baseline rows into the MySQL/MariaDB test database.
 *
 * Run once by CI after the schema has been imported from the setup migrations.
 * Connection details are read from the DB_* environment variables (matching
 * ipconfig.php), falling back to the conventional CI values.
 *
 * Usage:
 *   DB_HOSTNAME=127.0.0.1 DB_DATABASE=invoiceplane_test \
 *   DB_USERNAME=root DB_PASSWORD=root php tests/Support/seed-test-db.php
 */
$host = getenv('DB_HOSTNAME') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$name = getenv('DB_DATABASE') ?: 'invoiceplane_test';
$user = getenv('DB_USERNAME') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: 'root';

$pdo = new PDO(
    sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8', $host, $port, $name),
    $user,
    $pass,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
);

require_once __DIR__ . '/seed_baseline.php';
ip_seed_baseline($pdo);

fwrite(STDOUT, "Baseline seed applied to {$name}." . PHP_EOL);
