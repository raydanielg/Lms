<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddBundleIdToNeedleTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tags', function (Blueprint $table) {
            DB::statement("ALTER TABLE `tags` MODIFY COLUMN `webinar_id` int(0) UNSIGNED NULL AFTER `title`");
            $table->integer('bundle_id')->unsigned()->nullable()->after('webinar_id');

            $table->foreign('bundle_id')->on('bundles')->references('id')->cascadeOnDelete();
        });

        Schema::table('tickets', function (Blueprint $table) {
            DB::statement("ALTER TABLE `tickets` MODIFY COLUMN `webinar_id` int(0) UNSIGNED NULL AFTER `creator_id`");
            $table->integer('bundle_id')->unsigned()->nullable()->after('webinar_id');

            $table->foreign('bundle_id')->on('bundles')->references('id')->cascadeOnDelete();
        });

        Schema::table('faqs', function (Blueprint $table) {
            DB::statement("ALTER TABLE `faqs` MODIFY COLUMN `webinar_id` int(0) UNSIGNED NULL AFTER `creator_id`");
            $table->integer('bundle_id')->unsigned()->nullable()->after('webinar_id');

            $table->foreign('bundle_id')->on('bundles')->references('id')->cascadeOnDelete();
        });

        Schema::table('favorites', function (Blueprint $table) {
            DB::statement("ALTER TABLE `favorites` MODIFY COLUMN `webinar_id` int(0) UNSIGNED NULL AFTER `user_id`");
            $table->integer('bundle_id')->unsigned()->nullable()->after('webinar_id');

            $table->foreign('bundle_id')->on('bundles')->references('id')->cascadeOnDelete();
        });

        Schema::table('advertising_banners',function (Blueprint $table) {
            DB::statement("ALTER TABLE `advertising_banners` MODIFY COLUMN `position` enum('home1','home2','course','course_sidebar','product_show','bundle', 'bundle_sidebar') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `id`");
        });

        Schema::table('special_offers', function (Blueprint $table) {
            DB::statement("ALTER TABLE `special_offers` MODIFY COLUMN `webinar_id` int(0) UNSIGNED NULL AFTER `creator_id`");
            $table->integer('bundle_id')->unsigned()->nullable()->after('webinar_id');

            $table->foreign('bundle_id')->on('bundles')->references('id')->cascadeOnDelete();
        });

        Schema::table('webinar_reviews', function (Blueprint $table) {
            DB::statement("ALTER TABLE `webinar_reviews` MODIFY COLUMN `webinar_id` int(0) UNSIGNED NULL AFTER `creator_id`");
            $table->integer('bundle_id')->unsigned()->nullable()->after('webinar_id');

            $table->foreign('bundle_id')->on('bundles')->references('id')->cascadeOnDelete();
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->integer('bundle_id')->unsigned()->nullable()->after('webinar_id');

            $table->foreign('bundle_id')->on('bundles')->references('id')->cascadeOnDelete();
        });

        Schema::table('sales', function (Blueprint $table) {
            DB::statement("ALTER TABLE `sales` MODIFY COLUMN `type` enum('webinar','meeting','subscribe','promotion','registration_package','product','bundle') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `registration_package_id`");
            $table->integer('bundle_id')->unsigned()->nullable()->after('webinar_id');
        });

        Schema::table('cart', function (Blueprint $table) {
            $table->integer('bundle_id')->unsigned()->nullable()->after('webinar_id');

            $table->foreign('bundle_id')->on('bundles')->references('id')->cascadeOnDelete();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->integer('bundle_id')->unsigned()->nullable()->after('webinar_id');
        });

        Schema::table('accounting', function (Blueprint $table) {
            $table->integer('bundle_id')->unsigned()->nullable()->after('webinar_id');
        });

        Schema::table('subscribe_uses', function (Blueprint $table) {
            DB::statement("ALTER TABLE `subscribe_uses` MODIFY COLUMN `webinar_id` int(0) UNSIGNED NULL AFTER `subscribe_id`");
            $table->integer('bundle_id')->unsigned()->nullable()->after('webinar_id');

            $table->foreign('bundle_id')->on('bundles')->references('id')->cascadeOnDelete();
        });
    }
}
