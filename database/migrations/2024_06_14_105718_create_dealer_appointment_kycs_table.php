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
        Schema::create('dealer_appointment_kycs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('appointment_id')->nullable();
            $table->string('channel_partner')->nullable();
            $table->string('concerned_branch')->nullable();
            $table->string('dealer_code')->nullable();
            $table->string('division')->nullable();
            $table->string('proprietary_concern')->nullable();
            $table->string('partnership_firm')->nullable();
            $table->string('ltd_pvt')->nullable();
            $table->string('distribution_channel')->nullable();
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
        Schema::dropIfExists('dealer_appointment_kycs');
    }
};
