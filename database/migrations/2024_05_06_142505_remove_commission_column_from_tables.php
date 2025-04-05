<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            DB::statement("ALTER TABLE `users` DROP COLUMN `commission`");
        });

        Schema::table('groups', function (Blueprint $table) {
            DB::statement("ALTER TABLE `groups` DROP COLUMN `commission`");
        });

        Schema::table('products', function (Blueprint $table) {
            $table->enum('commission_type', ['percent', 'fixed_amount'])->after('tax');
        });
    }

};
