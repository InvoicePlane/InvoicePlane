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