<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
namespace Tests\Unit\Models;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

/**
 * Basic smoke tests for Mdl_settings model
 * Tests verify model structure and key functionality
 */
#[CoversClass(Mdl_Settings::class)]
class Mdl_settingsTest extends CiTestCase
{
    protected $model;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->CI->load->model('settings/mdl_settings');
        $this->model = $this->CI->mdl_settings;
    }

    #[Test]
    public function it_has_save_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'save'));
    }

    #[Test]
    public function it_has_save_batch_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'save_batch'));
    }

    #[Test]
    public function it_has_get_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'get'));
    }

    #[Test]
    public function it_extends_ci_model(): void
    {
        $this->assertInstanceOf('CI_Model', $this->model);
    }
}
