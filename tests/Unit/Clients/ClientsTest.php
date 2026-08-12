<?php

declare(strict_types=1);

namespace Tests\Unit\Clients;

use ClientTitleEnum;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\AbstractTestCase;

class ClientsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function setUpClientTitleEnum(): void

        {

            if ( ! defined('BASEPATH')) {

                define('BASEPATH', dirname(__DIR__, 3) . '/system/');

            }



            require_once dirname(__DIR__, 3) . '/application/libraries/ClientTitleEnum.php';

        }
    #[Test]

    public function it_returns_the_matching_title_value(): void

        {

            $this->setUpClientTitleEnum();

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

            $this->setUpClientTitleEnum();

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

            $this->setUpClientTitleEnum();

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
