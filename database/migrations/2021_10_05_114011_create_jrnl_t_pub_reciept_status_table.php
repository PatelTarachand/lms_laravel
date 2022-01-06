<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJrnlTPubRecieptStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jrnl_t_pub_reciept_status', function (Blueprint $table) {
            $table->id('reciept_status_id');

            $table->string('acc_code',10)->nullable()->default(null);
            $table->string('copyright_form',100)->nullable()->default(null);
            $table->string('pers_det_form',100)->nullable()->default(null);
            $table->string('other_det',100)->nullable()->default(null);
            $table->string('ppd',100)->nullable()->default(null)->comment('PPD (Prearranged Payment and Deposit)');
            $table->string('pdd',100)->nullable()->default(null)->comment('Post Disbursal Document(s) (PDD)');
            $table->string('hard_copy_dispatch_number',100)->nullable()->default(null);
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
        Schema::dropIfExists('jrnl_t_pub_reciept_status');
    }
}
