<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTEnrollEntpMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_enroll_entp_members', function (Blueprint $table) {

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';


            $table->integer('id')->autoIncrement();
            $table->string('roll_number',50)->nullable();
            $table->string('entroll_id',50)->nullable();
			$table->integer('student_id');
            $table->string('course_code',50);
            $table->char('record_status',4)->comment('A for active and D for Deactive');
			$table->char('student_type',4)->comment('A for new or admin student M for addon or member student');
            $table->boolean('row_delete')->default('0')->comment('1 for delete');
            $table->dateTime('record_created_on')->nullable();
            $table->dateTime('record_updated_on')->nullable();
            $table->string('record_created_by',15)->nullable();
            $table->string('record_updated_by',15)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_enroll_entp_members');
    }
}
