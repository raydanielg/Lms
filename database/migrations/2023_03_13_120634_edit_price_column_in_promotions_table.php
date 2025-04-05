<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class EditPriceColumnInPromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promotions', function (Blueprint $table) {
            DB::statement("ALTER TABLE `promotions` MODIFY COLUMN `price` double(15, 2) UNSIGNED NOT NULL AFTER `days`");
        });

        Schema::table('sales', function (Blueprint $table) {
            DB::statement("ALTER TABLE `sales` MODIFY COLUMN `amount` decimal(13, 2) UNSIGNED NOT NULL AFTER `type`");
        });
    }
}
