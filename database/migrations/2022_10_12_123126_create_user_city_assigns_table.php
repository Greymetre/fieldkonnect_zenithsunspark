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
        Schema::create('user_city_assigns', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('userid')->unsigned()->index();
            $table->bigInteger('reportingid')->unsigned()->index()->nullable();
            $table->bigInteger('city_id')->unsigned()->index()->nullable();
            $table->timestamps();
            $table->foreign('userid')->references('id')->on('users');
            $table->foreign('reportingid')->references('id')->on('users');
            $table->foreign('city_id')->references('id')->on('cities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_city_assigns');
    }
};
