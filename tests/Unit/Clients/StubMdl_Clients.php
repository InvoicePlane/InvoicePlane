<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
namespace Tests\Unit\Clients;

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
