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
        Schema::create('msp_activity_cities', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('msp_activity_id')->nullable();
            $table->bigInteger('city_id')->nullable(); // Fixed missing semicolon
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
        Schema::dropIfExists('msp_activity_cities');
    }
};
