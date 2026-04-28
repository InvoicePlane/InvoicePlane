<?php

namespace Tests\Unit\Quotes;

use Mdl_Quotes;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

#[CoversClass(Mdl_Quotes::class)]
class QuoteModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('quotes/mdl_quotes');
        $this->model = $this->CI->mdl_quotes;
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_quote_statuses(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_save_validation_rules_without_quote_id(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_save_validation_rules_with_quote_id(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Test]
    public function it_generates_url_key(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Group('exotic')]
    #[Test]
    public function it_calculates_date_due(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Group('relationships')]
    #[Test]
    public function it_finds_quote_with_relations(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Group('relationships')]
    #[Test]
    public function it_finds_quote_with_custom_relations(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Group('relationships')]
    #[Test]
    public function it_returns_null_when_quote_not_found(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Group('relationships')]
    #[Test]
    public function it_finds_quote_or_fails(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Group('relationships')]
    #[Test]
    public function it_throws_exception_when_quote_not_found(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Group('relationships')]
    #[Test]
    public function it_gets_all_quotes_with_relations_paginated(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Group('relationships')]
    #[Test]
    public function it_filters_quotes_by_status(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Group('relationships')]
    #[Test]
    public function it_respects_custom_per_page_parameter(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Group('queries')]
    #[Test]
    public function it_gets_quotes_by_client_id(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }
}
