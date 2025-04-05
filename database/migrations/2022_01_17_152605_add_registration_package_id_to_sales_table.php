<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \Illuminate\Support\Facades\DB;

class AddRegistrationPackageIdToSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->integer('registration_package_id')->unsigned()->nullable()->after('promotion_id');

            DB::statement("ALTER TABLE `sales` MODIFY COLUMN `type` enum('webinar','meeting','subscribe','promotion','registration_package') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `registration_package_id`");
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->integer('registration_package_id')->unsigned()->nullable()->after('promotion_id');
        });

        Schema::table('accounting', function (Blueprint $table) {
            $table->integer('registration_package_id')->unsigned()->nullable()->after('promotion_id');

            DB::statement("ALTER TABLE `accounting` MODIFY COLUMN `type_account` enum('income','asset','subscribe','promotion','registration_package') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `type`");
        });
    }
}
