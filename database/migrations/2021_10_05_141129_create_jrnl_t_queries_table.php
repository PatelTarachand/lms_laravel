<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJrnlTQueriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jrnl_t_queries', function (Blueprint $table) {
            $table->id('query_id');
           
            $table->string('email',255);
            $table->string('name',100);
            $table->integer('author_id');
            $table->integer('paper_id');
            $table->text('query',500)->nullable()->default(null);
            $table->string('priority',10)->nullable()->default(null)->comment('V- very important H- high L- low');
            $table->string('contact_number',10);
            $table->text('solution',500)->nullable()->default(null);
            $table->dateTime('query_date')->nullable();
            $table->string('status',10);

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
        Schema::dropIfExists('jrnl_t_queries');
    }
}
