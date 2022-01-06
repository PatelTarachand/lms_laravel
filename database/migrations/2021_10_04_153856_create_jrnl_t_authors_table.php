<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJrnlTAuthorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jrnl_t_authors', function (Blueprint $table) {
            $table->id('author_id');
            $table->integer('author_user_id');
            $table->integer('paper_id');
            $table->char('prefix',5)->nullable()->default(null);
            $table->string('first_name',50);
            $table->string('middle_name',50)->nullable();
            $table->string('last_name',50)->nullable();
            $table->string('designation',100)->nullable()->default(null);
            $table->string('institute',200)->nullable()->default(null);
            $table->string('department',200)->nullable()->default(null);
            $table->string('email',255)->nullable();
            $table->string('std_code',4)->nullable()->default(null);
            $table->string('contact_no',10)->nullable()->default(null);
            $table->string('address',200)->nullable()->default(null);
            $table->string('city',50)->nullable()->default(null);
            $table->string('district',50)->nullable()->default(null);
            $table->string('state',50)->nullable()->default(null);
            $table->string('country',50)->nullable()->default(null);
            $table->string('zip',6)->nullable()->default(null);
            $table->string('co_authors',200)->nullable()->default(null);
            $table->char('record_status',1)->default('A')->comment('A for active');
            $table->boolean('row_delete')->default('0')->comment('1 for delete');
            $table->dateTime('record_created_on')->nullable();
            $table->dateTime('record_updated_on')->nullable();
            $table->string('record_created_by',50)->nullable();
            $table->string('record_updated_by',50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jrnl_t_authors');
    }
}
