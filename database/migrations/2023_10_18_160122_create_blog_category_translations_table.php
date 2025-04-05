<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog_category_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('blog_category_id');
            $table->string('locale', 191)->index();
            $table->string('title');

            $table->foreign('blog_category_id')->references('id')->on('blog_categories')->onDelete('cascade');
        });

        $this->moveTitles();

        Schema::table('blog_categories', function (Blueprint $table) {
            $table->dropColumn('title');
        });
    }

    private function moveTitles()
    {
        /*$categories = \App\Models\BlogCategory::query()->get();
        $locale = app()->getLocale();
        $insert = [];
        foreach ($categories as $category) {
            $insert[] = [
                'blog_category_id' => $category->id,
                'locale' => mb_strtolower($locale),
                'title' => $category->title,
            ];
        }

        \App\Models\Translation\BlogCategoryTranslation::query()->insert($insert);*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blog_category_translations');
    }
};
