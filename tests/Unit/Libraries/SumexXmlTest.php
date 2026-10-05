<?php

namespace Tests\Unit\Libraries;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Support\FakeCiSettings;

final class SumexXmlTest extends TestCase
{
    private mixed $ciBackup = null;

    protected function setUp(): void
    {
        require_once APPPATH . 'helpers/invoice_helper.php';
        require_once APPPATH . 'libraries/Sumex.php';

        $this->ciBackup = $GLOBALS['unitCiInstance'] ?? null;
        $settings       = new FakeCiSettings();
        $settings->_data = [
            'sumex_sliptype' => '0',
            'currency_code'  => 'CHF',
            'sumex_role'     => '1',
            'sumex_place'    => '0',
            'sumex_canton'   => '24',
        ];
        $GLOBALS['unitCiInstance'] = (object) [
            'mdl_settings' => $settings,
            'load'         => new class {
                public function helper(mixed $h): void {}
            },
        ];
    }

    protected function tearDown(): void
    {
        $GLOBALS['unitCiInstance'] = $this->ciBackup;
    }

    #[Test]
    public function it_emits_a_well_formed_request_with_role_place_and_canton_from_settings(): void
    {
        $xml = $this->parse($this->sumex()->xml());

        self::assertSame('request', $xml->documentElement->localName);
        $body = $this->first($xml, 'body');
        self::assertSame('physiotherapist', $body->getAttribute('role'));
        self::assertSame('practice', $body->getAttribute('place'));
        self::assertSame('ZG', $this->first($xml, 'treatment')->getAttribute('canton'));
    }

    #[Test]
    public function it_maps_the_treatment_reason_index_to_its_label(): void
    {
        $xml = $this->parse($this->sumex(['sumex_reason' => 1])->xml());

        self::assertSame('accident', $this->first($xml, 'treatment')->getAttribute('reason'));
    }

    #[Test]
    public function it_uses_an_esr9_slip_with_a_27_digit_reference_by_default(): void
    {
        $xml = $this->parse($this->sumex(['user_subscribernumber' => '01-123456-7'])->xml());

        $esr = $this->first($xml, 'esr9');
        self::assertSame('01-123456-7', $esr->getAttribute('participant_number'));
        self::assertMatchesRegularExpression('/^06 \d{5} \d{5} \d{5} \d{5} \d{5}$/', $esr->getAttribute('reference_number'));
        self::assertNotSame('', $esr->getAttribute('coding_line'));
    }

    #[Test]
    public function it_uses_the_red_slip_when_configured(): void
    {
        $GLOBALS['unitCiInstance']->mdl_settings->_data['sumex_sliptype'] = '1';

        $xml = $this->parse($this->sumex(['user_subscribernumber' => '01-123456-7', 'invoice_number' => 'INV-9'])->xml());

        $red = $this->first($xml, 'esrRed');
        self::assertSame('01-123456-7', $red->getAttribute('post_account'));
        self::assertSame('011234567>', $red->getAttribute('coding_line2'));
        self::assertSame('INV-9', $this->first($xml, 'payment_reason')->nodeValue);
        self::assertSame(0, $xml->getElementsByTagName('esr9')->length);
    }

    #[Test]
    public function it_strips_attribute_breakout_characters_from_the_invoice_number(): void
    {
        $out = $this->sumex(['invoice_number' => 'INV"/><x a="1'])->xml();

        $xml = $this->parse($out);
        self::assertSame('INV/xa1', $this->first($xml, 'invoice')->getAttribute('request_id'));
    }

    #[Test]
    public function it_keeps_only_digits_and_dashes_in_the_subscriber_number(): void
    {
        $xml = $this->parse($this->sumex(['user_subscribernumber' => "01-12'3<>4-5abc"])->xml());

        self::assertSame('01-1234-5', $this->first($xml, 'esr9')->getAttribute('participant_number'));
    }

    #[Test]
    public function it_removes_control_characters_from_free_text(): void
    {
        $xml = $this->parse($this->sumex(['sumex_diagnosis' => "Back\x00 pain\x1F"])->xml());

        self::assertSame('Back pain', $this->first($xml, 'diagnosis')->nodeValue);
    }

    #[Test]
    public function it_omits_the_remark_when_there_are_no_observations_and_includes_it_otherwise(): void
    {
        $without = $this->parse($this->sumex(['sumex_observations' => ''])->xml());
        $with    = $this->parse($this->sumex(['sumex_observations' => 'Follow up'])->xml());

        self::assertSame(0, $without->getElementsByTagName('remark')->length);
        self::assertSame('Follow up', $this->first($with, 'remark')->nodeValue);
    }

    #[Test]
    public function it_omits_the_diagnosis_node_when_empty(): void
    {
        $xml = $this->parse($this->sumex(['sumex_diagnosis' => ''])->xml());

        self::assertSame(0, $xml->getElementsByTagName('diagnosis')->length);
    }

