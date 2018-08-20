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

namespace IP\Modules\Setup\Controllers;

use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use IP\Http\Controllers\Controller;
use IP\Modules\CompanyProfiles\Models\CompanyProfile;
use IP\Modules\Settings\Models\Setting;
use IP\Modules\Setup\Requests\DBRequest;
use IP\Modules\Setup\Requests\ProfileRequest;
use IP\Modules\Users\Models\User;
use IP\Support\Migrations;

class SetupController extends Controller
{
    private $migrations;

    public function __construct(Migrations $migrations)
    {
        $this->migrations = $migrations;
    }

    public function index()
    {
        return view('setup.index');
    }

    public function postIndex()
    {
        return redirect()->route('setup.prerequisites');
    }

    public function prerequisites()
    {
        $errors = [];
        $versionRequired = '7.0.0';

        if (version_compare(phpversion(), $versionRequired, '<')) {
            $errors[] = sprintf(trans('ip.php_version_error'), $versionRequired);
        }

        if (!$errors) {
            // Write a new app key to the env before continuing
            if (config('app.key') === 'SomeRandomStringWith32Characters') {
                $new_key = 'base64:' . base64_encode(
                        Encrypter::generateKey(config('app.cipher'))
                    );

                self::writeEnv(['APP_KEY' => $new_key]);
            }

            return redirect()->route('setup.dbconfig');
        }

        return view('setup.prerequisites')
            ->with('errors', $errors);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function dbConfig()
    {
        return view('setup.dbconfig');
    }

    /**
     * @param DBRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function postDbConfig(DBRequest $request)
    {
        $input = $request->all();

        // Create test configuration with new credentials
        config(['database.connections.setup' => config('database.connections.mysql')]);
        config(['database.connections.setup.host' => $input['db_host']]);
        config(['database.connections.setup.port' => $input['db_port']]);
        config(['database.connections.setup.database' => $input['db_database']]);
        config(['database.connections.setup.username' => $input['db_username']]);
        config(['database.connections.setup.password' => $input['db_password']]);

        // Check the new database config
        try {
            DB::connection('setup')->getPdo();
        } catch (\Exception $e) {
            return view('setup.dbconfig')->with('errors', collect(trans('ip.database_configuration_failed')));
        }

        // Save the new credentials to the .env file
        self::writeEnv($input);

        return redirect()->route('setup.migration');
    }

    public function migration()
    {
        return view('setup.migration');
    }

    public function postMigration()
    {
        if ($this->migrations->runMigrations(database_path('migrations'))) {
            return response()->json([], 200);
        }

        return response()->json(['exception' => $this->migrations->getException()->getMessage()], 400);
    }

    public function account()
    {
        if (!User::count()) {
            return view('setup.account');
        }

        return redirect()->route('setup.complete');
    }

    public function postAccount(ProfileRequest $request)
    {
        if (!User::count()) {
            $input = request()->except('password_confirmation');

            $user = new User($input['user']);
            $user->password = $input['user']['password'];
            $user->save();

            $companyProfile = CompanyProfile::create($input['company_profile']);

            Setting::saveByKey('defaultCompanyProfile', $companyProfile->id);
        }

        return redirect()->route('setup.complete');
    }

    public function complete()
    {
        return view('setup.complete');
    }

    /**
     * Write values to the .env file base on an key => value array
     *
     * @param array $data
     */
    private function writeEnv(array $data)
    {
        $config = file_get_contents(base_path('.env'));

        foreach ($data as $key => $value) {
            $key = strtoupper($key);
            $config = preg_replace("/$key=(.*)?/", "$key=" . $value, $config);
        }

        Storage::disk('base')->put('.env', $config);
    }
}
