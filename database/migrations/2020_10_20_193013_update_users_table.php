<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            DB::statement('ALTER TABLE `users`
                MODIFY COLUMN `mobile` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `role_id`,
                MODIFY COLUMN `avatar` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `remember_token`,
                MODIFY COLUMN `headline` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `avatar`,
                MODIFY COLUMN `about` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL AFTER `headline`');


            $table->boolean('identity')->default(false)->after('remember_token');
            $table->string('cover_img','128')->nullable()->after('avatar');
            $table->string('language','128')->nullable()->after('status');
            $table->boolean('newsletter')->default(false)->after('language');
            $table->boolean('public_message')->default(false)->after('newsletter');
            $table->string('account_type',128)->nullable()->after('public_message');
            $table->string('iban',128)->nullable()->after('account_type');
            $table->string('account_id',128)->nullable()->after('iban');
            $table->string('identity_scan',128)->nullable()->after('account_id');
            $table->string('address')->nullable()->after('about');
        });
    }
}
