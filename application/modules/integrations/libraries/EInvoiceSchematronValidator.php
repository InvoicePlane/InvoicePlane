<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Executes versioned EN 16931 and Peppol Schematron rules with Saxon-HE.
 */
final class EInvoiceSchematronValidator
{
    private const ENGINE_DIRECTORY = __DIR__ . '/../resources/validation/schematron/engine/';

    private const RULE_DIRECTORY = __DIR__ . '/../resources/validation/schematron/rules/';

    private const PROCESS_TIMEOUT_SECONDS = 30;

    public function __construct(private string $javaBinary = 'java') {}

    /**
     * @return string[]
     */
    public function validate(string $path, EInvoiceProfile $profile): array
    {
        if ($profile->syntax() !== 'ubl') {
            return [];
        }

        $rules = [
            'EN 16931 1.3.16' => 'EN16931-UBL-validation-1.3.16.xslt',
        ];

        if ($profile->format() === 'peppol-bis-billing-3') {
            $rules['Peppol BIS Billing 3.0.21'] = 'PEPPOL-EN16931-UBL-3.0.21.xslt';
        }

        $errors = [];
        foreach ($rules as $ruleName => $ruleFile) {
            $errors = array_merge($errors, $this->validateWithRule($path, $ruleName, $ruleFile));
        }

        return array_values(array_unique($errors));
    }

    /**
     * @return string[]
     */
    private function validateWithRule(string $path, string $ruleName, string $ruleFile): array
    {
        $saxonPath    = realpath(self::ENGINE_DIRECTORY . 'saxon-he-12.10.jar');
        $resolverPath = realpath(self::ENGINE_DIRECTORY . 'xmlresolver-5.3.3.jar');
        $rulePath     = realpath(self::RULE_DIRECTORY . $ruleFile);

        if ($saxonPath === false || $resolverPath === false || $rulePath === false) {
            return [$ruleName . ': required validation artifacts are unavailable.'];
        }

        if ( ! function_exists('proc_open')) {
            return [$ruleName . ': the PHP proc_open function is required for Schematron validation.'];
        }

        $outputPath = tempnam(sys_get_temp_dir(), 'ip-svrl-');
        if ($outputPath === false) {
            return [$ruleName . ': unable to create the validation result file.'];
        }

        $classPath = $saxonPath . PATH_SEPARATOR . $resolverPath;
        $command   = [
            $this->javaBinary,
            '-Djavax.xml.accessExternalDTD=',
            '-Djavax.xml.accessExternalSchema=',
            '-cp',
            $classPath,
            'net.sf.saxon.Transform',
            '-dtd:off',
            '-ext:off',
            '-warnings:silent',
            '-s:' . $path,
            '-xsl:' . $rulePath,
            '-o:' . $outputPath,
        ];

        try {
            $result = $this->run($command);
            if ($result['timed_out']) {
                return [$ruleName . ': validation timed out.'];
            }

            if ($result['exit_code'] !== 0) {
                return [$ruleName . ': the Schematron engine failed.'];
            }

            return $this->readSvrl($outputPath, $ruleName);
        } finally {
            if (is_file($outputPath)) {
                unlink($outputPath);
            }
        }
    }

    /**
     * @param string[] $command
     *
     * @return array{exit_code: int, timed_out: bool}
     */
    private function run(array $command): array
    {
        $descriptorSpec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];
        $pipes   = [];
        $process = @proc_open($command, $descriptorSpec, $pipes, null, null, ['bypass_shell' => true]);

        if ( ! is_resource($process)) {
            return ['exit_code' => 1, 'timed_out' => false];
        }

        fclose($pipes[0]);
        stream_set_blocking($pipes[1], false);
        stream_set_blocking($pipes[2], false);

        $deadline = microtime(true) + self::PROCESS_TIMEOUT_SECONDS;
        $timedOut = false;
        $exitCode = -1;

        do {
            // Drain both streams so the child process cannot block on a full pipe.
            stream_get_contents($pipes[1]);
            stream_get_contents($pipes[2]);

            $status = proc_get_status($process);
            if ( ! $status['running']) {
                $exitCode = $status['exitcode'];
                break;
            }

            if (microtime(true) >= $deadline) {
                $timedOut = true;
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

        return ['exit_code' => $exitCode, 'timed_out' => $timedOut];
    }

    /**
     * Return blocking assertions. Schematron warnings remain non-blocking.
     *
     * @return string[]
     */
    private function readSvrl(string $path, string $ruleName): array
    {
        $previous = libxml_use_internal_errors(true);
        libxml_clear_errors();

        $document                     = new DOMDocument();
        $document->resolveExternals   = false;
        $document->substituteEntities = false;
        $loaded                       = $document->load($path, LIBXML_NONET);

        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if ( ! $loaded) {
            return [$ruleName . ': the Schematron result is not valid XML.'];
        }

        $xpath = new DOMXPath($document);
        $xpath->registerNamespace('svrl', 'http://purl.oclc.org/dsdl/svrl');
        $assertions = $xpath->query('//svrl:failed-assert');

        if ($assertions === false) {
            return [$ruleName . ': unable to read the Schematron result.'];
        }

        $errors = [];
        foreach ($assertions as $assertion) {
            $flag = mb_strtolower(trim($assertion->attributes?->getNamedItem('flag')?->nodeValue ?? 'fatal'));
            if (in_array($flag, ['warning', 'info'], true)) {
                continue;
            }

            $id       = trim($assertion->attributes?->getNamedItem('id')?->nodeValue ?? 'rule');
            $textNode = $xpath->query('svrl:text', $assertion)?->item(0);
            $message  = preg_replace('/\s+/', ' ', trim($textNode?->textContent ?? 'Rule failed.'));
            $errors[] = $ruleName . ' [' . $id . ']: ' . $message;
        }

        return $errors;
    }
}
