<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificateTemplateTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificate_template_translations', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->bigIncrements('id');
            $table->unsignedInteger('certificate_template_id');
            $table->string('locale', 191)->index();
            $table->string('title')->nullable();
            $table->text('body')->nullable();
            $table->boolean('rtl')->default(false);

            $table->foreign('certificate_template_id', 'certificate_template_id')->on('certificates_templates')->references('id')->onDelete('cascade');
        });

        Schema::table('certificates_templates', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('body');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('certificate_template_translations');
    }
}
