<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class Viewed extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table)
        {
            $table->boolean('viewed')->default(0);
        });

        Schema::table('quotes', function (Blueprint $table)
        {
            $table->boolean('viewed')->default(0);
        });

        DB::table('invoices')->whereIn('id', function ($query)
        {
            $query->select('audit_id')->from('activities')->where('audit_type', 'FI\Modules\Invoices\Models\Invoice');
        })->update(['viewed' => 1]);

        DB::table('quotes')->whereIn('id', function ($query)
        {
            $query->select('audit_id')->from('activities')->where('audit_type', 'FI\Modules\Quotes\Models\Quote');
        })->update(['viewed' => 1]);
    }

    public function down()
    {
        //
    }
}