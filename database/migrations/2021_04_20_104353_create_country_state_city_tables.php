<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountryStateCityTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_countries', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id('id');
            $table->string('name',50);
            $table->string('capital',50)->nullable();
            $table->string('currency',50)->nullable();
            $table->string('native',50)->nullable();
            $table->string('region',50)->nullable();
            $table->tinyInteger('flag');
            $table->string('currency_symbol')->nullable();
            $table->timestamps();
        });

        Schema::create('m_states', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->engine = 'InnoDB';
            $table->id('id');
            $table->string('name');
            $table->bigInteger('country_id')->unsigned();
            $table->tinyInteger('flag');
            $table->timestamps();
            $table->foreign('country_id')->references('id')->on('m_countries') ->onUpdate('cascade')->onDelete('cascade');
        });


        Schema::create('m_cities', function (Blueprint $table) {

            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->engine = 'InnoDB';
            $table->id('id');
            $table->string('name');
            $table->bigInteger('country_id')->unsigned();
            $table->bigInteger('state_id')->unsigned();
            $table->tinyInteger('flag');
            $table->timestamps();
            $table->foreign('state_id')->references('id')->on('m_states')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('country_id')->references('id')->on('m_countries')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::dropIfExists('m_cities');
        Schema::dropIfExists('m_states');
        Schema::dropIfExists('m_countries');
    }
}
