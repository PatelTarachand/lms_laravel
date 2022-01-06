<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTCourseEnrollTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_course_enroll', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('id')->autoIncrement();
            $table->string('enroll_id', 50)->nullable()->default(null);
			$table->integer('student_id');
            $table->string('course_code',50)->nullable();
            $table->integer('batch_id')->nullable();
            $table->string('batch_code',50)->nullable();
            $table->date('batch_join_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->char('record_status',4)->comment('A for active D for deactive U for update.');
            $table->boolean('row_delete')->default('0')->comment('1 for delete');
            $table->dateTime('record_created_on')->nullable();
            $table->dateTime('record_updated_on')->nullable();
            $table->string('record_created_by',15)->nullable();
            $table->string('record_updated_by',15)->nullable();
            //$table->foreign('m_courses')->references('id')->on('course_id') ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_course_enroll');
    }
}
