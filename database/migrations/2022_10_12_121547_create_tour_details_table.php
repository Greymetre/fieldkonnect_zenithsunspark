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
        Schema::create('tour_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tourid')->unsigned()->index()->nullable();
            $table->bigInteger('city_id')->unsigned()->index()->nullable();
            $table->date('visited_date')->index()->nullable();
            $table->bigInteger('visited_cityid')->unsigned()->index()->nullable();
            $table->date('last_visited')->index()->nullable();
            $table->timestamps();
            $table->foreign('city_id')->references('id')->on('cities');
            $table->foreign('tourid')->references('id')->on('tour_programmes');
            $table->foreign('visited_cityid')->references('id')->on('cities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tour_details');
    }
};
