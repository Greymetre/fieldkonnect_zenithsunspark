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
        Schema::create('beat_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('active',1)->default('Y');
            $table->unsignedBigInteger('beat_id')->unsigned()->index()->nullable();
            $table->date('beat_date')->index()->nullable();
            $table->unsignedBigInteger('user_id')->unsigned()->index()->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('tourid')->unsigned()->index()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('tourid')->references('id')->on('tour_programmes');
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
        Schema::dropIfExists('beat_schedules');
    }
};
