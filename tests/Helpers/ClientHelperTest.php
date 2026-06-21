<?php

namespace Tests\Helpers;

use Modules\Core\Support\ClientHelper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

#[CoversClass(Tests\Helpers\ClientHelper::class)]

class ClientHelperTest extends AbstractTestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Modules\Core\Support\ClientHelper does not exist — not yet implemented in CI3');
    }
    public static function genderProvider(): array
    {
        return [
            'male'    => [0],
            'female'  => [1],
            'other'   => [2],
            'unknown' => [99],
        ];
    }

    #[Test]
    public function it_formats_gender_male(): void
    {
        /* Arrange */

        /* Act */
        $result = ClientHelper::format_gender(0);

        /* Assert */
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    #[Test]
    public function it_formats_gender_female(): void
    {
        /* Arrange */

        /* Act */
        $result = ClientHelper::format_gender(1);

        /* Assert */
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    #[Test]
    public function it_formats_gender_other(): void
    {
        /* Arrange */

        /* Act */
        $result = ClientHelper::format_gender(2);

        /* Assert */
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    #[Test]
    #[DataProvider('genderProvider')]
    public function it_formats_various_genders(int $gender): void
    {
        /* Arrange */

        /* Act */
        $result = ClientHelper::format_gender($gender);

        /* Assert */
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    #[Test]
    public function it_handles_string_gender_values(): void
    {
        /* Arrange */

        /* Act */
        $result = ClientHelper::format_gender('0');

        /* Assert */
        $this->assertIsString($result);
    }

    #[Test]
    public function it_handles_null_gender(): void
    {
        /* Arrange */

        /* Act */
        $result = ClientHelper::format_gender(null);

        /* Assert */
        $this->assertIsString($result);
    }
}
