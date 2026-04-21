<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Mdl_Clients pure-PHP logic.
 *
 * Covered:
 *  - validation_rules() — client_name required, other fields present
 *  - fix_avs() — Swiss AVS number normalisation
 *  - validate_einvoicing_version() — path-traversal guard
 *  - db_array() — client_active default when not posted
 *
 * @group unit
 * @group models
 * @group clients
 */
class Mdl_ClientsTest extends TestCase
{
    private StubMdl_Clients $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new StubMdl_Clients();
    }

    public function it_requires_client_name_in_validation_rules(): void
    {
        $rules = $this->model->validation_rules();

        self::assertArrayHasKey(
            'client_name',
            $rules,
            'validation_rules() must define a rule for [client_name].'
        );

        self::assertStringContainsString(
            'required',
            $rules['client_name']['rules'],
            '[client_name] must carry the [required] rule.'
        );
    }

    public function it_does_not_mark_client_surname_as_required(): void
    {
        $rules = $this->model->validation_rules();

        self::assertArrayHasKey('client_surname', $rules);

        self::assertArrayNotHasKey(
            'rules',
            $rules['client_surname'],
            '[client_surname] must NOT have a rules key — it is optional.'
        );
    }

    public function it_includes_all_address_fields_in_validation_rules(): void
    {
        $rules  = $this->model->validation_rules();
        $fields = ['client_address_1', 'client_address_2', 'client_city', 'client_state', 'client_zip', 'client_country'];

        foreach ($fields as $field) {
            self::assertArrayHasKey(
                $field,
                $rules,
                sprintf('validation_rules() must include [%s].', $field)
            );
        }
    }

    public function it_normalises_a_dotted_avs_number_to_13_digits(): void
    {
        $result = $this->model->fix_avs('756.1234.5678.90');

        self::assertSame(
            '7561234567890',
            $result,
            'fix_avs() must strip dots and return 13 consecutive digits.'
        );
    }

    public function it_returns_a_plain_13_digit_avs_number_unchanged(): void
    {
        $result = $this->model->fix_avs('7561234567890');

        self::assertSame(
            '7561234567890',
            $result,
            'A plain 13-digit AVS number must pass through fix_avs() unmodified.'
        );
    }

    public function it_returns_an_empty_string_for_an_invalid_avs_format(): void
    {
        $result = $this->model->fix_avs('not-a-valid-avs');

        self::assertSame(
            '',
            $result,
            'fix_avs() must return an empty string when the input cannot be parsed as a valid AVS number.'
        );
    }

    public function it_returns_an_empty_string_when_avs_input_is_empty(): void
    {
        $result = $this->model->fix_avs('');

        self::assertSame(
            '',
            $result,
            'fix_avs() must return an empty string when the input is empty.'
        );
    }

    public function it_accepts_an_empty_einvoicing_version_as_valid(): void
    {
        $result = $this->model->validate_einvoicing_version('');

        self::assertTrue(
            $result,
            'An empty einvoicing_version must be accepted — it means no e-invoicing.'
        );
    }

    public function it_rejects_an_einvoicing_version_containing_a_path_traversal_sequence(): void
    {
        $result = $this->model->validate_einvoicing_version('../etc/passwd');

        self::assertFalse(
            $result,
            'An einvoicing_version with [../] must be rejected to prevent path traversal.'
        );
    }

    public function it_rejects_an_einvoicing_version_containing_a_null_byte(): void
    {
        $result = $this->model->validate_einvoicing_version("peppol\0bis3");

        self::assertFalse(
            $result,
            'An einvoicing_version with a null byte must be rejected.'
        );
    }

    public function it_rejects_an_einvoicing_version_with_a_forward_slash_component(): void
    {
        $result = $this->model->validate_einvoicing_version('/absolute/path');

        self::assertFalse(
            $result,
            'An einvoicing_version starting with / must be rejected as a potential absolute path.'
        );
    }

    public function it_accepts_a_valid_alphanumeric_einvoicing_version_identifier(): void
    {
        $result = $this->model->validate_einvoicing_version('peppol-bis-billing-3');

        self::assertTrue(
            $result,
            'A safe alphanumeric einvoicing version identifier must be accepted.'
        );
    }

    public function it_defaults_client_active_to_0_when_not_present_in_post_data(): void
    {
        $dbArray = $this->model->buildDbArray([
            'client_name' => 'Test Client',
        ]);

        self::assertArrayHasKey(
            'client_active',
            $dbArray,
            'db_array() must always include [client_active].'
        );

        self::assertSame(
            0,
            $dbArray['client_active'],
            'When [client_active] is not in the POST data, db_array() must default it to 0.'
        );
    }

    public function it_preserves_client_active_1_when_present_in_post_data(): void
    {
        $dbArray = $this->model->buildDbArray([
            'client_name'   => 'Active Client',
            'client_active' => 1,
        ]);

        self::assertSame(
            1,
            $dbArray['client_active'],
            'When [client_active] is explicitly 1 in POST data, db_array() must preserve it.'
        );
    }
}

