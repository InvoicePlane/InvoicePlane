<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * SetupController Feature Tests.
 */
class SetupControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    #[Group('smoke')]
    public function it_allows_the_setup_flow_when_setup_is_explicitly_unlocked(): void
    {
        /* Arrange */
        $this->withEnvironment([
            'SETUP_COMPLETED' => 'false',
            'DISABLE_SETUP'   => 'false',
        ]);

        /* Act */
        $response = $this->get('/setup/language');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
        $this->assertResponseBodyContains($response, 'setup');
    }

    #[Test]
    #[Group('security')]
    public function it_locks_every_http_setup_route_after_setup_is_completed(): void
    {
        /* Arrange */
        $this->withEnvironment([
            'SETUP_COMPLETED' => 'true',
            'DISABLE_SETUP'   => 'false',
        ]);

        $setupRoutes = [
            '/setup',
            '/setup/language',
            '/setup/prerequisites',
            '/setup/configure_database',
            '/setup/install_tables',
            '/setup/upgrade_tables',
            '/setup/create_user',
            '/setup/calculation_info',
            '/setup/complete',
        ];

        foreach ($setupRoutes as $route) {
            /* Act */
            $response = $this->get($route);

            /* Assert */
            self::assertSame(
                403,
                $response->statusCode(),
                "Completed installations must block HTTP setup route [{$route}]."
            );
        }
    }

    #[Test]
    public function it_redirects_direct_setup_steps_to_the_wizard_when_setup_is_unlocked(): void
    {
        /* Arrange */
        $this->withEnvironment([
            'SETUP_COMPLETED' => 'false',
            'DISABLE_SETUP'   => 'false',
        ]);

        /* Act */
        $response = $this->get('/setup/upgrade_tables');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unlocked direct setup step should redirect into the wizard. Got [%d].', $response->statusCode())
        );
        self::assertNotSame(403, $response->statusCode());
    }
}
