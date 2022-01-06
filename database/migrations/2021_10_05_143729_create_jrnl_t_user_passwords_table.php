<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJrnlTUserPasswordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jrnl_t_user_passwords', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('user_id');

            $table->string('username',50);
            $table->string('old_password',255);
            $table->string('new_password',255);

            $table->string('security_q2',50)->nullable();
            $table->string('security_q1',50)->nullable();
            $table->string('answer_key1',50)->nullable();
            $table->string('answer_key2',50)->nullable();
            $table->string('api_token',100)->nullable();
            $table->char('record_status',4)->nullable()->comment('A for active and D for Deactive');
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
        Schema::dropIfExists('jrnl_t_user_passwords');
    }
}
