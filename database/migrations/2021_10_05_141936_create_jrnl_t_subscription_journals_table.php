<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJrnlTSubscriptionJournalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jrnl_t_subscription_journals', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            // need to be discuss with Dr. Vikash sir for journal subscription.

            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jrnl_t_subscription_journals');
    }
}
