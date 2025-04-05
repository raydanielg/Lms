<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class EditCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('certificates', function (Blueprint $table) {
            DB::statement("ALTER TABLE `certificates`
                        DROP COLUMN `file`");

            DB::statement("ALTER TABLE `certificates`
                                MODIFY COLUMN `quiz_id` int(0) UNSIGNED NULL AFTER `id`,
                                MODIFY COLUMN `quiz_result_id` int(0) UNSIGNED NULL AFTER `quiz_id`");

            $table->enum('type', ['quiz', 'course'])->after('user_grade');
            $table->integer('webinar_id')->unsigned()->after('student_id')->nullable();

            $table->foreign('webinar_id')->on('webinars')->references('id')->cascadeOnDelete();
        });
    }
}
