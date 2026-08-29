<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Validates and archives provider-downloaded incoming invoice documents.
 */
final class IncomingInvoiceDocumentService
{
    private const MAX_DOCUMENT_BYTES = 15 * 1024 * 1024;

    public function __construct(
        private ?EInvoiceProfileRegistry $profiles = null,
        private ?EInvoiceDocumentValidator $validator = null,
        private string $pdfDetachBinary = 'pdfdetach'
    ) {
        $this->profiles ??= EInvoiceProfileRegistry::builtIn();
        $this->validator ??= new EInvoiceDocumentValidator();
    }

    public function archive(
        string $providerCode,
        array $invoice,
        array $download,
        string $archiveDirectory
    ): array {
        if (empty($download['success'])) {
            throw new RuntimeException($download['message'] ?? 'Provider document download failed.');
        }

        $content = $download['content'] ?? null;
        if ( ! is_string($content) || $content === '') {
            throw new RuntimeException('Provider returned an empty incoming invoice document.');
        }

        $size = strlen($content);
        if ($size > self::MAX_DOCUMENT_BYTES) {
            throw new RuntimeException('Incoming invoice document exceeds the 15 MB limit.');
        }

        if (preg_match('/^[a-z][a-z0-9_-]*$/', $providerCode) !== 1) {
            throw new InvalidArgumentException('Invalid incoming invoice provider code.');
        }

        $temporaryPath = tempnam(sys_get_temp_dir(), 'ip-incoming-');
        if ($temporaryPath === false || file_put_contents($temporaryPath, $content, LOCK_EX) !== $size) {
            throw new RuntimeException('Unable to stage the incoming invoice document.');
        }

        $extractedPath = null;

        try {
            [$extension, $mimeType, $profile] = $this->validateDocument(
                $temporaryPath,
                $content,
                $providerCode,
                $invoice,
                $extractedPath
            );

            $hash              = hash('sha256', $content);
            $providerDirectory = rtrim($archiveDirectory, DIRECTORY_SEPARATOR)
                . DIRECTORY_SEPARATOR . 'incoming'
                . DIRECTORY_SEPARATOR . $providerCode;

            if ( ! is_dir($providerDirectory)
                && ! mkdir($providerDirectory, 0750, true)
                && ! is_dir($providerDirectory)) {
                throw new RuntimeException('Unable to create the incoming invoice archive directory.');
            }

            $storedName = $hash . '.' . $extension;
            $storedPath = $providerDirectory . DIRECTORY_SEPARATOR . $storedName;

            if ( ! is_file($storedPath)) {
                $archiveTemporaryPath = tempnam($providerDirectory, '.incoming-');
                if ($archiveTemporaryPath === false
                    || ! copy($temporaryPath, $archiveTemporaryPath)
                    || ! chmod($archiveTemporaryPath, 0640)
                    || ! rename($archiveTemporaryPath, $storedPath)) {
                    if (is_string($archiveTemporaryPath) && is_file($archiveTemporaryPath)) {
                        unlink($archiveTemporaryPath);
                    }

                    throw new RuntimeException('Unable to archive the incoming invoice document.');
                }
            }

            return [
                'document_path'              => 'incoming/' . $providerCode . '/' . $storedName,
                'document_name'              => $this->safeFilename($download['filename'] ?? null, $extension),
                'document_mime_type'         => $mimeType,
                'document_size'              => $size,
                'document_sha256'            => $hash,
                'document_profile'           => $profile->code(),
                'document_validation_status' => 'valid',
                'document_validation_error'  => null,
            ];
        } finally {
            if (is_file($temporaryPath)) {
                unlink($temporaryPath);
            }
            if ($extractedPath !== null && is_file($extractedPath)) {
                unlink($extractedPath);
            }
        }
    }

    /**
     * @param string|null $extractedPath
     *
     * @return array{string, string, EInvoiceProfile}
     */
    private function validateDocument(
        string $path,
        string $content,
        string $providerCode,
        array $invoice,
        ?string &$extractedPath
    ): array {
        if (str_starts_with($content, '%PDF-')) {
            $profile = $this->profiles->get('Facturxv10');
            if ( ! $profile->supportsProvider($providerCode)) {
                throw new RuntimeException('Provider is not enabled for incoming Factur-X documents.');
            }

            $extractedPath = $this->extractFacturX($path);
            $this->assertValid($this->validator->validateStructuredData($extractedPath, $profile));

            return ['pdf', 'application/pdf', $profile];
        }

        $profile  = $this->detectXmlProfile($path, $providerCode, $invoice);
        $artifact = $this->validator->validate($path, $profile);
        $this->assertValid($artifact->validationErrors());

        return ['xml', 'application/xml', $profile];
    }

