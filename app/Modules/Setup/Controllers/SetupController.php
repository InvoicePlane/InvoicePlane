<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Setup\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\CompanyProfiles\Models\CompanyProfile;
use FI\Modules\Settings\Models\Setting;
use FI\Modules\Setup\Requests\LicenseRequest;
use FI\Modules\Setup\Requests\ProfileRequest;
use FI\Modules\Users\Models\User;
use FI\Support\Migrations;

class SetupController extends Controller
{
    private $migrations;

    public function __construct(Migrations $migrations)
    {
        $this->migrations = $migrations;
    }

    public function index()
    {
        return view('setup.index')
            ->with('license', file_get_contents(public_path('LICENSE')));
    }

    public function postIndex(LicenseRequest $request)
    {
        return redirect()->route('setup.prerequisites');
    }

    public function prerequisites()
    {
        $errors          = [];
        $versionRequired = '5.5.9';
        $dbDriver        = config('database.default');
        $dbConfig        = config('database.connections.' . $dbDriver);

        if (version_compare(phpversion(), $versionRequired, '<'))
        {
            $errors[] = sprintf(trans('fi.php_version_error'), $versionRequired);
        }

        if (!$dbConfig['host'] or !$dbConfig['database'] or !$dbConfig['username'] or !$dbConfig['password'])
        {
            $errors[] = trans('fi.database_not_configured');
        }

        if (!$errors)
        {
            return redirect()->route('setup.migration');
        }

        return view('setup.prerequisites')
            ->with('errors', $errors);
    }

    public function migration()
    {
        return view('setup.migration');
    }

    public function postMigration()
    {
        if ($this->migrations->runMigrations(database_path('migrations')))
        {
            return response()->json([], 200);
        }

        return response()->json(['exception' => $this->migrations->getException()->getMessage()], 400);
    }

    public function account()
    {
        if (!User::count())
        {
            return view('setup.account');
        }

        return redirect()->route('setup.complete');
    }

    public function postAccount(ProfileRequest $request)
    {
        if (!User::count())
        {
            $input = request()->all();

            unset($input['user']['password_confirmation']);

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
}