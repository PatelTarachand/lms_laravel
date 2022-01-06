<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJrnlTReprintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jrnl_t_reprints', function (Blueprint $table) {
            $table->id();
            
            $table->integer('author_id'); 
            $table->string('author_full_name',100); 
            $table->integer('volume_id');
            $table->string('volume_name',50);
            $table->integer('volume_number');
            $table->integer('paper_id');
            $table->string('order_number',10);
            $table->integer('reprint_coppies');
            $table->datetime('reprint_order_date');
            $table->integer('journal_id');
            $table->string('journal_name',100);

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
        Schema::dropIfExists('jrnl_t_reprints');
    }
}
