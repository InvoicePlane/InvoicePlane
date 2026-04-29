<?php

namespace Tests\Feature\Invoices;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class InvoicesRecurringModelTest extends AbstractTestCase
{
    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $this->markTestIncomplete('InvoicesRecurringService does not exist in CI3');
    }

    #[Test]
    public function it_validates_invoice_id_as_required_integer(): void
    {
        $this->markTestIncomplete('InvoicesRecurringService does not exist in CI3');
    }

    #[Test]
    public function it_validates_recur_start_date_as_required_date(): void
    {
        $this->markTestIncomplete('InvoicesRecurringService does not exist in CI3');
    }

    #[Test]
    public function it_validates_recur_end_date_as_nullable_date(): void
    {
        $this->markTestIncomplete('InvoicesRecurringService does not exist in CI3');
    }

    #[Test]
    public function it_validates_recur_frequency_as_required_string(): void
    {
        $this->markTestIncomplete('InvoicesRecurringService does not exist in CI3');
    }

    #[Test]
    public function it_validates_recur_next_date_as_nullable_date(): void
    {
        $this->markTestIncomplete('InvoicesRecurringService does not exist in CI3');
    }

    #[Test]
    public function it_provides_all_required_validation_keys(): void
    {
        $this->markTestIncomplete('InvoicesRecurringService does not exist in CI3');
    }
}
