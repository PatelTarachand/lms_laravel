<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJrnlTPublishedPapersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jrnl_t_published_papers', function (Blueprint $table) {
            $table->id('pub_paper_id');
            $table->string('paper_title',50);
            $table->integer('paper_type_id');
            $table->integer('journal_id');
            $table->integer('journal_category_id');
            $table->integer('volume_id');
            $table->text('abstract',500);
            $table->integer('author_id');
            $table->string('co_author_id',255)->nullable()->default(null)->comment('Data should be in JSON format.');
            $table->string('author_name',255);
            $table->string('author_email',255);
            $table->string('paper_path',255);
            $table->dateTime('submission_date');
            $table->char('paper_status',1);
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
        Schema::dropIfExists('jrnl_t_published_papers');
    }
}
