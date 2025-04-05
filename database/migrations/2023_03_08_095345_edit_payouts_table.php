<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class EditPayoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payouts', function (Blueprint $table) {
            DB::statement("ALTER TABLE `payouts`
                    DROP COLUMN `account_name`,
                    DROP COLUMN `account_number`,
                    DROP COLUMN `account_bank_name`");

            $table->integer('user_selected_bank_id')->unsigned()->after('user_id')->nullable();

            $table->foreign('user_selected_bank_id', 'payout_user_selected_bank_id')->on('user_selected_banks')->references('id')->cascadeOnDelete();
        });
    }
}
