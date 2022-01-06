<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTEnrollmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_enrollments', function (Blueprint $table) {

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';


            $table->integer('id')->autoIncrement();
            $table->string('enroll_id', 50)->unique();
            $table->integer('student_id')->unsigned();
            $table->date('enroll_date')->nullable()->default(null);
            $table->char('record_status',4)->comment('R for Registred A for approve X for reject P pending');
			$table->string('request_status',15)->comment('Registred,Approve,Reject,Pending');
            $table->boolean('row_delete')->default('0')->comment('1 for delete');
            $table->dateTime('record_created_on')->nullable();
            $table->dateTime('record_updated_on')->nullable();
            $table->string('record_created_by',15)->nullable();
            $table->string('record_updated_by',15)->nullable();
            //$table->foreign('t_students')->references('id')->on('student_id') ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_enrollments');
    }
}
