<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJrnlMSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jrnl_m_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key_name');
            $table->text('value')->nullable();
            $table->char('status')->default('A')->comment('A for Active, I for inactive');
            $table->boolean('row_delete')->default('0')->comment('1 for delete');
            $table->string('record_created_by',15)->nullable()->default(null);
            $table->string('record_updated_by',15)->nullable()->default(null);
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
        Schema::dropIfExists('jrnl_m_settings');
    }
}
