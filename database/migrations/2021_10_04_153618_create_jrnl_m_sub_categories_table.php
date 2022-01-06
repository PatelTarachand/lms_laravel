<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJrnlMSubCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jrnl_m_sub_categories', function (Blueprint $table) {
            $table->bigIncrements('sub_category_id');
            $table->integer('journal_category_id');
            $table->string('sub_category_name',50)->nullable()->default(null);
            $table->string('sub_category_sort_name',200)->nullable()->default(null);
            $table->string('cover_image',255)->nullable()->default(null);
            $table->string('side_image',255)->nullable()->default(null);
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
        Schema::dropIfExists('jrnl_m_sub_categories');
    }
}
