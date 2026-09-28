<?php

namespace Tests\Feature\Core\Security;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Regression tests for nginx deny rules on sensitive directories.
 *
 * The nginx configuration in resources/docker/nginx/invoiceplane.conf must
 * include explicit `deny all; return 404;` blocks for directories that:
 *   - Hold PHP session files (storage/)
 *   - Hold archived PDFs (uploads/archive/)
 *   - Hold customer attachments (uploads/customer_files/)
 *   - Hold import CSVs (uploads/import/)
 *   - Hold temporary SUMEX XML (uploads/temp/)
 *
 * These directories have .htaccess `Deny from all` (Apache protection), but
 * nginx ignores .htaccess files entirely. Without explicit nginx blocks,
 * unauthenticated attackers can directly download session files (session hijack),
 * archived invoices, import CSVs, and patient PII via SUMEX XML on Docker
 * deployments.
 *
 * Note: The HTTP harness does not invoke nginx, so these tests assert on the
 * configuration file and .htaccess fallbacks instead of on HTTP responses.
 * For end-to-end verification, deploy with Docker Compose and manually verify:
 *   curl http://target/storage/ci_session* → 404
 *   curl http://target/uploads/archive/* → 404
 *   curl http://target/uploads/import/* → 404
 */
#[Group('security')]
class NginxDenyRulesTest extends TestCase
{
    private string $rootPath;

    protected function setUp(): void
    {
        parent::setUp();
        // Navigate from tests/Feature/Core/Security/ to project root (5 levels up)
        $this->rootPath = dirname(__DIR__, 5);
    }

    #[Test]
    public function it_includes_nginx_deny_rules_for_storage(): void
    {
        /* Arrange */
        $nginxConfig = $this->rootPath . '/resources/docker/nginx/invoiceplane.conf';

        /* Act */
        $content = file_get_contents($nginxConfig);

        /* Assert */
        self::assertFileExists(
            $nginxConfig,
            'resources/docker/nginx/invoiceplane.conf must exist.'
        );
        self::assertStringContainsString(
            'location ^~ /storage/',
            $content,
            'nginx config must include location ^~ /storage/ block.'
        );
        self::assertStringContainsString(
            'deny all',
            $content,
            'The /storage/ location block must include deny all; (protects PHP session files from direct HTTP access).'
        );
    }

    #[Test]
    public function it_includes_nginx_deny_rules_for_uploads_archive(): void
    {
        /* Arrange */
        $nginxConfig = $this->rootPath . '/resources/docker/nginx/invoiceplane.conf';

        /* Act */
        $content = file_get_contents($nginxConfig);

        /* Assert */
        self::assertStringContainsString(
            'location ^~ /uploads/archive/',
            $content,
            'nginx config must include location ^~ /uploads/archive/ block.'
        );
        self::assertStringContainsString(
            'deny all',
            $content,
            'The /uploads/archive/ location block must include deny all; (protects archived invoice PDFs from direct HTTP access).'
        );
    }

    #[Test]
    public function it_includes_nginx_deny_rules_for_uploads_customer_files(): void
    {
        /* Arrange */
        $nginxConfig = $this->rootPath . '/resources/docker/nginx/invoiceplane.conf';

        /* Act */
        $content = file_get_contents($nginxConfig);

        /* Assert */
        self::assertStringContainsString(
            'location ^~ /uploads/customer_files/',
            $content,
            'nginx config must include location ^~ /uploads/customer_files/ block.'
        );
        self::assertStringContainsString(
            'deny all',
            $content,
            'The /uploads/customer_files/ location block must include deny all; (protects customer attachment files from direct HTTP access).'
        );
    }

    #[Test]
    public function it_includes_nginx_deny_rules_for_uploads_import(): void
    {
        /* Arrange */
        $nginxConfig = $this->rootPath . '/resources/docker/nginx/invoiceplane.conf';

        /* Act */
        $content = file_get_contents($nginxConfig);

        /* Assert */
        self::assertStringContainsString(
            'location ^~ /uploads/import/',
            $content,
            'nginx config must include location ^~ /uploads/import/ block.'
        );
        self::assertStringContainsString(
            'deny all',
            $content,
            'The /uploads/import/ location block must include deny all; (protects import CSVs containing bulk client PII from direct HTTP access).'
        );
    }

    #[Test]
    public function it_includes_nginx_deny_rules_for_uploads_temp(): void
    {
        /* Arrange */
        $nginxConfig = $this->rootPath . '/resources/docker/nginx/invoiceplane.conf';

        /* Act */
        $content = file_get_contents($nginxConfig);

        /* Assert */
        self::assertStringContainsString(
            'location ^~ /uploads/temp/',
            $content,
            'nginx config must include location ^~ /uploads/temp/ block.'
        );
        self::assertStringContainsString(
            'deny all',
            $content,
            'The /uploads/temp/ location block must include deny all; (protects temporary SUMEX XML files containing patient PII from direct HTTP access).'
        );
    }

    #[Test]
    public function it_includes_htaccess_fallback_for_uploads_import_on_apache(): void
    {
        /* Arrange */
        $htaccessPath = $this->rootPath . '/uploads/import/.htaccess';

        /* Act */
        $content = file_get_contents($htaccessPath);

        /* Assert */
        self::assertFileExists(
            $htaccessPath,
            'uploads/import/.htaccess must exist as fallback protection for Apache deployments.'
        );
        self::assertStringContainsString(
            'Deny from all',
            $content,
            'uploads/import/.htaccess must include Deny from all; (Apache protection when nginx config is absent).'
        );
    }

    #[Test]
    public function it_includes_htaccess_fallback_for_uploads_archive_on_apache(): void
    {
        /* Arrange */
        $htaccessPath = $this->rootPath . '/uploads/archive/.htaccess';

        /* Act */
        $content = file_get_contents($htaccessPath);

        /* Assert */
        self::assertFileExists(
            $htaccessPath,
            'uploads/archive/.htaccess must exist as fallback protection for Apache deployments.'
        );
        self::assertStringContainsString(
            'Deny from all',
            $content,
            'uploads/archive/.htaccess must include Deny from all; (Apache protection when nginx config is absent).'
        );
    }

    #[Test]
    public function it_includes_htaccess_fallback_for_uploads_customer_files_on_apache(): void
    {
        /* Arrange */
        $htaccessPath = $this->rootPath . '/uploads/customer_files/.htaccess';

        /* Act */
        $content = file_get_contents($htaccessPath);

        /* Assert */
        self::assertFileExists(
            $htaccessPath,
            'uploads/customer_files/.htaccess must exist as fallback protection for Apache deployments.'
        );
        self::assertStringContainsString(
            'Deny from all',
            $content,
            'uploads/customer_files/.htaccess must include Deny from all; (Apache protection when nginx config is absent).'
        );
    }
}
