<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFinancialApprovalColumnToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('financial_approval')->default(false)->after('verified');
            $table->boolean('installment_approval')->default(false)->after('financial_approval');
            $table->boolean('enable_installments')->default(true)->after('installment_approval');
            $table->boolean('disable_cashback')->default(false)->after('enable_installments');
        });
    }
}
