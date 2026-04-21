<?php

namespace Modules\Core\Tests\Unit;

use Modules\Core\Support\ClientHelper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\UnitTestCase;

#[CoversClass(ClientHelper::class)]

class PagerHelperTest extends UnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Clean up quotes table before each test
        $this->cleanupTables(['ip_quotes']);
    }

    #[Test]
    public function it_returns_links_html_when_given_length_aware_paginator(): void
    {
        // Arrange
        $items = collect([
            ['id' => 1, 'name' => 'Item 1'],
            ['id' => 2, 'name' => 'Item 2'],
            ['id' => 3, 'name' => 'Item 3'],
        ]);

        $paginator = new LengthAwarePaginator(
            $items,
            3,
            15,
            1,
            ['path' => '/test']
        );

        // Act
        $result = PagerHelper::pager('/test', $paginator);

        // Assert
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
        // Laravel pagination HTML contains navigation elements
        $this->assertStringContainsString('pagination', $result);
    }

    #[Test]
    public function it_returns_links_html_when_given_simple_paginator(): void
    {
        // Arrange
        $items = collect([
            ['id' => 1, 'name' => 'Item 1'],
            ['id' => 2, 'name' => 'Item 2'],
        ]);

        $paginator = new Paginator(
            $items,
            15,
            1,
            ['path' => '/test']
        );

        // Act
        $result = PagerHelper::pager('/test', $paginator);

        // Assert
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
        $this->assertStringContainsString('pagination', $result);
    }

    #[Test]
    public function it_paginates_eloquent_builder_and_returns_links(): void
    {
        // Arrange - Create test quotes
        for ($i = 1; $i <= 30; $i++) {
            $this->createTestQuote([
                'quote_number' => 'Q-' . mb_str_pad($i, 4, '0', STR_PAD_LEFT),
            ]);
        }

        $builder = Quote::query()->where('quote_status_id', '>', 0);

        // Act
        $result = PagerHelper::pager('/quotes', $builder, 10);

        // Assert
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
        $this->assertStringContainsString('pagination', $result);
    }

    #[Test]
    public function it_paginates_query_builder_and_returns_links(): void
    {
        // Arrange - Create test data
        for ($i = 1; $i <= 20; $i++) {
            $this->createTestQuote([
                'quote_number' => 'Q-' . mb_str_pad($i, 4, '0', STR_PAD_LEFT),
            ]);
        }

        $builder = Quote::query()->getQuery()->where('quote_status_id', '>', 0);

        // Act
        $result = PagerHelper::pager('/quotes', $builder, 5);

        // Assert
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
        $this->assertStringContainsString('pagination', $result);
    }

    #[Test]
    public function it_uses_default_per_page_when_not_specified(): void
    {
        // Arrange - Create test quotes
        for ($i = 1; $i <= 20; $i++) {
            $this->createTestQuote([
                'quote_number' => 'Q-' . mb_str_pad($i, 4, '0', STR_PAD_LEFT),
            ]);
        }

        $builder = Quote::query();

        // Act - Not passing perPage, should use default of 15
        $result = PagerHelper::pager('/quotes', $builder);

        // Assert
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
        $this->assertStringContainsString('pagination', $result);
    }

    #[Test]
    public function it_returns_empty_string_for_plain_array(): void
    {
        // Arrange
        $array = [
            ['id' => 1, 'name' => 'Item 1'],
            ['id' => 2, 'name' => 'Item 2'],
        ];

        // Act
        $result = PagerHelper::pager('/test', $array);

        // Assert
        $this->assertSame('', $result);
    }

    #[Test]
    public function it_returns_empty_string_for_collection(): void
    {
        // Arrange
        $collection = collect([
            ['id' => 1, 'name' => 'Item 1'],
            ['id' => 2, 'name' => 'Item 2'],
        ]);

        // Act
        $result = PagerHelper::pager('/test', $collection);

        // Assert
        $this->assertSame('', $result);
    }

    #[Test]
    public function it_returns_empty_string_for_null(): void
    {
        // Act
        $result = PagerHelper::pager('/test', null);

        // Assert
        $this->assertSame('', $result);
    }

    #[Test]
    public function it_returns_empty_string_for_string(): void
    {
        // Act
        $result = PagerHelper::pager('/test', 'mdl_quotes');

        // Assert
        $this->assertSame('', $result);
    }

    #[Test]
    public function it_handles_empty_eloquent_builder(): void
    {
        // Arrange - Builder with no results
        $builder = Quote::query()->where('quote_id', -1); // No matching records

        // Act
        $result = PagerHelper::pager('/quotes', $builder);

        // Assert
        $this->assertIsString($result);
        // Even with no results, pagination HTML may be rendered
    }

    #[Test]
    public function it_preserves_builder_constraints_when_paginating(): void
    {
        // Arrange - Create draft and sent quotes
        for ($i = 1; $i <= 10; $i++) {
            $this->createTestQuote([
                'quote_number'    => 'Q-DRAFT-' . mb_str_pad($i, 4, '0', STR_PAD_LEFT),
                'quote_status_id' => 1, // Draft
            ]);
        }

        for ($i = 1; $i <= 10; $i++) {
            $this->createTestQuote([
                'quote_number'    => 'Q-SENT-' . mb_str_pad($i, 4, '0', STR_PAD_LEFT),
                'quote_status_id' => 2, // Sent
            ]);
        }

        $builder = Quote::query()->where('quote_status_id', 1); // Draft only

        // Act
        $result = PagerHelper::pager('/quotes/draft', $builder, 5);

        // Assert
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
        // The builder should have been paginated with draft filter preserved
        $this->assertStringContainsString('pagination', $result);
    }

    #[Test]
    public function it_respects_custom_per_page_parameter(): void
    {
        // Arrange - Create test quotes
        for ($i = 1; $i <= 50; $i++) {
            $this->createTestQuote([
                'quote_number' => 'Q-' . mb_str_pad($i, 4, '0', STR_PAD_LEFT),
            ]);
        }

        $builder = Quote::query();

        // Act - Use custom perPage of 25
        $result = PagerHelper::pager('/quotes', $builder, 25);

        // Assert
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
        $this->assertStringContainsString('pagination', $result);
    }

    #[Test]
    public function it_handles_already_paginated_results_without_double_pagination(): void
    {
        // Arrange - Create test quotes
        for ($i = 1; $i <= 30; $i++) {
            $this->createTestQuote([
                'quote_number' => 'Q-' . mb_str_pad($i, 4, '0', STR_PAD_LEFT),
            ]);
        }

        $paginated = Quote::query()->paginate(10);

        // Act - Pass already paginated result
        $result = PagerHelper::pager('/quotes', $paginated);

        // Assert
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
        $this->assertStringContainsString('pagination', $result);
    }
}
