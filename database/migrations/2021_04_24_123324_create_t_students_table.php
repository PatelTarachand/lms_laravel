<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_students', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('id')->autoIncrement();
            $table->string('first_name', 50);
            $table->string('last_name',50);
            $table->string('mother_name', 50);
            $table->string('father_name',50);
            $table->date('dob')->nullable()->default(null);
            $table->string('gender',10);
            $table->string('category',10);
            $table->string('mobile_no',10);
            $table->string('email',50)->nullable()->default(null);
            $table->text('password')->nullable();
            $table->string('aadhar_no',20);
            $table->string('correspondence_country',25)->nullable()->default(null)->nullable()->default(null);
            $table->string('correspondence_state',25)->nullable()->default(null)->nullable()->default(null);
            $table->string('correspondence_city',25)->nullable()->default(null)->nullable()->default(null);
            $table->string('correspondence_address', 200)->nullable()->default(null)->nullable()->default(null);
            $table->string('correspondence_pin',6)->nullable()->default(null)->nullable()->default(null);
            $table->string('whatsapp_no',10)->nullable()->default(null);
            $table->string('twitter_id',100)->nullable()->default(null);
            $table->string('facebook_id',100)->nullable()->default(null);
            $table->string('instagram_id',100)->nullable()->default(null);
            $table->string('is_disability',3)->nullable()->default(null);
            $table->string('disability_per',10)->nullable()->default(null);
            $table->string('course_code',50)->nullable()->default(null);
            $table->char('approve_reject_status',4)->comment('R for Registred A for approve X for reject P pending U for updated F for Fee Paid E for Eligible M for Modified ');
            $table->text('approve_reject_reason')->nullable()->default(null);
            $table->dateTime('approve_reject_date')->nullable()->default(null);
            $table->dateTime('registration_date')->nullable()->default(null);
            $table->string('request_status',15)->nullable()->default(null)->comment('Registred,Approve,Reject,Pending');
            $table->boolean('row_delete')->default('0')->comment('1 for delete');
            $table->string('remember_token')->nullable()->default(null);
            $table->string('ip',50)->nullable();
            $table->json('client_info')->nullable()->default(null);
            $table->string('api_token',100)->nullable();
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
        Schema::dropIfExists('t_students');
    }
}
