<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * pager() prints the base URL into href attributes. Callers build that URL from a segment of the request URI,
 * so the helper must encode it rather than trust every caller to have done so.
 */
final class PagerHelperTest extends TestCase
{
    private mixed $previousCi = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->previousCi          = $GLOBALS['unitCiInstance'] ?? null;
        $GLOBALS['unitCiInstance'] = (object) [
            'lang' => new class () {
                public function line(string $key): string
                {
                    return $key;
                }
            },
            'items' => (object) ['previous_offset' => 10, 'next_offset' => 30, 'last_offset' => 50],
        ];
        require_once dirname(__DIR__, 3) . '/application/helpers/echo_helper.php';
        require_once dirname(__DIR__, 3) . '/application/helpers/trans_helper.php';
        require_once dirname(__DIR__, 3) . '/application/helpers/pager_helper.php';
    }

    protected function tearDown(): void
    {
        $GLOBALS['unitCiInstance'] = $this->previousCi;

        parent::tearDown();
    }

    #[Test]
    public function it_encodes_markup_and_quotes_in_the_base_url_of_every_link(): void
    {
        /* Arrange: a base URL carrying an attribute breakout and a script tag */
        $baseUrl = 'http://localhost/clients/status/"><script>alert(1)</script>';

        /* Act */
        $html = pager($baseUrl, 'items');

        /* Assert: no raw tag survives, and all four links carry the encoded form */
        self::assertStringNotContainsString('<script>', $html);
        self::assertSame(4, substr_count($html, 'href="http://localhost/clients/status/&quot;&gt;&lt;script&gt;alert(1)&lt;/script&gt;/'));
    }

    #[Test]
    public function it_links_first_previous_next_and_last_offsets_for_an_ordinary_base_url(): void
    {
        /* Arrange: positive control, a plain URL must come out intact */
        $baseUrl = 'http://localhost/clients/status/active';

        /* Act */
        $html = pager($baseUrl, 'items');

        /* Assert */
        self::assertStringContainsString('href="http://localhost/clients/status/active/0" title="first"', $html);
        self::assertStringContainsString('href="http://localhost/clients/status/active/10" title="prev"', $html);
        self::assertStringContainsString('href="http://localhost/clients/status/active/30" title="next"', $html);
        self::assertStringContainsString('href="http://localhost/clients/status/active/50" title="last"', $html);
    }

    #[Test]
    public function it_disables_the_links_on_the_first_and_last_pages(): void
    {
        /* Arrange: nothing before, nothing after */
        $GLOBALS['unitCiInstance']->items = (object) ['previous_offset' => -1, 'next_offset' => 99, 'last_offset' => 50];

        /* Act */
        $html = pager('http://localhost/clients', 'items');

        /* Assert */
        self::assertSame(4, substr_count($html, 'btn btn-default disabled'));
        self::assertStringNotContainsString('http://localhost/clients', $html);
    }
}
