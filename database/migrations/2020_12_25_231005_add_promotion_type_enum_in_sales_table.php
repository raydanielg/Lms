<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPromotionTypeEnumInSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            DB::statement("ALTER TABLE `sales` MODIFY COLUMN `type` ENUM('webinar','meeting','subscribe', 'promotion') NOT NULL;");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            DB::statement("ALTER TABLE `sales` MODIFY COLUMN `type` ENUM('webinar','meeting','subscribe') NOT NULL;");
        });
    }
}
