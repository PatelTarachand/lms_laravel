<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJrnlTUserLoginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jrnl_t_user_logins', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('user_id')->nullable();
            $table->string('username',50)->nullable();
            $table->string('login_status',30);
            $table->dateTime('login_date_time');
            $table->dateTime('logout_date_time')->nullable();
            $table->ipAddress('login_ip_address');
            $table->string('token_id',100)->nullable();
            $table->integer('login_attempt');
            $table->dateTime('record_created_on');
            $table->dateTime('record_updated_on');
            $table->string('record_created_by',20)->nullable();
            $table->string('record_updated_by',20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jrnl_t_user_logins');
    }
}
