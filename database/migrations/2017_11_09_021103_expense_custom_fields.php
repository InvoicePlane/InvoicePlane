<?php

use FI\Modules\CustomFields\Models\ExpenseCustom;
use FI\Modules\Expenses\Models\Expense;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ExpenseCustomFields extends Migration
{
    public function up()
    {
        Schema::create('expenses_custom', function (Blueprint $table)
        {
            $table->integer('expense_id');
            $table->timestamps();

            $table->primary('expense_id');
        });

        foreach (Expense::get() as $expense)
        {
            $expense->custom()->save(new ExpenseCustom());
        }
    }

    public function down()
    {
        //
    }
}
