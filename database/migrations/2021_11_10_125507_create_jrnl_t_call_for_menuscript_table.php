<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJrnlTCallForMenuscriptTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jrnl_t_call_for_menuscript', function (Blueprint $table) {
            $table->id();
            $table->integer('jrnl_category_id');
            $table->integer('journal_id');
            $table->text('introduction')->nullable();
            $table->text('details')->nullable();
            $table->text('scope_topic')->nullable();
            $table->string('cover_image',200)->nullable();
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
        Schema::dropIfExists('jrnl_t_call_for_menuscript');
    }
}
