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

namespace FI\Modules\Users\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\Users\Models\User;
use FI\Modules\Users\Requests\UpdatePasswordRequest;
use FI\Traits\ReturnUrl;

class UserPasswordController extends Controller
{
    use ReturnUrl;

    public function edit($id)
    {
        return view('users.password_form')
            ->with('user', User::find($id));
    }

    public function update(UpdatePasswordRequest $request, $id)
    {
        $user = User::find($id);

        $user->password = $request->input('password');

        $user->save();

        return redirect($this->getReturnUrl())
            ->with('alertInfo', trans('fi.password_successfully_reset'));
    }
}