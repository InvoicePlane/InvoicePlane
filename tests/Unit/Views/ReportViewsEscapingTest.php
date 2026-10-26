<?php

namespace Tests\Unit\Views;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Support\FakeCiSettings;

/**
 * GHSA-9jmq-q7m8-vjm3: the report views are rendered to HTML and handed to mPDF, which fetches any <img src> it finds,
 * so unescaped request data in them is a server-side request forgery. The from/to date headings are now wrapped in
 * htmlsc(). ReportsControllerTest only checks that a PDF comes back (%PDF-, %%EOF, size), which passes whether or
 * not the heading is escaped. These render the four real views with a hostile date and inspect the HTML.
 */
final class ReportViewsEscapingTest extends TestCase
{
    private const HOSTILE = '<img src="http://169.254.169.254/latest/meta-data/">';

    private mixed $previousCi = null;

    protected function setUp(): void
    {
        parent::setUp();

        $root = dirname(__DIR__, 3);
        require_once $root . '/application/helpers/echo_helper.php';
        require_once $root . '/application/helpers/number_helper.php';
        require_once $root . '/application/helpers/trans_helper.php';
        require_once $root . '/application/helpers/settings_helper.php';

        $this->previousCi                = $GLOBALS['unitCiInstance'] ?? null;
        $GLOBALS['unitCiInstance']       = (object) ['mdl_settings' => new FakeCiSettings()];
        $GLOBALS['unitCiInstance']->lang = new class () {
            public function line(string $line): string
            {
                return $line;
            }
        };
    }

    protected function tearDown(): void
    {
        $GLOBALS['unitCiInstance'] = $this->previousCi;

        parent::tearDown();
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function reportViews(): array
    {
        return [
            'sales by year'       => ['sales_by_year'],
            'payment history'     => ['payment_history'],
            'sales by client'     => ['sales_by_client'],
            'invoices per client' => ['invoices_per_client'],
        ];
    }

    #[Test]
    #[DataProvider('reportViews')]
    public function it_escapes_the_from_and_to_dates_in_the_report_heading(string $view): void
    {
        /* Act */
        $html = $this->render($view, ['from_date' => self::HOSTILE, 'to_date' => 'x' . self::HOSTILE, 'results' => []]);

        /* Assert: the markup is shown as text, never emitted as a tag */
        self::assertStringNotContainsString('<img', $html, 'A date value must never reach mPDF as an <img> tag.');
        self::assertStringContainsString('&lt;img src=&quot;http://169.254.169.254', $html);
    }

    #[Test]
    #[DataProvider('reportViews')]
    public function it_prints_an_ordinary_date_range_unchanged(string $view): void
    {
        /* Act */
        $html = $this->render($view, ['from_date' => '01/02/2026', 'to_date' => '28/02/2026', 'results' => []]);

        /* Assert: positive control, the heading is there and not blanked */
        self::assertStringContainsString('01/02/2026 - 28/02/2026', $html);
    }

    /**
     * @param array<string, mixed> $vars
     */
    private function render(string $view, array $vars): string
    {
        extract($vars);

        ob_start();
        include dirname(__DIR__, 3) . '/application/views/reports/' . $view . '.php';

        return (string) ob_get_clean();
    }
}
