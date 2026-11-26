<?php

namespace Tests\Unit\Libraries;

use DOMDocument;
use DOMElement;
use DOMXPath;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sumex;
use Tests\Support\FakeCiSettings;

/**
 * GHSA-j7f7-999x-v4gx (XML control characters in free text) and GHSA-6f6w-6r4q-x79w (attribute injection through
 * invoice_number / subscriber number). Every user-controlled free-text field is fed a hostile value and the produced
 * XML is parsed with a real parser: it must be well-formed, carry no injected elements or attributes, and contain no
 * control character.
 */
final class SumexHostileInputTest extends TestCase
{
    private const CONTROL_CHARS = "\x00\x01\x02\x03\x04\x05\x06\x07\x08\x0B\x0C\x0E\x0F\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1A\x1B\x1C\x1D\x1E\x1F\x7F";

    private mixed $ciBackup = null;

    protected function setUp(): void
    {
        require_once APPPATH . 'helpers/invoice_helper.php';
        require_once APPPATH . 'libraries/Sumex.php';

        $this->ciBackup  = $GLOBALS['unitCiInstance'] ?? null;
        $settings        = new FakeCiSettings();
        $settings->_data = [
            'sumex_sliptype' => '0',
            'currency_code'  => 'CHF',
            'sumex_role'     => '1',
            'sumex_place'    => '0',
            'sumex_canton'   => '24',
        ];
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
     * @return array<string, array{0: string}>
     */
    public static function freeTextInvoiceFields(): array
    {
        return [
            'patient given name'    => ['client_name'],
            'patient family name'   => ['client_surname'],
            'patient street'        => ['client_address_1'],
            'patient zip'           => ['client_zip'],
            'patient city'          => ['client_city'],
            'patient phone'         => ['client_phone'],
            'company name'          => ['user_company'],
            'company street'        => ['user_address_1'],
            'company zip'           => ['user_zip'],
            'company city'          => ['user_city'],
            'company phone'         => ['user_phone'],
            'treatment diagnosis'   => ['sumex_diagnosis'],
            'treatment observation' => ['sumex_observations'],
        ];
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function singleControlCharacters(): array
    {
        $cases = [];
        foreach (array_merge(range(0x00, 0x08), [0x0B, 0x0C], range(0x0E, 0x1F), [0x7F]) as $code) {
            $cases[sprintf('U+%04X', $code)] = [chr($code)];
        }

        return $cases;
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function allowedWhitespace(): array
    {
        return [
            'tab'             => ["\t"],
            'line feed'       => ["\n"],
            'carriage return' => ["\r"],
            'space'           => [' '],
        ];
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function invoiceNumbers(): array
    {
        return [
            'plain token'                  => ['INV-2026/001.a_b', 'INV-2026/001.a_b'],
            'attribute breakout'           => ['X" evil="1" y="', 'Xevil1y'],
            'element breakout'             => ['"><invoice:evil/><x a="', 'invoiceevil/xa'],
            'single quote and ampersand'   => ["A'B&C;D", 'ABCD'],
            'spaces'                       => ['A B C', 'ABC'],
            'control characters'           => ["A\x00B\x01C\nD\rE\tF\x7FG", 'ABCDEFG'],
            'null byte truncation attempt' => ["INV\x00.php", 'INV.php'],
            'unicode'                      => ['Rechnung-Ä€日本-1', 'Rechnung--1'],
            'only illegal characters'      => ['"<>&\' =', ''],
            'empty'                        => ['', ''],
            'very long'                    => [str_repeat('A', 10000) . '"', str_repeat('A', 10000)],
        ];
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function subscriberNumbers(): array
    {
        return [
            'attribute breakout'     => ['"/><x a="01-123456-7', '01-123456-7'],
            'letters and whitespace' => [" 01-12a3456 -7\n", '01-123456-7'],
            'control characters'     => ["01-123\x00456\x01-7", '01-123456-7'],
            'unicode digits'         => ['١٢01-123456-7٣', '01-123456-7'],
        ];
    }

    #[Test]
    #[DataProvider('freeTextInvoiceFields')]
    public function it_strips_control_characters_and_keeps_markup_inert_in_every_free_text_field(string $field): void
    {
        /* Arrange */
        $hostile  = $field . "-A\x00B\x01C\x08D\x0BE\x0CF\x0EG\x1FH\x7FI \"q\" 'a' <b>x</b> <invoice:evil/> ]]> <!-- c --> <?pi x?>\n\tend";
        $expected = $field . "-ABCDEFGHI \"q\" 'a' <b>x</b> <invoice:evil/> ]]> <!-- c --> <?pi x?>\n\tend";

        /* Act */
        $raw = $this->sumex([$field => $hostile])->xml();
        $doc = $this->parse($raw);

        /* Assert */
        self::assertSame(0, preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $raw), $field . ' must not leak a control character into the XML.');
        self::assertContains($expected, $this->leafTexts($doc), $field . ' must be present, stripped of control characters only.');
        self::assertSame(0, $doc->getElementsByTagName('b')->length, 'Markup in ' . $field . ' must be escaped text, not an element.');
        self::assertSame(0, $doc->getElementsByTagNameNS('*', 'evil')->length, 'No element may be injected through ' . $field . '.');
        self::assertSame(0, $doc->getElementsByTagName('invoice:evil')->length);
    }

    #[Test]
    #[DataProvider('freeTextInvoiceFields')]
    public function it_emits_a_well_formed_document_for_a_clean_value_in_every_free_text_field(string $field): void
    {
        /* Arrange: positive control, the same field with a harmless value */
        $clean = $field . ' Müller 日本 d\'Or';

        /* Act */
        $doc = $this->parse($this->sumex([$field => $clean])->xml());

        /* Assert: clean (including non-ASCII) text is not altered by the sanitizer */
        self::assertContains($clean, $this->leafTexts($doc));
    }

    #[Test]
    #[DataProvider('singleControlCharacters')]
    public function it_removes_each_forbidden_control_character_from_the_diagnosis(string $char): void
    {
        /* Arrange */
        $value = 'Flu' . $char . 'virus';

        /* Act */
        $raw = $this->sumex(['sumex_diagnosis' => $value])->xml();
        $doc = $this->parse($raw);

        /* Assert */
        self::assertSame('Fluvirus', $this->first($doc, 'diagnosis')->textContent);
        self::assertStringNotContainsString($char, $raw);
    }

    #[Test]
    #[DataProvider('allowedWhitespace')]
    public function it_keeps_the_whitespace_characters_that_xml_allows(string $char): void
    {
        /* Arrange */
        $value = 'line1' . $char . 'line2';

        /* Act */
        $doc = $this->parse($this->sumex(['sumex_diagnosis' => $value])->xml());

        /* Assert */
        self::assertSame($value, $this->first($doc, 'diagnosis')->textContent);
    }

    #[Test]
    public function it_survives_a_value_made_only_of_control_characters_in_every_free_text_field(): void
    {
        /* Arrange */
        $overrides = [];
        foreach (self::freeTextInvoiceFields() as [$field]) {
            $overrides[$field] = self::CONTROL_CHARS;
        }

        /* Act */
        $raw = $this->sumex($overrides)->xml();
        $doc = $this->parse($raw);

        /* Assert */
        self::assertSame(0, preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $raw));
        self::assertSame('request', $doc->documentElement->localName);
    }

    #[Test]
    public function it_omits_the_diagnosis_and_remark_elements_when_the_value_is_only_control_characters(): void
    {
        /* Arrange */
        $overrides = ['sumex_diagnosis' => "\x01\x02\x7F", 'sumex_observations' => "\x00\x0B\x1F"];

        /* Act */
        $doc = $this->parse($this->sumex($overrides)->xml());

        /* Assert: sanitisation runs before the empty check, so no empty element is emitted */
        self::assertSame(0, $doc->getElementsByTagName('diagnosis')->length);
        self::assertSame(0, $doc->getElementsByTagName('remark')->length);
    }

    #[Test]
    public function it_emits_the_diagnosis_and_remark_elements_for_a_normal_value(): void
    {
        /* Act */
        $doc = $this->parse($this->sumex(['sumex_diagnosis' => 'Flu', 'sumex_observations' => 'Rest'])->xml());

        /* Assert: positive control for the omission test above */
        self::assertSame('Flu', $this->first($doc, 'diagnosis')->textContent);
        self::assertSame('Rest', $this->first($doc, 'remark')->textContent);
    }

    #[Test]
    public function it_carries_a_very_long_hostile_value_through_unchanged_apart_from_control_characters(): void
    {
        /* Arrange */
        $long = str_repeat("ab\x01<>\"'", 20000);

        /* Act */
        $doc = $this->parse($this->sumex(['sumex_observations' => $long])->xml());

        /* Assert */
        self::assertSame(str_repeat("ab<>\"'", 20000), $this->first($doc, 'remark')->textContent);
    }

    #[Test]
    #[DataProvider('invoiceNumbers')]
    public function it_restricts_the_invoice_number_to_the_attribute_allowlist_in_the_request_id(string $input, string $expected): void
    {
        /* Act */
        $raw = $this->sumex(['invoice_number' => $input])->xml();
        $doc = $this->parse($raw);

        /* Assert */
        $invoice = $this->first($doc, 'invoice');
        self::assertSame($expected, $invoice->getAttribute('request_id'));
        self::assertSame(['request_timestamp', 'request_id', 'request_date'], $this->attributeNames($invoice), 'No attribute may be injected through invoice_number.');
        self::assertSame(0, $doc->getElementsByTagNameNS('*', 'evil')->length);
        self::assertSame(0, $doc->getElementsByTagName('invoice:evil')->length);
    }

    #[Test]
    #[DataProvider('invoiceNumbers')]
    public function it_restricts_the_invoice_number_in_the_red_slip_payment_reason(string $input, string $expected): void
    {
        /* Arrange */
        $GLOBALS['unitCiInstance']->mdl_settings->_data['sumex_sliptype'] = '1';

        /* Act */
        $doc = $this->parse($this->sumex(['invoice_number' => $input])->xml());

        /* Assert */
        self::assertSame($expected, $this->first($doc, 'payment_reason')->textContent);
        self::assertSame(0, $doc->getElementsByTagNameNS('*', 'evil')->length);
    }

    #[Test]
    #[DataProvider('subscriberNumbers')]
    public function it_restricts_the_subscriber_number_to_digits_and_dashes_in_the_esr9_participant_number(string $input, string $expected): void
    {
        /* Act */
        $raw = $this->sumex(['user_subscribernumber' => $input])->xml();
        $doc = $this->parse($raw);

        /* Assert */
        $esr = $this->first($doc, 'esr9');
        self::assertSame($expected, $esr->getAttribute('participant_number'));
        self::assertSame(['participant_number', 'type', 'reference_number', 'coding_line'], $this->attributeNames($esr));
        self::assertSame(0, $doc->getElementsByTagName('x')->length);
    }

    #[Test]
    #[DataProvider('subscriberNumbers')]
    public function it_restricts_the_subscriber_number_in_the_red_slip_attributes(string $input, string $expected): void
    {
        /* Arrange */
        $GLOBALS['unitCiInstance']->mdl_settings->_data['sumex_sliptype'] = '1';

        /* Act */
        $doc = $this->parse($this->sumex(['user_subscribernumber' => $input])->xml());

        /* Assert */
        $esr = $this->first($doc, 'esrRed');
        self::assertSame($expected, $esr->getAttribute('post_account'));
        self::assertSame(str_replace('-', '', $expected) . '>', $esr->getAttribute('coding_line2'));
        self::assertSame(['payment_to', 'post_account', 'reference_number', 'coding_line1', 'coding_line2'], $this->attributeNames($esr));
        self::assertSame(0, $doc->getElementsByTagName('x')->length);
    }

    #[Test]
    public function it_round_trips_quotes_angle_brackets_and_newlines_in_the_item_name_attribute_without_adding_attributes(): void
    {
        /* Arrange */
        $name = "Session \"A\" <b> 'x'\n\ttail evil=\"1\"";

        /* Act */
        $doc = $this->parse($this->sumex([], [$this->item($name)])->xml());

        /* Assert */
        $record = $this->first($doc, 'record_other');
        self::assertSame($name, $record->getAttribute('name'));
        self::assertFalse($record->hasAttribute('evil'));
        self::assertSame(0, $doc->getElementsByTagName('b')->length);
    }

    #[Test]
    public function it_emits_one_record_per_item_for_hostile_item_names(): void
    {
        /* Arrange */
        $items = [$this->item('a"><record_other name="x'), $this->item('b'), $this->item("c\n<d/>")];

        /* Act */
        $doc = $this->parse($this->sumex([], $items)->xml());

        /* Assert */
        self::assertSame(3, $doc->getElementsByTagName('record_other')->length, 'A hostile item name must not add or remove records.');
        self::assertSame(0, $doc->getElementsByTagName('d')->length);
    }

    #[Test]
    public function it_places_hostile_text_only_inside_the_fields_it_was_given_to(): void
    {
        /* Arrange */
        $overrides = ['client_name' => 'Evil<invoice:injected/>', 'user_company' => "Co\x01<x/>"];

        /* Act */
        $doc   = $this->parse($this->sumex($overrides)->xml());
        $xpath = new DOMXPath($doc);

        /* Assert */
        self::assertSame(0, $xpath->query('//*[local-name()="injected" or local-name()="x"]')->length);
        self::assertGreaterThan(0, $xpath->query('//*[local-name()="givenname"][. = "Evil<invoice:injected/>"]')->length);
        self::assertGreaterThan(0, $xpath->query('//*[local-name()="companyname"][. = "Co<x/>"]')->length);
    }

    /** @param array<string, mixed> $overrides */
    private function sumex(array $overrides = [], ?array $items = null): Sumex
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
            'client_phone'          => '044 111 11 11',
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
            'sumex_observations'    => 'Rest',
        ], $overrides);

        return new Sumex(['invoice' => $invoice, 'items' => $items ?? [$this->item('Consult')], 'options' => []]);
    }

