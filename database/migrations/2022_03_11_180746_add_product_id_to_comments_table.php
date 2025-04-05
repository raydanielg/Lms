<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductIdToCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->integer('product_id')->nullable()->unsigned()->after('blog_id');
            $table->integer('product_review_id')->nullable()->unsigned()->after('product_id');

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }
}
