<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBundlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bundles', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->increments('id');
            $table->integer('creator_id')->unsigned();
            $table->integer('teacher_id')->unsigned();
            $table->integer('category_id')->unsigned()->nullable();
            $table->string('slug')->index();
            $table->string('thumbnail');
            $table->string('image_cover');
            $table->string('video_demo')->nullable();
            $table->enum('video_demo_source', \App\Models\Bundle::$videoDemoSource)->nullable();
            $table->integer('price')->nullable();
            $table->integer('points')->nullable();
            $table->boolean('subscribe')->default(false);
            $table->integer('access_days')->nullable()->comment('Number of days to access the bundle');
            $table->text('message_for_reviewer')->nullable();
            $table->enum('status', \App\Models\Bundle::$statuses);
            $table->bigInteger('created_at')->unsigned();
            $table->bigInteger('updated_at')->unsigned()->nullable();

            $table->foreign('creator_id')->on('users')->references('id')->cascadeOnDelete();
            $table->foreign('teacher_id')->on('users')->references('id')->cascadeOnDelete();
            $table->foreign('category_id')->on('categories')->references('id')->cascadeOnDelete();
        });

        Schema::create('bundle_translations', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->bigIncrements('id');
            $table->unsignedInteger('bundle_id');
            $table->string('locale', 191)->index();
            $table->string('title');
            $table->text('seo_description')->nullable();
            $table->longText('description')->nullable();

            $table->foreign('bundle_id')->on('bundles')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bundles');
        Schema::dropIfExists('bundle_translations');
    }
}