    private function detectXmlProfile(string $path, string $providerCode, array $invoice): EInvoiceProfile
    {
        $document                     = new DOMDocument();
        $document->resolveExternals   = false;
        $document->substituteEntities = false;

        $previous = libxml_use_internal_errors(true);
        $loaded   = $document->load($path, LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if ( ! $loaded || $document->documentElement === null) {
            throw new RuntimeException('Incoming invoice is neither a PDF nor well-formed XML.');
        }

        $declaredCode = $invoice['profile'] ?? $invoice['profile_code'] ?? null;
        if (is_string($declaredCode) && $this->profiles->has($declaredCode)) {
            $declared = $this->profiles->get($declaredCode);
            if ($declared->extension() === 'xml' && $declared->supportsProvider($providerCode)) {
                return $declared;
            }
        }

        $xpath      = new DOMXPath($document);
        $nodes      = $xpath->query('//*[local-name()="CustomizationID"] | //*[local-name()="GuidelineSpecifiedDocumentContextParameter"]/*[local-name()="ID"]');
        $identifier = $nodes !== false && $nodes->length > 0
            ? trim($nodes->item(0)->textContent)
            : '';

        foreach ($this->profiles->all() as $profile) {
            if ($profile->extension() !== 'xml' || ! $profile->supportsProvider($providerCode)) {
                continue;
            }

            if ($profile->customizationId() !== null && hash_equals($profile->customizationId(), $identifier)) {
                return $profile;
            }
        }

        throw new RuntimeException('Incoming XML invoice profile is unsupported or cannot be identified.');
    }

    private function extractFacturX(string $pdfPath): string
    {
        if ( ! function_exists('proc_open')) {
            throw new RuntimeException('Factur-X reception requires PHP proc_open.');
        }

        $outputPath = tempnam(sys_get_temp_dir(), 'ip-facturx-');
        if ($outputPath === false) {
            throw new RuntimeException('Unable to create a temporary Factur-X XML path.');
        }
        unlink($outputPath);

        $command = [
            $this->pdfDetachBinary,
            '-savefile',
            'factur-x.xml',
            '-o',
            $outputPath,
            $pdfPath,
        ];
        $descriptorSpec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];
        $pipes   = [];
        $process = @proc_open($command, $descriptorSpec, $pipes, null, null, ['bypass_shell' => true]);

        if ( ! is_resource($process)) {
            throw new RuntimeException('Unable to start the Factur-X attachment extractor.');
        }

        fclose($pipes[0]);
        stream_set_blocking($pipes[1], false);
        stream_set_blocking($pipes[2], false);
        $deadline = microtime(true) + 30;
        $exitCode = -1;

        do {
            stream_get_contents($pipes[1]);
            stream_get_contents($pipes[2]);

            $status = proc_get_status($process);
            if ( ! $status['running']) {
                $exitCode = $status['exitcode'];
                break;
            }

            if (microtime(true) >= $deadline) {
                proc_terminate($process);
                break;
            }

            usleep(10000);
        } while (true);

        fclose($pipes[1]);
        fclose($pipes[2]);
        $closeCode = proc_close($process);

        if ($exitCode < 0 && $closeCode >= 0) {
            $exitCode = $closeCode;
        }

        if ($exitCode !== 0 || ! is_file($outputPath) || filesize($outputPath) === 0) {
            if (is_file($outputPath)) {
                unlink($outputPath);
            }
            throw new RuntimeException('Factur-X PDF does not expose a readable factur-x.xml attachment.');
        }

        return $outputPath;
    }

    /**
     * @param string[] $errors
     */
    private function assertValid(array $errors): void
    {
        if ($errors !== []) {
            throw new RuntimeException(implode(' ', array_slice($errors, 0, 3)));
        }
    }

    private function safeFilename(mixed $filename, string $extension): string
    {
        $filename = is_string($filename) ? basename($filename) : '';
        $filename = preg_replace('/[^\pL\pN._ -]+/u', '_', $filename) ?? '';
        $filename = trim($filename, " ._\t\n\r\0\x0B");

        if ($filename === '') {
            return 'incoming-invoice.' . $extension;
        }

        if (mb_strtolower(pathinfo($filename, PATHINFO_EXTENSION)) !== $extension) {
            $filename .= '.' . $extension;
        }

        return mb_substr($filename, 0, 255);
    }
}
