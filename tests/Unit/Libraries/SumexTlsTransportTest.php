<?php

namespace Tests\Unit\Libraries;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * GHSA-pqjr-7x5w-gj3r / GHSA-qmh5-hgvc-6p6c: what libcurl does inside Sumex::pdf() once the https:// guard has
 * passed. SumexRemoteUrlGuardTest covers the scheme check; the cURL options that follow it (peer and host
 * verification, no redirect following) have no visible effect on a refused URL, so here the real class posts to
 * a local TLS server (tests/Support/tls_recording_server.php) with a throw-away CA, and the test reads which
 * requests the server saw.
 *
 * Not observable from here, and why: CURLOPT_PROTOCOLS only restricts the first URL, which the scheme guard has
 * already limited to https, and CURLOPT_REDIR_PROTOCOLS only matters once redirects are followed, which
 * CURLOPT_FOLLOWLOCATION=false already prevents. Each is a second lock on a door that is shut; the redirect
 * tests below pin the combined guarantee (no second request, in particular none to a plain-http target).
 */
final class SumexTlsTransportTest extends TestCase
{
    private string $dir = '';

    /** @var resource|null */
    private $server;

    private int $tlsPort = 0;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . '/sumex-tls-' . bin2hex(random_bytes(6));
        mkdir($this->dir, 0700, true);
    }

    protected function tearDown(): void
    {
        if (is_resource($this->server)) {
            proc_terminate($this->server);
            proc_close($this->server);
        }

        foreach (glob($this->dir . '/*') ?: [] as $file) {
            unlink($file);
        }
        if (is_dir($this->dir)) {
            rmdir($this->dir);
        }
    }

    #[Test]
    public function it_posts_the_invoice_xml_to_a_server_whose_certificate_chains_to_a_trusted_ca_and_matches_the_host(): void
    {
        /* Arrange: positive control, so the refusals below can only be about verification */
        $this->startServer('IP:127.0.0.1');

        /* Act */
        $output = $this->probe('/ok', trustCa: true);

        /* Assert */
        self::assertStringContainsString("RESULT:'OK-PDF'", $output);
        self::assertSame(['TLS POST /ok'], $this->requestsSeen());
    }

    #[Test]
    public function it_refuses_a_server_whose_certificate_does_not_chain_to_a_trusted_ca(): void
    {
        /* Arrange: same server and certificate, but the CA is not trusted */
        $this->startServer('IP:127.0.0.1');

        /* Act */
        $output = $this->probe('/ok', trustCa: false);

        /* Assert: the handshake fails before any request is sent */
        self::assertStringContainsString('RESULT:false', $output);
        self::assertSame([], $this->requestsSeen());
    }

    #[Test]
    public function it_refuses_a_server_whose_trusted_certificate_is_for_a_different_host(): void
    {
        /* Arrange: the CA is trusted, but the certificate only names "localhost", not 127.0.0.1 */
        $this->startServer('DNS:localhost');

        /* Act */
        $output = $this->probe('/ok', trustCa: true);

        /* Assert */
        self::assertStringContainsString('RESULT:false', $output);
        self::assertSame([], $this->requestsSeen());
    }

    #[Test]
    public function it_does_not_follow_a_redirect_from_the_sumex_endpoint(): void
    {
        /* Arrange: the endpoint answers 302 to another https path on the same server */
        $this->startServer('IP:127.0.0.1');

        /* Act */
        $output = $this->probe('/redirect', trustCa: true);

        /* Assert: one request, and the redirect target was never fetched */
        self::assertStringContainsString("RESULT:''", $output);
        self::assertSame(['TLS POST /redirect'], $this->requestsSeen());
    }

    #[Test]
    public function it_never_reaches_a_plain_http_target_named_by_a_redirect(): void
    {
        /* Arrange: the endpoint answers 302 to http://127.0.0.1:<plain>/, the classic SSRF pivot */
        $this->startServer('IP:127.0.0.1');

        /* Act */
        $output = $this->probe('/redirect-plain', trustCa: true);
        usleep(300_000);

        /* Assert */
        self::assertStringContainsString("RESULT:''", $output);
        self::assertSame(['TLS POST /redirect-plain'], $this->requestsSeen(), 'Nothing may connect to the plain-http listener.');
    }

    private function startServer(string $subjectAltName): void
    {
        $config = $this->dir . '/openssl.cnf';
        file_put_contents($config, "[req]\ndistinguished_name=dn\n[dn]\n[v3_ca]\nbasicConstraints=critical,CA:TRUE\nkeyUsage=critical,keyCertSign\n[v3_leaf]\nbasicConstraints=CA:FALSE\nsubjectAltName={$subjectAltName}\n");
        $options = ['config' => $config, 'digest_alg' => 'sha256'];

        $caKey  = openssl_pkey_new(['private_key_bits' => 2048, 'config' => $config]);
        $caCert = openssl_csr_sign(openssl_csr_new(['commonName' => 'Sumex Test CA'], $caKey, $options), null, $caKey, 1, $options + ['x509_extensions' => 'v3_ca']);

        $leafKey  = openssl_pkey_new(['private_key_bits' => 2048, 'config' => $config]);
        $leafCert = openssl_csr_sign(openssl_csr_new(['commonName' => 'sumex.test'], $leafKey, $options), $caCert, $caKey, 1, $options + ['x509_extensions' => 'v3_leaf']);

        openssl_x509_export($caCert, $caPem);
        openssl_x509_export($leafCert, $leafPem);
        openssl_pkey_export($leafKey, $leafKeyPem, null, ['config' => $config]);
        file_put_contents($this->dir . '/ca.pem', $caPem);
        file_put_contents($this->dir . '/server.pem', $leafPem . $leafKeyPem);

        $pipes        = [];
        $this->server = proc_open(
            [PHP_BINARY, dirname(__DIR__, 2) . '/Support/tls_recording_server.php', $this->dir . '/server.pem', $this->dir . '/requests.log'],
            [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes
        );
        self::assertIsResource($this->server);

        stream_set_timeout($pipes[1], 10);
        $banner = (string) fgets($pipes[1]);
        if (preg_match('/^TLS:(\\d+) PLAIN:\\d+$/', trim($banner), $m) !== 1) {
            self::fail('The recording server did not start: ' . $banner);
        }
        $this->tlsPort = (int) $m[1];
    }

    private function probe(string $path, bool $trustCa): string
    {
        $cainfo = $trustCa ? ' -d curl.cainfo=' . escapeshellarg($this->dir . '/ca.pem') : '';
        $url    = 'https://127.0.0.1:' . $this->tlsPort . $path;
        $cmd    = escapeshellarg(PHP_BINARY) . ' -d display_errors=0' . $cainfo . ' ' . escapeshellarg(dirname(__DIR__, 2) . '/Fixtures/sumex/pdf_probe.php') . ' ' . escapeshellarg($url) . ' 2>&1';
        $output = shell_exec($cmd);
        self::assertNotNull($output, 'The probe produced no output.');

        return (string) $output;
    }

    /**
     * @return list<string>
     */
    private function requestsSeen(): array
    {
        $log = $this->dir . '/requests.log';

        return is_file($log) ? array_values(array_filter(explode("\n", (string) file_get_contents($log)))) : [];
    }
}
