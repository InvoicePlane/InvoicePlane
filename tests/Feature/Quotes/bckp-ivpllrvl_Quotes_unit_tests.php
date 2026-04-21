<?php

namespace Modules\Quotes\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Quotes\Models\Quote;
use Modules\Quotes\Services\QuotesService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QuotesServiceTest extends TestCase
{
    use RefreshDatabase;

    private QuotesService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(QuotesService::class);
    }

    #[Test]
    public function it_marks_quote_as_viewed_when_status_is_sent(): void
    {
        // Arrange
        $client = tmpClient::create([
            'client_name'   => 'Test Client',
            'client_active' => 1,
        ]);

        $quote = Quote::create([
            'client_id'       => $client->client_id,
            'quote_status_id' => 2, // Sent status
        ]);

        // Act
        $this->service->markViewed($quote->quote_id);

        // Assert
        $quote->refresh();
        $this->assertEquals(3, $quote->quote_status_id); // Viewed status
    }

    #[Test]
    public function it_does_not_change_status_when_quote_is_not_sent(): void
    {
        // Arrange
        $client = tmpClient::create([
            'client_name'   => 'Test Client',
            'client_active' => 1,
        ]);

        $quote = Quote::create([
            'client_id'       => $client->client_id,
            'quote_status_id' => 1, // Draft status
        ]);

        // Act
        $this->service->markViewed($quote->quote_id);

        // Assert
        $quote->refresh();
        $this->assertEquals(1, $quote->quote_status_id); // Should remain draft
    }

    #[Test]
    public function it_filters_quotes_by_client(): void
    {
        // Arrange
        $client1 = tmpClient::create([
            'client_name'   => 'Client 1',
            'client_active' => 1,
        ]);

        $client2 = tmpClient::create([
            'client_name'   => 'Client 2',
            'client_active' => 1,
        ]);

        Quote::create([
            'client_id'       => $client1->client_id,
            'quote_status_id' => 1,
        ]);

        Quote::create([
            'client_id'       => $client1->client_id,
            'quote_status_id' => 1,
        ]);

        Quote::create([
            'client_id'       => $client2->client_id,
            'quote_status_id' => 1,
        ]);

        // Act
        $result = $this->service->byClient($client1->client_id);

        // Assert
        $this->assertInstanceOf(QuotesService::class, $result);
    }

    #[Test]
    public function it_returns_db_array_with_correct_structure(): void
    {
        // Act
        $result = $this->service->dbArray();

        // Assert
        $this->assertIsArray($result);
    }
}
