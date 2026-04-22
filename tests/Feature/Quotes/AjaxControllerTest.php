<?php

namespace Tests\Feature\Quotes;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Test coverage for Invoices Ajax Controller (application/modules/invoices/controllers/Ajax.php).
 */
#[CoversClass()]

class AjaxControllerTest extends AbstractTestCase
{
    #[Test]
    public function it_saves_quote_item()
    {
        $this->markTestIncomplete('Implement meaningful test for save');
    }

    #[Test]
    public function it_saves_quote_tax_rate()
    {
        $this->markTestIncomplete('Implement meaningful test for saveQuoteTaxRate');
    }

    #[Test]
    public function it_deletes_quote_item()
    {
        $this->markTestIncomplete('Implement meaningful test for deleteItem');
    }

    #[Test]
    public function it_gets_quote_item()
    {
        $this->markTestIncomplete('Implement meaningful test for getItem');
    }

    #[Test]
    public function it_displays_copy_quote_modal()
    {
        $this->markTestIncomplete('Implement meaningful test for modalCopyQuote');
    }

    #[Test]
    public function it_copies_quote()
    {
        $this->markTestIncomplete('Implement meaningful test for copyQuote');
    }

    #[Test]
    public function it_displays_change_user_modal()
    {
        $this->markTestIncomplete('Implement meaningful test for modalChangeUser');
    }

    #[Test]
    public function it_changes_quote_user()
    {
        $this->markTestIncomplete('Implement meaningful test for changeUser');
    }

    #[Test]
    public function it_displays_change_client_modal()
    {
        $this->markTestIncomplete('Implement meaningful test for modalChangeClient');
    }

    #[Test]
    public function it_changes_quote_client()
    {
        $this->markTestIncomplete('Implement meaningful test for changeClient');
    }

    #[Test]
    public function it_displays_create_quote_modal()
    {
        $this->markTestIncomplete('Implement meaningful test for modalCreateQuote');
    }

    #[Test]
    public function it_creates_quote()
    {
        $this->markTestIncomplete('Implement meaningful test for create');
    }

    #[Test]
    public function it_displays_quote_to_invoice_modal()
    {
        $this->markTestIncomplete('Implement meaningful test for modalQuoteToInvoice');
    }

    #[Test]
    public function it_converts_quote_to_invoice()
    {
        $this->markTestIncomplete('Implement meaningful test for quoteToInvoice');
    }
}
