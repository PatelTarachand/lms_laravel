<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJrnlTVolumesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jrnl_t_volumes', function (Blueprint $table) {
            $table->id('volume_id');
            $table->integer('journal_id');
            $table->string('volume_name',50);
            $table->text('volume_details',1000)->nullable();
            $table->string('cover_image',255)->nullable();
            
            $table->string('file_name',255)->nullable();
            $table->string('file_extension',255)->nullable();
            $table->string('volume_path',255)->nullable();
            $table->datetime('reg_date');
            $table->datetime('publish_date')->nullable();
            
            $table->string('status',1)->default(null);
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
        Schema::dropIfExists('jrnl_t_volumes');
    }
}
