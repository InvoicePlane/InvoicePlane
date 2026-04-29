<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

#[CoversNothing]
class ViewTemplateSystemTest extends AbstractTestCase
{
    #[Test]
    public function it_php_view_engine_is_registered(): void
    {
        $this->markTestIncomplete('InvoicePlane uses CI3 views, not Laravel view engine resolver.');
    }

    #[Test]
    public function it_blade_engine_is_available_as_secondary(): void
    {
        $this->markTestIncomplete('InvoicePlane uses CI3 views, not Blade engine.');
    }

    #[Test]
    public function it_plain_php_views_can_be_rendered(): void
    {
        $this->markTestIncomplete('InvoicePlane uses CI3 views, not Laravel view rendering.');
    }

    #[Test]
    public function it_welcome_view_is_php_template(): void
    {
        $this->markTestIncomplete('InvoicePlane uses CI3 views, not Laravel resource views.');
    }
}
