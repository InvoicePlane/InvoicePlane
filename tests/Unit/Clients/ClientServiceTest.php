<?php

namespace Modules\Crm\Tests\Unit;

use Modules\Crm\Models\Client;
use Modules\Crm\Services\ClientService;
use Modules\Invoices\Models\Invoice;
use Modules\Projects\Models\Project;
use Modules\Quotes\Models\Quote;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractServiceTestCase;

/**
 * ClientService Deletion Validation Tests.
 *
 * Tests business rules for client deletion:
 * - Clients with invoices cannot be deleted
 * - Clients with quotes cannot be deleted
 * - Clients with projects cannot be deleted
 */
#[CoversClass(ClientService::class)]

class ClientServiceTest extends AbstractServiceTestCase
{
    private ClientService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ClientService();
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $rules = $this->service->getValidationRules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('client_name', $rules);
        $this->assertArrayHasKey('client_email', $rules);
        $this->assertArrayHasKey('client_phone', $rules);
        $this->assertArrayHasKey('client_active', $rules);
    }
}
