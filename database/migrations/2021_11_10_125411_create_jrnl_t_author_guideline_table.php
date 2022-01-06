<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJrnlTAuthorGuidelineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jrnl_t_author_guideline', function (Blueprint $table) {
            $table->id();
            $table->integer('jrnl_category_id')->nullable();
            $table->integer('journal_id')->nullable();
            $table->text('author_guideline')->nullable();
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
        Schema::dropIfExists('jrnl_t_author_guideline');
    }
}
