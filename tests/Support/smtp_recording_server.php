<?php

/**
 * A throw-away SMTP server that records the envelope of every message it accepts, for tests that need to
 * observe who a mail was actually addressed to instead of reading the controller source.
 *
 * Arguments: <log file>.
 * Listens on one ephemeral 127.0.0.1 port and prints "PORT:<port>" as its first line.
 *
 * Each "MAIL FROM:" and "RCPT TO:" command is appended to the log file as "FROM <address>" / "TO <address>".
 * The process runs for at most 30 seconds; the test normally terminates it first.
 */
[$script, $logFile] = $argv + [null, null];

$server = stream_socket_server('tcp://127.0.0.1:0', $errno, $errstr);

if ($server === false) {
    fwrite(STDERR, "cannot listen: {$errstr}\n");
    exit(1);
}

$name = (string) stream_socket_get_name($server, false);
echo 'PORT:' . substr($name, (int) strrpos($name, ':') + 1) . "\n";
flush();

$log = static function (string $line) use ($logFile): void {
    file_put_contents($logFile, $line . "\n", FILE_APPEND | LOCK_EX);
};

$deadline = time() + 30;

while (time() < $deadline) {
    $conn = @stream_socket_accept($server, 1);

    if ($conn === false) {
        continue;
    }

    fwrite($conn, "220 recorder ESMTP\r\n");
    $inData = false;

    while (($line = fgets($conn)) !== false) {
        $line = rtrim($line, "\r\n");

        if ($inData) {
            if ($line === '.') {
                $inData = false;
                fwrite($conn, "250 queued\r\n");
            }

            continue;
        }

        $command = strtoupper(substr($line, 0, 4));

        if ($command === 'EHLO' || $command === 'HELO') {
            fwrite($conn, "250 recorder\r\n");
        } elseif ($command === 'MAIL') {
            $log('FROM ' . trim((string) preg_replace('/^MAIL FROM:\s*<?([^>\s]*).*$/i', '$1', $line)));
            fwrite($conn, "250 ok\r\n");
        } elseif ($command === 'RCPT') {
            $log('TO ' . trim((string) preg_replace('/^RCPT TO:\s*<?([^>\s]*).*$/i', '$1', $line)));
            fwrite($conn, "250 ok\r\n");
        } elseif ($command === 'DATA') {
            $inData = true;
            fwrite($conn, "354 go ahead\r\n");
        } elseif ($command === 'QUIT') {
            fwrite($conn, "221 bye\r\n");
            break;
        } else {
            fwrite($conn, "250 ok\r\n");
        }
    }

    fclose($conn);
}
