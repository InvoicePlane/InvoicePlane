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

namespace IP\Modules\Users\Controllers;

use IP\Http\Controllers\Controller;
use IP\Modules\Clients\Models\Client;
use IP\Modules\CustomFields\Models\CustomField;
use IP\Modules\Users\Models\User;
use IP\Modules\Users\Requests\UserStoreRequest;
use IP\Modules\Users\Requests\UserUpdateRequest;
use IP\Traits\ReturnUrl;

class UserController extends Controller
{
    use ReturnUrl;

    public function index()
    {
        $this->setReturnUrl();

        $users = User::sortable(['name' => 'asc'])->userType(request('userType'))->paginate(config('ip.resultsPerPage'));

        return view('users.index')
            ->with('users', $users)
            ->with('userTypes', [
                '' => trans('ip.all_accounts'),
                'admin' => trans('ip.admin_accounts'),
                'client' => trans('ip.client_accounts'),
            ]);
    }

    public function create($userType)
    {
        $view = view('users.' . $userType . '_form')
            ->with('editMode', false)
            ->with('customFields', CustomField::forTable('users')->get());

        if ($userType == 'client') {
            $view->with('clients', Client::whereDoesntHave('user')
                ->where('email', '<>', '')
                ->whereNotNull('email')
                ->where('active', 1)
                ->orderBy('unique_name')
                ->pluck('unique_name', 'id')
                ->toArray()
            );
        }

        return $view;
    }

    public function store(UserStoreRequest $request, $userType)
    {
        $user = new User($request->except('custom'));

        $user->password = $request->input('password');

        $user->save();

        $user->custom->update($request->input('custom', []));

        return redirect($this->getReturnUrl())
            ->with('alertSuccess', trans('ip.record_successfully_created'));
    }

    public function edit($id, $userType)
    {
        $user = User::find($id);

        return view('users.' . $userType . '_form')
            ->with(['editMode' => true, 'user' => $user])
            ->with('customFields', CustomField::forTable('users')->get());
    }

    public function update(UserUpdateRequest $request, $id, $userType)
    {
        $user = User::find($id);

        $user->fill($request->except('custom'));

        $user->save();

        $user->custom->update($request->input('custom', []));

        return redirect($this->getReturnUrl())
            ->with('alertInfo', trans('ip.record_successfully_updated'));
    }

    public function delete($id)
    {
        User::destroy($id);

        return redirect()->route('users.index')
            ->with('alert', trans('ip.record_successfully_deleted'));
    }

    public function getClientInfo()
    {
        return Client::find(request('id'));
    }
}
