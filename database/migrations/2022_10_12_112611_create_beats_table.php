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
        Schema::create('beats', function (Blueprint $table) {
            $table->id();
            $table->string('active',1)->default('Y');
            $table->string('beat_name',250)->index();
            $table->string('description',450)->index()->default('');
            $table->bigInteger('region_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('country_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('state_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('district_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('city_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('created_by')->unsigned()->index()->nullable();
            $table->timestamps();
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('country_id')->references('id')->on('countries');
            $table->foreign('state_id')->references('id')->on('states');
            $table->foreign('district_id')->references('id')->on('districts');
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
        Schema::dropIfExists('beats');
    }
};
