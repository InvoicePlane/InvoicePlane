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

namespace IP\Modules\Clients\Controllers;

use IP\Http\Controllers\Controller;
use IP\Modules\Clients\Models\Contact;
use IP\Modules\Clients\Requests\ContactRequest;

class ContactController extends Controller
{
    public function create($clientId)
    {
        return view('clients._modal_contact')
            ->with('editMode', false)
            ->with('clientId', $clientId)
            ->with('submitRoute', route('clients.contacts.store', [$clientId]));
    }

    public function store(ContactRequest $request, $clientId)
    {
        Contact::create($request->all());

        return $this->loadContacts($clientId);
    }

    private function loadContacts($clientId)
    {
        return view('clients._contacts')
            ->with('clientId', $clientId)
            ->with('contacts', Contact::where('client_id', $clientId)->orderBy('name')->get());
    }

    public function edit($clientId, $id)
    {
        return view('clients._modal_contact')
            ->with('editMode', true)
            ->with('clientId', $clientId)
            ->with('submitRoute', route('clients.contacts.update', [$clientId, $id]))
            ->with('contact', Contact::find($id));
    }

    public function update(ContactRequest $request, $clientId, $id)
    {
        $contact = Contact::find($id);

        $contact->fill($request->all());

        $contact->save();

        return $this->loadContacts($clientId);
    }

    public function delete($clientId)
    {
        Contact::destroy(request('id'));

        return $this->loadContacts($clientId);
    }

    public function updateDefault($clientId)
    {
        $contact = Contact::find(request('id'));

        switch (request('default')) {
            case 'to':
                $data = [
                    'default_to' => ($contact->default_to) ? 0 : 1,
                    'default_cc' => 0,
                    'default_bcc' => 0,
                ];
                break;
            case 'cc':
                $data = [
                    'default_to' => 0,
                    'default_cc' => ($contact->default_cc) ? 0 : 1,
                    'default_bcc' => 0,
                ];
                break;
            case 'bcc':
                $data = [
                    'default_to' => 0,
                    'default_cc' => 0,
                    'default_bcc' => ($contact->default_bcc) ? 0 : 1,
                ];
                break;
        }

        $contact->fill($data);
        $contact->save();

        return $this->loadContacts($clientId);
    }
}