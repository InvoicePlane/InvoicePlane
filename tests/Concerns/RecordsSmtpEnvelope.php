<?php

namespace Tests\Concerns;

/**
 * Sends mail through a real SMTP conversation with a local recording server
 * (tests/Support/smtp_recording_server.php) so a test can assert on the envelope that actually went
 * out instead of reading controller source or re-reading database rows.
 *
 * Call stopSmtpRecorder() from tearDown().
 */
trait RecordsSmtpEnvelope
{
    /** @var resource|null */
    private $smtpServer;

    private string $smtpLogFile = '';

    /**
     * Starts the recorder and points the mailer settings at it.
     *
     * @param array<string, string> $settings additional or overriding ip_settings values
     */
    protected function startSmtpRecorder(array $settings = []): void
    {
        $this->smtpLogFile = sys_get_temp_dir() . '/smtp-envelope-' . bin2hex(random_bytes(6)) . '.log';
        $pipes             = [];
        $this->smtpServer  = proc_open(
            [PHP_BINARY, dirname(__DIR__) . '/Support/smtp_recording_server.php', $this->smtpLogFile],
            [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes
        );
        self::assertIsResource($this->smtpServer, 'The recording SMTP server must start.');
        $banner = (string) fgets($pipes[1]);
        self::assertMatchesRegularExpression('/^PORT:\d+/', $banner, 'The recording SMTP server must report its port.');

        foreach ($settings + [
            'email_send_method'    => 'smtp',
            'smtp_server_address'  => '127.0.0.1',
            'smtp_port'            => (string) (int) substr(trim($banner), 5),
            'smtp_security'        => '',
            'smtp_authentication'  => '0',
            'smtp_mail_from'       => 'billing@company.test',
            'email_pdf_attachment' => '0',
        ] as $key => $value) {
            $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => $key, 'setting_value' => '']);
            $this->databaseUpdate('ip_settings', ['setting_value' => $value], ['setting_key' => $key]);
        }
    }

    protected function stopSmtpRecorder(): void
    {
        if (is_resource($this->smtpServer)) {
            proc_terminate($this->smtpServer);
            proc_close($this->smtpServer);
        }

        if ($this->smtpLogFile !== '' && is_file($this->smtpLogFile)) {
            unlink($this->smtpLogFile);
        }
    }

    /**
     * @return array{to: list<string>, from: list<string>}
     */
    protected function recordedEnvelope(): array
    {
        $to   = [];
        $from = [];

        foreach (is_file($this->smtpLogFile) ? (file($this->smtpLogFile, FILE_IGNORE_NEW_LINES) ?: []) : [] as $line) {
            [$kind, $address] = explode(' ', $line, 2) + [1 => ''];
            if ($kind === 'TO') {
                $to[] = $address;
            } elseif ($kind === 'FROM') {
                $from[] = $address;
            }
        }

        return ['to' => $to, 'from' => $from];
    }
}
