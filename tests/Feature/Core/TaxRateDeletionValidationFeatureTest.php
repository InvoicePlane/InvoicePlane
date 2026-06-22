<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * TaxRateDeletionValidation Feature Tests.
 *
 * Tests tax rate deletion validation.
 */
class TaxRateDeletionValidationFeatureTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    #[Group('smoke')]
    public function it_returns_a_successful_response_or_redirect(): void
    {
        /* Arrange */
        $this->databaseInsert('ip_tax_rates', [
            'tax_rate_name'    => 'Deletion Tax Rate',
            'tax_rate_percent' => '15.00',
        ]);

        /* Act */
        $response = $this->get('/tax_rates');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertDatabaseHas('ip_tax_rates', ['tax_rate_name' => 'Deletion Tax Rate']);
        $this->assertResponseBodyContains($response, '<html');
    }

    #[Test]
    public function it_redirects_a_guest_to_login(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/tax_rates');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET [/tax_rates] must redirect. Got [%d].', $response->statusCode())
        );
    }
}
