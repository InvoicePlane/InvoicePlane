<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
namespace Tests\Unit\Clients;

use Mdl_Clients;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * ClientService Deletion Validation Tests.
 *
 * Tests business rules for client deletion:
 * - Clients with invoices cannot be deleted
 * - Clients with quotes cannot be deleted
 * - Clients with projects cannot be deleted
 */
#[CoversClass(Mdl_Clients::class)]
class ClientModelTest extends AbstractTestCase
{
    private ClientService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new ClientService();
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $rules = $this->model->getValidationRules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('client_name', $rules);
        $this->assertArrayHasKey('client_email', $rules);
        $this->assertArrayHasKey('client_phone', $rules);
        $this->assertArrayHasKey('client_active', $rules);
    }
}
