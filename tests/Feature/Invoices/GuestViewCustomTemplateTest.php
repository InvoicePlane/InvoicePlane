<?php

namespace Tests\Feature\Invoices;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use View;

/**
 * InvoicePlane/InvoicePlane#1780: a custom public invoice template placed in
 * CUSTOM_TEMPLATES_FOLDER and allowlisted via CUSTOM_INVOICE_TEMPLATES_PUBLIC could be
 * selected in Settings, but the guest view then returned HTTP 500. Two bugs in
 * application/helpers/template_helper.php:
 *
 * 1. get_validated_template_path() validated the resolved path against
 *    APPPATH . 'views/' only, so a template that exists solely in CUSTOM_TEMPLATES_FOLDER
 *    failed validate_file_in_directory() and hit show_error(500), even though
 *    render_template_view() already gives the custom folder precedence.
 * 2. render_template_view() included the custom file from inside a plain function, so
 *    $this was undefined. The built-in InvoicePlane_Web template -- the natural starting
 *    point for a custom one -- calls $this->layout->load_view('layout/alerts'), so a copy
 *    of it crashed with "Using $this when not in object context".
 *
 * tests/Fixtures/custom_templates/invoice_templates/public/CustomWebTemplate.php
 * reproduces bug 2 by including that same $this->layout->load_view() call.
 */
#[CoversClass(View::class)]
class GuestViewCustomTemplateTest extends AbstractTestCase
{
    private const CUSTOM_TEMPLATE_NAME = 'CustomWebTemplate';

    // Allowlisted (passes validate_template_name()) but has no .php file anywhere --
    // simulates a CUSTOM_INVOICE_TEMPLATES_PUBLIC entry whose file was never created, or
    // was deleted/typo'd after being added to ipconfig.php.
    private const MISSING_TEMPLATE_NAME = 'GhostTemplate';

    protected function setUp(): void
    {
        parent::setUp();

        $this->withEnvironment([
            'CUSTOM_TEMPLATES_FOLDER'         => dirname(__DIR__, 2) . '/Fixtures/custom_templates',
            'CUSTOM_INVOICE_TEMPLATES_PUBLIC' => self::CUSTOM_TEMPLATE_NAME . ',' . self::MISSING_TEMPLATE_NAME,
        ]);
    }

    protected function tearDown(): void
    {
        $this->databaseDelete('ip_settings', ['setting_key' => 'public_invoice_template']);

        parent::tearDown();
    }

    #[Test]
    public function it_renders_a_guest_invoice_with_a_custom_public_template_from_the_custom_templates_folder(): void
    {
        /* Arrange */
        $this->selectPublicInvoiceTemplate(self::CUSTOM_TEMPLATE_NAME);
        $clientId = $this->seedClient();
        $urlKey   = 'custom-tpl-' . bin2hex(random_bytes(4));
        $this->seedInvoice($clientId, ['invoice_url_key' => $urlKey, 'invoice_status_id' => 2, 'payment_method' => 0, 'invoice_number' => 'CT-INV-1']);

        /* Act */
        $response = $this->get('/guest/view/invoice/' . $urlKey);

        /* Assert: not the 500 from either bug, and the custom file (not the built-in
         * default) actually rendered, proving render_template_view() both found it in
         * CUSTOM_TEMPLATES_FOLDER and ran its $this->layout->load_view() call successfully. */
        $this->assertResponseOk($response);
        $this->assertResponseBodyContains($response, 'CUSTOM-TEMPLATE-MARKER-CT-INV-1');

        // Rules out a silent fallback to the default template masking the custom lookup
        // failing: 'webpreview-header' only exists in the built-in InvoicePlane_Web.php.
        $this->assertResponseBodyNotContains($response, 'webpreview-header');
    }

    #[Test]
    public function it_still_renders_the_built_in_template_when_no_custom_template_is_selected(): void
    {
        /* Arrange: positive control -- CUSTOM_TEMPLATES_FOLDER/allowlist are configured
         * (per setUp()) but the setting still selects the built-in template */
        $this->selectPublicInvoiceTemplate('InvoicePlane_Web');
        $clientId = $this->seedClient();
        $urlKey   = 'builtin-tpl-' . bin2hex(random_bytes(4));
        $this->seedInvoice($clientId, ['invoice_url_key' => $urlKey, 'invoice_status_id' => 2, 'payment_method' => 0, 'invoice_number' => 'BT-INV-1']);

        /* Act */
        $response = $this->get('/guest/view/invoice/' . $urlKey);

        /* Assert */
        $this->assertResponseOk($response);
        $this->assertResponseBodyContains($response, 'BT-INV-1');
        $this->assertResponseBodyNotContains($response, 'CUSTOM-TEMPLATE-MARKER');
    }

    #[Test]
    public function it_falls_back_to_the_built_in_template_when_the_selected_one_is_allowlisted_but_its_file_is_missing(): void
    {
        /* Arrange: GhostTemplate passes validate_template_name() (it's in the allowlist) but
         * has no .php file in either CUSTOM_TEMPLATES_FOLDER or the built-in views directory --
         * get_validated_template_path() must fall back to the hardcoded default rather than
         * returning a path render_template_view() can't resolve either. */
        $this->selectPublicInvoiceTemplate(self::MISSING_TEMPLATE_NAME);
        $clientId = $this->seedClient();
        $urlKey   = 'missing-tpl-' . bin2hex(random_bytes(4));
        $this->seedInvoice($clientId, ['invoice_url_key' => $urlKey, 'invoice_status_id' => 2, 'payment_method' => 0, 'invoice_number' => 'MT-INV-1']);

        /* Act */
        $response = $this->get('/guest/view/invoice/' . $urlKey);

        /* Assert: no 500, and specifically the built-in template's own markup, proving the
         * fallback resolved to the real default file and not just an empty/partial render. */
        $this->assertResponseOk($response);
        $this->assertResponseBodyContains($response, 'webpreview-header');
        $this->assertResponseBodyContains($response, 'MT-INV-1');
        $this->assertResponseBodyNotContains($response, 'CUSTOM-TEMPLATE-MARKER');
    }

    private function selectPublicInvoiceTemplate(string $templateName): void
    {
        $this->databaseDelete('ip_settings', ['setting_key' => 'public_invoice_template']);
        $this->databaseInsert('ip_settings', ['setting_key' => 'public_invoice_template', 'setting_value' => $templateName]);
    }
}
