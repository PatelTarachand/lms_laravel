<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJrnlTVolumeTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jrnl_t_volume_topics', function (Blueprint $table) {
            $table->id('volume_topic_id');
            $table->integer('journal_id');
            $table->string('volume_id',50)->nullable();
            $table->integer('pub_paper_id');
            $table->string('topic_name',200)->nullable()->default(null);
            
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
        Schema::dropIfExists('jrnl_t_volume_topics');
    }
}
