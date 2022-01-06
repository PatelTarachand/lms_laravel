float<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTQualificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_qualifications', function (Blueprint $table) {
            $table->integer('qualification_id')->autoIncrement();
            $table->integer('student_id');
            $table->string('qualification_code',5)->nullable();
            $table->string('qualification_name',50);
            $table->string('board_name',50);
            $table->string('passing_year',50);
            $table->float('marks')->nullable();
            $table->float('grade')->nullable();
            $table->string('file_path')->nullable();
			$table->char('record_status',4)->comment('R for registred U for update.');
            $table->boolean('row_delete')->default('0')->comment('1 for delete');
            $table->dateTime('record_created_on');
            $table->dateTime('record_updated_on')->nullable();
            $table->string('record_created_by',10)->nullable();
            $table->string('record_updated_by',10)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_qualifications');
    }
}
