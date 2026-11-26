<?php

/**
 * A throw-away HTTPS server that records which requests reach it, for tests that need to observe what
 * libcurl actually does (TLS verification, redirects) instead of reading option names in the source.
 *
 * Arguments: <pem with certificate and key> <log file>.
 * Listens on two ephemeral 127.0.0.1 ports and prints "TLS:<port> PLAIN:<port>" as its first line.
 *
 *   TLS    answers "POST /ok"            with 200 "OK-PDF"
 *                  "/redirect"           with 302 -> https://127.0.0.1:<tls>/followed
 *                  "/redirect-plain"     with 302 -> http://127.0.0.1:<plain>/plain-target
 *   PLAIN  accepts a connection and records it (nothing is ever answered)
 *
 * Every request line that arrives is appended to the log file as "TLS <METHOD> <path>" or "PLAIN".
 * The process runs for at most 30 seconds; the test normally terminates it first.
 */
[$script, $pem, $logFile] = $argv + [null, null, null];

$context = stream_context_create(['ssl' => [
    'local_cert'  => $pem,
    'verify_peer' => false,
]]);

$tls = stream_socket_server('tls://127.0.0.1:0', $errno, $errstr, STREAM_SERVER_BIND | STREAM_SERVER_LISTEN, $context);
$raw = stream_socket_server('tcp://127.0.0.1:0', $errno, $errstr);

if ($tls === false || $raw === false) {
    fwrite(STDERR, "cannot listen: {$errstr}\n");
    exit(1);
}

$port      = static fn ($socket): int => (int) substr((string) strrchr((string) stream_socket_get_name($socket, false), ':'), 1);
$tlsPort   = $port($tls);
$plainPort = $port($raw);

echo "TLS:{$tlsPort} PLAIN:{$plainPort}\n";
flush();

$log = static function (string $line) use ($logFile): void {
    file_put_contents($logFile, $line . "\n", FILE_APPEND | LOCK_EX);
};

$deadline = time() + 30;

while (time() < $deadline) {
    $read  = [$tls, $raw];
    $write = $except = null;

    if (stream_select($read, $write, $except, 1) < 1) {
        continue;
    }

    foreach ($read as $listener) {
        if ($listener === $raw) {
            $conn = @stream_socket_accept($raw, 1);
            if ($conn !== false) {
                $log('PLAIN');
                fclose($conn);
            }

            continue;
        }

        $conn = @stream_socket_accept($tls, 1);
        if ($conn === false) {
            continue;
        }

        stream_set_timeout($conn, 2);
        $head = '';
        while ( ! str_contains($head, "\r\n\r\n") && ($chunk = fread($conn, 8192)) !== false && $chunk !== '') {
            $head .= $chunk;
        }

        if ($head === '') {
            fclose($conn);

            continue;
        }

        [$method, $path] = array_pad(explode(' ', (string) strtok($head, "\r\n")), 2, '');
        $log("TLS {$method} {$path}");

        if ($path === '/redirect') {
            $response = "HTTP/1.1 302 Found\r\nLocation: https://127.0.0.1:{$tlsPort}/followed\r\nContent-Length: 0\r\nConnection: close\r\n\r\n";
        } elseif ($path === '/redirect-plain') {
            $response = "HTTP/1.1 302 Found\r\nLocation: http://127.0.0.1:{$plainPort}/plain-target\r\nContent-Length: 0\r\nConnection: close\r\n\r\n";
        } else {
            $response = "HTTP/1.1 200 OK\r\nContent-Length: 6\r\nConnection: close\r\n\r\nOK-PDF";
        }

        fwrite($conn, $response);
        fclose($conn);
    }
}
