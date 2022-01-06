<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJrnlTCameraReadysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jrnl_t_camera_readys', function (Blueprint $table) {
            $table->id('camera_ready_id');
            
            $table->integer('journal_id');
            $table->integer('paper_id');
            $table->string('file_name',200)->nullable()->default(null);
            $table->string('file_path',255);

            $table->datetime('approved_date');
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
        Schema::dropIfExists('jrnl_t_camera_readys');
    }
}
