<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusColumnToPaymentChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_channels', function (Blueprint $table) {
            DB::statement("ALTER TABLE `payment_channels` DROP COLUMN `disabled_at`");
            $table->enum('status', ['active', 'inactive'])->after('class_name');
        });
    }
}
