<?php

namespace Tests\Feature\Products;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

#[CoversClass(AjaxController::class)]
#[CoversClass(Tests\Feature\Products\AjaxController::class)]

class AjaxControllerTest extends AbstractTestCase
{
    #[Test]
    public function modal_product_lookups_returns_expected_results(): void
    {
        $this->markTestIncomplete('Implement meaningful test for modalProductLookups');
    }

    #[Test]
    public function process_product_selections_handles_selection_logic(): void
    {
        $this->markTestIncomplete('Implement meaningful test for processProductSelections');
    }
}
