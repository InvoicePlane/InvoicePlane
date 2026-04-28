<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
namespace Tests\Feature\Core;

use Modules\Core\Controllers\AjaxController as CoreAjaxController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

/**
 * Core AjaxController Feature Tests.
 *
 * Tests AJAX requests for settings operations.
 */
#[CoversClass(CoreAjaxController::class)]
#[CoversClass(Tests\Feature\Core\ViewTemplateSystem::class)]

class ViewTemplateSystemTest extends AbstractTestCase
{
    /**
     * Test that PHP view engine is registered.
     */
    public function test_php_view_engine_is_registered(): void
    {
        $resolver = $this->app->make('view.engine.resolver');

        // PHP engine should be registered
        $phpEngine = $resolver->resolve('php');
        $this->assertInstanceOf(\Illuminate\View\Engines\PhpEngine::class, $phpEngine);
    }

    /**
     * Test that Blade engine is available but secondary.
     */
    public function test_blade_engine_is_available_as_secondary(): void
    {
        $resolver = $this->app->make('view.engine.resolver');

        // Blade engine should also be available
        $bladeEngine = $resolver->resolve('blade');
        $this->assertInstanceOf(\Illuminate\View\Engines\CompilerEngine::class, $bladeEngine);
    }

    /**
     * Test that plain PHP views can be rendered.
     */
    public function test_plain_php_views_can_be_rendered(): void
    {
        // Create a temporary PHP view
        $viewPath = resource_path('views/test_php_template.php');
        file_put_contents($viewPath, '<?php echo "PHP Template Works: " . $message; ?>');

        try {
            // Render the view
            $rendered = view('test_php_template', ['message' => 'Success'])->render();

            /* Assert */
            $this->assertStringContainsString('PHP Template Works: Success', $rendered);
        } finally {
            // Clean up
            if (file_exists($viewPath)) {
                unlink($viewPath);
            }
        }
    }

    /**
     * Test that welcome view uses PHP template.
     */
    public function test_welcome_view_is_php_template(): void
    {
        $welcomePath = resource_path('views/welcome.php');

        // The welcome view should be a .php file, not .blade.php
        $this->assertFileExists($welcomePath);

        // Should not have a .blade.php version
        $bladePath = resource_path('views/welcome.blade.php');
        $this->assertFileDoesNotExist($bladePath);
    }
}
