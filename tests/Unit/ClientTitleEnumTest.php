<?php

namespace Tests\Unit;

use ClientTitleEnum;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ClientTitleEnumTest extends TestCase
{
    protected function setUp(): void
    {
        if ( ! defined('BASEPATH')) {
            define('BASEPATH', dirname(__DIR__, 2) . '/system/');
        }

        require_once dirname(__DIR__, 2) . '/application/libraries/ClientTitleEnum.php';
    }

    #[Test]
    public function it_returns_the_matching_title_value(): void
    {
        /* Arrange */
        $value = 'doctor';

        /* Act */
        $title = ClientTitleEnum::tryFrom($value);

        /* Assert */
        self::assertNotNull($title);
        self::assertSame($value, $title->value);
    }

    #[Test]
    public function it_returns_null_for_an_unknown_title(): void
    {
        /* Arrange */
        $value = 'unknown';

        /* Act */
        $title = ClientTitleEnum::tryFrom($value);

        /* Assert */
        self::assertNull($title);
    }

    #[Test]
    public function it_exposes_all_supported_titles_including_custom(): void
    {
        /* Arrange */
        $expected = ['mr', 'mrs', 'doctor', 'professor', 'custom'];

        /* Act */
        $actual = array_map(static fn (object $case): string => $case->value, ClientTitleEnum::cases());

        /* Assert */
        self::assertSame(
            $expected,
            $actual
        );
    }
}
