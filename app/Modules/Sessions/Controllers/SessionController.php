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

namespace IP\Modules\Sessions\Controllers;

use IP\Http\Controllers\Controller;
use IP\Modules\Sessions\Requests\SessionRequest;

class SessionController extends Controller
{
    public function login()
    {
        deleteTempFiles();
        deleteViewCache();

        return view('sessions.login')->with('skin', config('ip.skin'));
    }

    public function attempt(SessionRequest $request)
    {
        $rememberMe = ($request->input('remember_me')) ? true : false;

        if (!auth()->attempt(['email' => $request->input('email'), 'password' => $request->input('password')], $rememberMe)) {
            return redirect()->route('session.login')->with('error', trans('ip.invalid_credentials'));
        }

        if (!auth()->user()->client_id) {
            return redirect()->route('dashboard.index');
        }

        return redirect()->route('clientCenter.dashboard');

    }

    public function logout()
    {
        auth()->logout();

        session()->flush();

        return redirect()->route('session.login');
    }
}