    private function item(string $name): object
    {
        return (object) [
            'item_name'     => $name,
            'product_sku'   => '120',
            'item_quantity' => '1.00',
            'item_price'    => '50.00',
            'item_total'    => '50.00',
            'item_date'     => '2026-02-03',
        ];
    }

    private function parse(string|false $xml): DOMDocument
    {
        self::assertIsString($xml);
        $previous = libxml_use_internal_errors(true);
        $doc      = new DOMDocument();
        $loaded   = $doc->loadXML($xml);
        $errors   = array_map(static fn ($e) => trim($e->message), libxml_get_errors());
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        self::assertTrue($loaded, 'Sumex must emit well-formed XML. Parser said: ' . implode(' | ', $errors));

        return $doc;
    }

    private function first(DOMDocument $doc, string $local): DOMElement
    {
        $nodes = $doc->getElementsByTagNameNS('*', $local);
        if ($nodes->length === 0) {
            $nodes = $doc->getElementsByTagName($local);
        }
        self::assertGreaterThan(0, $nodes->length, 'Expected an element named ' . $local);

        return $nodes->item(0);
    }

    /** @return list<string> */
    private function leafTexts(DOMDocument $doc): array
    {
        $texts = [];
        foreach ((new DOMXPath($doc))->query('//*[not(*)]') as $node) {
            $texts[] = $node->textContent;
        }

        return $texts;
    }

    /** @return list<string> */
    private function attributeNames(DOMElement $element): array
    {
        $names = [];
        foreach ($element->attributes as $attribute) {
            $names[] = $attribute->name;
        }

        return $names;
    }
}
