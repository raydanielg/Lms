<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEnableRegistrationBonusToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('enable_registration_bonus')->default(false)->after('disable_cashback');
            $table->float('registration_bonus_amount', 15, 2)->nullable()->after('enable_registration_bonus');
        });
    }
}
