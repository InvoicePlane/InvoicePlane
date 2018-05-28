<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Addons\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\Addons\Models\Addon;
use FI\Support\Directory;
use FI\Support\Migrations;

class AddonController extends Controller
{
    private $migrations;

    public function __construct(Migrations $migrations)
    {
        $this->migrations = $migrations;
    }

    public function index()
    {
        $this->refreshList();

        return view('addons.index')
            ->with('addons', Addon::orderBy('name')->get());
    }

    public function install($id)
    {
        $addon = Addon::find($id);

        $migrator = app('migrator');

        $migrator->run(addon_path($addon->path . '/Migrations'));

        $addon->enabled = 1;

        $addon->save();

        return redirect()->route('addons.index');
    }

    public function upgrade($id)
    {
        $addon = Addon::find($id);

        $this->migrations->runMigrations(addon_path($addon->path . '/Migrations'));

        return redirect()->route('addons.index');
    }

    public function uninstall($id)
    {
        Addon::destroy($id);

        return redirect()->route('addons.index');
    }

    private function refreshList()
    {
        $addons = Directory::listDirectories(addon_path());

        foreach ($addons as $addon)
        {
            $setupClass = 'Addons\\' . $addon . '\Setup';

            $setupClass = new $setupClass;

            $addonRecord = $setupClass->properties;

            if (!Addon::where('name', $addonRecord['name'])->count())
            {
                $addonRecord['path'] = $addon;

                Addon::create($addonRecord);
            }
        }
    }
}