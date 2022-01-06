<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJrnlTNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jrnl_t_notifications', function (Blueprint $table) {
            $table->id('notice_id');
            $table->string('notice_title',300);
            $table->string('notice_desc',500);
            $table->string('notice_file',200);
            $table->char('status')->default('A')->comment('A for Active, I for inactive');
            $table->boolean('row_delete')->default('0')->comment('1 for delete');
            $table->string('record_created_by',15)->nullable();
            $table->string('record_updated_by',15)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jrnl_t_notifications');
    }
}
