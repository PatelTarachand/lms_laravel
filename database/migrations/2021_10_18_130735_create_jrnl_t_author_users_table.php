<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJrnlTAuthorUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jrnl_t_author_users', function (Blueprint $table) {
            $table->id('author_id');
            $table->string('first_name',50);
            $table->string('last_name',50)->nullable()->default(null);
            $table->string('email',255)->nullable()->default(null);
            $table->text('password');
            $table->string('profile_pic',200)->nullable();
            $table->string('mobile_no',100)->nullable()->default(null);
            $table->char('record_status',1)->default('A')->comment('A for active');
            $table->boolean('row_delete')->default('0')->comment('1 for delete');
            $table->dateTime('record_created_on')->nullable();
            $table->dateTime('record_updated_on')->nullable();
            $table->string('record_created_by',50)->nullable();
            $table->string('record_updated_by',50)->nullable();
            $table->string('api_token',200)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jrnl_t_author_users');
    }
}
