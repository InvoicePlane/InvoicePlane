<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RenameInvoiceGroups extends Migration
{
    public function up()
    {
        // Rename the invoice groups table to just groups
        Schema::rename('invoice_groups', 'groups');

        Schema::table('invoices', function (Blueprint $table)
        {
            $table->renameColumn('invoice_group_id', 'group_id');
        });

        Schema::table('quotes', function (Blueprint $table)
        {
            $table->renameColumn('invoice_group_id', 'group_id');
        });
    }

    public function down()
    {
        //
    }
}
