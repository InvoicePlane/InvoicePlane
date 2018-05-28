<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\CustomFields\Models;

use FI\Traits\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class CustomField extends Model
{
    use Sortable;

    protected $guarded = ['id'];

    protected $sortable = ['tbl_name', 'column_name', 'field_label', 'field_type'];

    /*
    |--------------------------------------------------------------------------
    | Static Methods
    |--------------------------------------------------------------------------
    */

    public static function getNextColumnName($tableName)
    {
        $currentColumn = self::where('tbl_name', '=', $tableName)->orderBy('id', 'DESC')->take(1)->first();

        if (!$currentColumn)
        {
            return 'column_1';
        }
        else
        {
            $column = explode('_', $currentColumn->column_name);

            return $column[0] . '_' . ($column[1] + 1);
        }
    }

    public static function createCustomColumn($tableName, $columnName, $fieldType)
    {
        if (substr($tableName, -7) <> '_custom')
        {
            $tableName = $tableName . '_custom';
        }

        Schema::table($tableName, function ($table) use ($columnName, $fieldType)
        {
            if ($fieldType == 'textarea')
            {
                $table->text($columnName)->nullable();
            }
            else
            {
                $table->string($columnName)->nullable();
            }

        });
    }

    public static function deleteCustomColumn($tableName, $columnName)
    {
        if (substr($tableName, -7) <> '_custom')
        {
            $tableName = $tableName . '_custom';
        }

        if (Schema::hasColumn($tableName, $columnName))
        {
            Schema::table($tableName, function ($table) use ($columnName)
            {
                $table->dropColumn($columnName);
            });
        }
    }

    public static function copyCustomFieldValues($fromModel, $toModel)
    {
        $commonFields = [];
        $fromFields   = self::forTable($fromModel->getTable())->get();
        $toFields     = self::forTable($toModel->getTable())->get();

        foreach ($fromFields as $fromField)
        {
            $toField = $toFields->where('field_label', $fromField->field_label)->first();

            if ($toField)
            {
                $commonFields[$toField->column_name] = $fromModel->custom->{$fromField->column_name};
            }
        }

        if ($commonFields)
        {
            $toModel->custom->update($commonFields);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeForTable($query, $table)
    {
        return $query->where('tbl_name', '=', $table);
    }
}