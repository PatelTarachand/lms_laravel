<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTStudentRemarkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_student_remark', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->text('remark');
            $table->integer('student_id');
            $table->dateTime('remark_date');
            $table->char('remark_status',4)->nullable()->comment('A for active and D for Deactive');
            $table->char('email_sent_or_not',4)->default("N")->comment('Y for sent, N for Not Sent ');
            $table->boolean('row_delete')->default('0')->comment('1 for delete');
            $table->dateTime('record_created_on')->nullable();
            $table->dateTime('record_updated_on')->nullable();
            $table->string('record_created_by',15)->nullable();
            $table->string('record_updated_by',15)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_student_remark');
    }
}
