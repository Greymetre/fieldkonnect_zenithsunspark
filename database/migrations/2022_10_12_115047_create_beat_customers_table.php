<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beat_customers', function (Blueprint $table) {
            $table->id();
            $table->string('active',1)->default('Y');
            $table->unsignedBigInteger('beat_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('customer_id')->unsigned()->index()->nullable();
            $table->timestamps();
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('beat_id')->references('id')->on('beats');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('beat_customers');
    }
};