    #[Test]
    public function it_emits_one_record_per_item_with_a_sequential_id(): void
    {
        $xml = $this->parse($this->sumex([], [
            $this->item('Consult', '120', '2.00', '50.00', '100.00'),
            $this->item('Visit', '121', '1.00', '30.00', '30.00'),
        ])->xml());

        $records = $xml->getElementsByTagName('record_other');
        self::assertSame(2, $records->length);
        self::assertSame('1', $records->item(0)->getAttribute('record_id'));
        self::assertSame('2', $records->item(1)->getAttribute('record_id'));
        self::assertSame('120', $records->item(0)->getAttribute('code'));
        self::assertSame('Visit', $records->item(1)->getAttribute('name'));
        self::assertSame('100.00', $records->item(0)->getAttribute('amount'));
    }

    #[Test]
    public function it_maps_patient_gender_and_omits_an_empty_phone(): void
    {
        $male   = $this->parse($this->sumex(['client_gender' => '0', 'client_phone' => ''])->xml());
        $female = $this->parse($this->sumex(['client_gender' => '1', 'client_phone' => '079 111 22 33'])->xml());

        self::assertSame('male', $this->first($male, 'patient')->getAttribute('gender'));
        self::assertSame('female', $this->first($female, 'patient')->getAttribute('gender'));
        self::assertSame(0, $this->first($male, 'patient')->getElementsByTagName('phone')->length);
        self::assertSame('079 111 22 33', $this->first($female, 'patient')->getElementsByTagName('phone')->item(0)->nodeValue);
    }

    #[Test]
    public function it_only_sets_case_and_insured_ids_when_present(): void
    {
        $with    = $this->parse($this->sumex(['sumex_casenumber' => 'C-7', 'client_insurednumber' => '555'])->xml());
        $without = $this->parse($this->sumex(['sumex_casenumber' => '', 'client_insurednumber' => ''])->xml());

        self::assertSame('C-7', $this->first($with, 'org')->getAttribute('case_id'));
        self::assertSame('555', $this->first($with, 'org')->getAttribute('insured_id'));
        self::assertFalse($this->first($without, 'org')->hasAttribute('case_id'));
        self::assertFalse($this->first($without, 'org')->hasAttribute('insured_id'));
    }

    #[Test]
    public function it_merges_storno_and_copy_options_into_the_payload(): void
    {
        $xml = $this->parse($this->sumex([], null, ['storno' => '1', 'copy' => '1'])->xml());

        self::assertSame('1', $this->first($xml, 'payload')->getAttribute('storno'));
        self::assertSame('1', $this->first($xml, 'payload')->getAttribute('copy'));
    }

    /** @param array<string, mixed> $overrides */
    private function sumex(array $overrides = [], ?array $items = null, array $options = []): \Sumex
    {
        $invoice = (object) array_merge([
            'invoice_id'            => 7,
            'invoice_number'        => 'INV-7',
            'invoice_total'         => '150.00',
            'invoice_date_modified' => '2026-03-02 10:00:00',
            'client_id'             => 3,
            'client_name'           => 'Anna',
            'client_surname'        => 'Muster',
            'client_birthdate'      => '1980-05-06',
            'client_gender'         => '1',
            'client_address_1'      => 'Hauptstrasse 1',
            'client_zip'            => '8000',
            'client_city'           => 'Zurich',
            'client_phone'          => '',
            'client_avs'            => '7561234567890',
            'client_insurednumber'  => '999',
            'user_company'          => 'Praxis AG',
            'user_address_1'        => 'Via Cantonale 5',
            'user_zip'              => '6900',
            'user_city'             => 'Lugano',
            'user_phone'            => '091 000 00 00',
            'user_gln'              => '7601000000001',
            'user_rcc'              => 'C000002',
            'user_subscribernumber' => '01-123456-7',
            'sumex_casedate'        => '2026-02-01',
            'sumex_casenumber'      => '42',
            'sumex_treatmentstart'  => '2026-02-01',
            'sumex_treatmentend'    => '2026-02-10',
            'sumex_reason'          => 0,
            'sumex_diagnosis'       => 'Flu',
            'sumex_observations'    => '',
        ], $overrides);

        return new \Sumex(['invoice' => $invoice, 'items' => $items ?? [$this->item('Consult', '120', '1.00', '50.00', '50.00')], 'options' => $options]);
    }

    private function item(string $name, string $sku, string $qty, string $price, string $total): object
    {
        return (object) [
            'item_name'     => $name,
            'product_sku'   => $sku,
            'item_quantity' => $qty,
            'item_price'    => $price,
            'item_total'    => $total,
            'item_date'     => '2026-02-03',
        ];
    }

    private function parse(string|false $xml): \DOMDocument
    {
        self::assertIsString($xml);
        $doc = new \DOMDocument();
        self::assertTrue($doc->loadXML($xml), 'Sumex must emit well-formed XML.');

        return $doc;
    }

    private function first(\DOMDocument $doc, string $local): \DOMElement
    {
        $nodes = $doc->getElementsByTagName($local);
        self::assertGreaterThan(0, $nodes->length, "Expected <{$local}> in the output.");

        return $nodes->item(0);
    }
}
