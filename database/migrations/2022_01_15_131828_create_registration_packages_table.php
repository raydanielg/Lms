<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistrationPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registration_packages', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->increments('id');
            $table->integer('days')->unsigned();
            $table->integer('price')->unsigned();
            $table->string('icon');
            $table->enum('role', ['instructors', 'organizations'])->index();
            $table->integer('instructors_count')->nullable();
            $table->integer('students_count')->nullable();
            $table->integer('courses_capacity')->nullable();
            $table->integer('courses_count')->nullable();
            $table->integer('meeting_count')->nullable();
            $table->enum('status', ['disabled', 'active']);
            $table->integer('created_at')->unsigned();
        });

        Schema::create('registration_packages_translations', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->bigIncrements('id');
            $table->integer('registration_package_id')->unsigned();
            $table->string('locale', 191)->index();
            $table->string('title');
            $table->string('description')->nullable();

            $table->foreign('registration_package_id', 'registration_package')->on('registration_packages')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registration_packages');
        Schema::dropIfExists('registration_packages_translations');
    }
}
