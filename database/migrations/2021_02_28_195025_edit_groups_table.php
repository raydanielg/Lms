<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('groups', function (Blueprint $table) {
            DB::statement("ALTER TABLE `groups` DROP COLUMN `percent`");

            $table->integer('discount')->nullable()->after('name');
            $table->integer('commission')->nullable()->after('discount');
            $table->enum('status', ['active', 'inactive'])->default('inactive')->after('commission');
        });
    }
}
