<?php

/**
 * InvoicePlane
 *
 * @package     InvoicePlane
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (C) 2014 - 2018 InvoicePlane
 * @license     https://invoiceplane.com/license
 * @link        https://invoiceplane.com
 *
 * Based on FusionInvoice by Jesse Terry (FusionInvoice, LLC)
 */

namespace IP\Support;

use Collective\Html\FormFacade;
use IP\Modules\Clients\Models\Client;

class Contacts
{
    private $client;
    private $user;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->user = auth()->user();
    }

    public function contactDropdownTo()
    {
        $allContacts = $this->getAllContacts();
        $selectedContacts = $this->getSelectedContactsTo();

        return FormFacade::select('to', $allContacts, $selectedContacts, ['id' => 'to', 'multiple' => 'multiple', 'class' => 'form-control']);
    }

    private function getAllContacts()
    {
        $contacts = ($this->client->email) ? [$this->client->email => $this->getFormattedContact($this->client->name, $this->client->email)] : [];

        foreach ($this->client->contacts->pluck('name', 'email') as $email => $name) {
            $contacts[$email] = $this->getFormattedContact($name, $email);
        }

        $contacts[$this->user->email] = $this->getFormattedContact($this->user->name, $this->user->email);

        if (config('fi.mailDefaultCc')) {
            $contacts[config('fi.mailDefaultCc')] = config('fi.mailDefaultCc');
        }

        if (config('fi.mailDefaultBcc')) {
            $contacts[config('fi.mailDefaultBcc')] = config('fi.mailDefaultBcc');
        }

        return $contacts;
    }

    private function getFormattedContact($name, $email)
    {
        return $name . ' <' . $email . '>';
    }

    public function getSelectedContactsTo()
    {
        return $this->client->contacts->where('default_to', 1)->pluck('email')->prepend($this->client->email);
    }

    public function contactDropdownCc()
    {
        $allContacts = $this->getAllContacts();
        $selectedContacts = $this->getSelectedContactsCc();

        return FormFacade::select('cc', $allContacts, $selectedContacts, ['id' => 'cc', 'multiple' => 'multiple', 'class' => 'form-control']);
    }

    public function getSelectedContactsCc()
    {
        $contacts = $this->client->contacts
            ->where('default_cc', 1)
            ->pluck('email')
            ->toArray();

        if (config('fi.mailDefaultCc')) {
            $contacts = array_merge($contacts, [config('fi.mailDefaultCc')]);
        }

        return $contacts;
    }

    public function contactDropdownBcc()
    {
        $allContacts = $this->getAllContacts();
        $selectedContacts = $this->getSelectedContactsBcc();

        return FormFacade::select('bcc', $allContacts, $selectedContacts, ['id' => 'bcc', 'multiple' => 'multiple', 'class' => 'form-control']);
    }

    public function getSelectedContactsBcc()
    {
        $contacts = $this->client->contacts
            ->where('default_bcc', 1)
            ->pluck('email')
            ->toArray();

        if (config('fi.mailDefaultBcc')) {
            $contacts = array_merge($contacts, [config('fi.mailDefaultBcc')]);
        }

        return $contacts;
    }
}
