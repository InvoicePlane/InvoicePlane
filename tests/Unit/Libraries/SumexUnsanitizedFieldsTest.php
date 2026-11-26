<?php

namespace Tests\Unit\Libraries;

use DOMDocument;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Sumex;
use Tests\Support\FakeCiSettings;

/**
 * Fields that used to reach the SUMEX XML without sanitisation (found while hardening GHSA-j7f7-999x-v4gx).
 * A control character in any attribute field made the document not well-formed, and an ampersand in a
 * text field was truncated because DOMNode::nodeValue reads it as an entity reference.
 */
final class SumexUnsanitizedFieldsTest extends TestCase
{
    private mixed $ciBackup = null;

    protected function setUp(): void
    {
        require_once APPPATH . 'helpers/invoice_helper.php';
        require_once APPPATH . 'libraries/Sumex.php';

        $this->ciBackup            = $GLOBALS['unitCiInstance'] ?? null;
        $settings                  = new FakeCiSettings();
        $settings->_data           = ['sumex_sliptype' => '0', 'currency_code' => 'CHF', 'sumex_role' => '1', 'sumex_place' => '0', 'sumex_canton' => '24'];
        $GLOBALS['unitCiInstance'] = (object) [
            'mdl_settings' => $settings,
            'load'         => new class () {
                public function helper(mixed $h): void {}
            },
        ];
    }

    protected function tearDown(): void
    {
        $GLOBALS['unitCiInstance'] = $this->ciBackup;
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: string}>
     */
    public static function unsanitizedAttributeFields(): array
    {
        return [
            'item name'             => ['item_name', 'name', 'record_other'],
            'company GLN'           => ['user_gln', 'ean_party', 'biller'],
            'company RCC'           => ['user_rcc', 'zsr', 'biller'],
            'case number'           => ['sumex_casenumber', 'case_id', 'org'],
            'client insured number' => ['client_insurednumber', 'insured_id', 'org'],
        ];
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function textFields(): array
    {
        return [
            'patient given name'  => ['client_name', 'givenname'],
            'company name'        => ['user_company', 'companyname'],
            'diagnosis'           => ['sumex_diagnosis', 'diagnosis'],
            'observations'        => ['sumex_observations', 'remark'],
            'patient city'        => ['client_city', 'city'],
            'patient street'      => ['client_address_1', 'street'],
            'company street'      => ['user_address_1', 'street'],
            'patient zip'         => ['client_zip', 'zip'],
            'company zip'         => ['user_zip', 'zip'],
            'patient family name' => ['client_surname', 'familyname'],
            'patient phone'       => ['client_phone', 'phone'],
            'company phone'       => ['user_phone', 'phone'],
        ];
    }

    #[Test]
    #[DataProvider('unsanitizedAttributeFields')]
    public function it_emits_well_formed_xml_when_an_attribute_field_contains_a_control_character(string $field, string $attribute, string $element): void
    {
        /* Arrange */
        $value = "AB\x01CD\x1FEF";

        /* Act */
        $doc = $this->parse($this->build($field, $value));

        /* Assert */
        $node = $doc->getElementsByTagNameNS('*', $element)->item(0) ?? $doc->getElementsByTagName($element)->item(0);
        self::assertNotNull($node);
        self::assertSame('ABCDEF', $node->getAttribute($attribute));
    }

    #[Test]
    public function it_strips_control_characters_from_the_patient_ssn_of_the_mvg_block(): void
    {
        /* Arrange: xmlInvoiceMvg() is not wired into the payload at the moment, so it is driven directly */
        $sumex = $this->makeSumex('client_avs', "756\x011234\x1F5");
        $sumex->xml();

        /* Act */
        $node = (new ReflectionMethod($sumex, 'xmlInvoiceMvg'))->invoke($sumex);

        /* Assert */
        self::assertSame('75612345', $node->getAttribute('ssn'));
    }

    #[Test]
    #[DataProvider('textFields')]
    public function it_keeps_an_ampersand_in_a_free_text_field(string $field, string $element): void
    {
        /* Act */
        $doc = $this->parse($this->build($field, 'Tom & Jerry AG'));

        /* Assert: the text is added as a text node, so '&' is neither truncated nor an entity reference */
        $texts = [];
        foreach ($doc->getElementsByTagNameNS('*', $element) as $node) {
            $texts[] = $node->textContent;
        }
        self::assertContains('Tom & Jerry AG', $texts);
    }

    private function makeSumex(string $field, string $value): Sumex
    {
        $invoice = (object) array_merge([
            'invoice_id'           => 7, 'invoice_number' => 'INV-7', 'invoice_total' => '150.00', 'invoice_date_modified' => '2026-03-02 10:00:00',
            'client_id'            => 3, 'client_name' => 'Anna', 'client_surname' => 'Muster', 'client_birthdate' => '1980-05-06', 'client_gender' => '1',
            'client_address_1'     => 'Hauptstrasse 1', 'client_zip' => '8000', 'client_city' => 'Zurich', 'client_phone' => '', 'client_avs' => '7561234567890',
            'client_insurednumber' => '999', 'user_company' => 'Praxis AG', 'user_address_1' => 'Via Cantonale 5', 'user_zip' => '6900', 'user_city' => 'Lugano',
            'user_phone'           => '091', 'user_gln' => '7601000000001', 'user_rcc' => 'C000002', 'user_subscribernumber' => '01-123456-7',
            'sumex_casedate'       => '2026-02-01', 'sumex_casenumber' => '42', 'sumex_treatmentstart' => '2026-02-01', 'sumex_treatmentend' => '2026-02-10',
            'sumex_reason'         => 0, 'sumex_diagnosis' => 'Flu', 'sumex_observations' => 'Rest',
        ], $field === 'item_name' ? [] : [$field => $value]);

        $item = (object) [
            'item_name'  => $field === 'item_name' ? $value : 'Consult', 'product_sku' => '120', 'item_quantity' => '1.00',
            'item_price' => '50.00', 'item_total' => '50.00', 'item_date' => '2026-02-03',
        ];

        return new Sumex(['invoice' => $invoice, 'items' => [$item], 'options' => []]);
    }

    private function build(string $field, string $value): string
    {
        $xml = $this->makeSumex($field, $value)->xml();
        self::assertIsString($xml);

        return $xml;
    }

    private function parse(string $xml): DOMDocument
    {
        $previous = libxml_use_internal_errors(true);
        $doc      = new DOMDocument();
        $loaded   = $doc->loadXML($xml);
        $errors   = array_map(static fn ($e) => trim($e->message), libxml_get_errors());
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        self::assertTrue($loaded, 'Sumex must emit well-formed XML. Parser said: ' . implode(' | ', $errors));

        return $doc;
    }
}
