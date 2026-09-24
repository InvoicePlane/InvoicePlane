<?php

namespace Tests\Unit\Security;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CountryHelperTest extends TestCase
{
    protected function setUp(): void
    {
        require_once dirname(__DIR__, 3) . '/application/helpers/country_helper.php';
    }

    #[Test]
    public function it_loads_a_valid_country_locale(): void
    {
        /* Arrange */
        $locale = 'nl';

        /* Act */
        $countries = get_country_list($locale);

        /* Assert */
        self::assertSame('Nederland', $countries['NL']);
    }

    #[Test]
    public function it_falls_back_to_english_for_a_path_traversal_locale(): void
    {
        /* Arrange */
        $locale = '../etc/passwd';

        /* Act */
        $countries = get_country_list($locale);

        /* Assert */
        self::assertSame('The Netherlands', $countries['NL']);
    }
}