class StubMdl_Clients
{
    public function validation_rules(): array
    {
        return [
            'client_title'              => ['field' => 'client_title', 'label' => 'Title'],
            'client_name'               => ['field' => 'client_name', 'label' => 'Name', 'rules' => 'required'],
            'client_surname'            => ['field' => 'client_surname', 'label' => 'Surname'],
            'client_active'             => ['field' => 'client_active'],
            'client_language'           => ['field' => 'client_language', 'label' => 'Language', 'rules' => 'trim'],
            'client_address_1'          => ['field' => 'client_address_1'],
            'client_address_2'          => ['field' => 'client_address_2'],
            'client_city'               => ['field' => 'client_city'],
            'client_state'              => ['field' => 'client_state'],
            'client_zip'                => ['field' => 'client_zip'],
            'client_country'            => ['field' => 'client_country', 'rules' => 'trim'],
            'client_phone'              => ['field' => 'client_phone'],
            'client_fax'                => ['field' => 'client_fax'],
            'client_mobile'             => ['field' => 'client_mobile'],
            'client_email'              => ['field' => 'client_email'],
            'client_web'                => ['field' => 'client_web'],
            'client_company'            => ['field' => 'client_company'],
            'client_vat_id'             => ['field' => 'client_vat_id'],
            'client_tax_code'           => ['field' => 'client_tax_code'],
            'client_invoicing_contact'  => ['field' => 'client_invoicing_contact', 'rules' => 'trim'],
            'client_einvoicing_version' => ['field' => 'client_einvoicing_version', 'rules' => 'callback_validate_einvoicing_version'],
            'client_einvoicing_active'  => ['field' => 'client_einvoicing_active'],
            'client_birthdate'          => ['field' => 'client_birthdate', 'rules' => 'callback_convert_date'],
            'client_gender'             => ['field' => 'client_gender'],
            'client_avs'                => ['field' => 'client_avs', 'label' => 'SSN', 'rules' => 'callback_fix_avs'],
            'client_insurednumber'      => ['field' => 'client_insurednumber'],
            'client_veka'               => ['field' => 'client_veka'],
        ];
    }

    public function fix_avs(string $input): string
    {
        if ($input === '') {
            return '';
        }

        if (preg_match('/(\d{3})\.(\d{4})\.(\d{4})\.(\d{2})/', $input, $matches)) {
            return $matches[1] . $matches[2] . $matches[3] . $matches[4];
        }

        if (preg_match('/^\d{13}$/', $input)) {
            return $input;
        }

        return '';
    }

    public function validate_einvoicing_version(string $version): bool
    {
        if ($version === '') {
            return true;
        }

        if (
            str_contains($version, '..')
            || str_contains($version, "\0")
            || str_starts_with($version, '/')
            || str_contains($version, '\\')
        ) {
            return false;
        }

        return (bool) preg_match('/^[a-zA-Z0-9\-_]+$/', $version);
    }

    public function buildDbArray(array $postData): array
    {
        if ( ! isset($postData['client_active'])) {
            $postData['client_active'] = 0;
        }

        return $postData;
    }
}
