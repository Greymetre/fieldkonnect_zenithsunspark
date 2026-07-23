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
        Schema::create('planned_sop_sale_data', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('planned_sop_id')->nullable();
            $table->string('month_1')->nullable();
            $table->string('month_2')->nullable();
            $table->string('month_3')->nullable();
            $table->string('month_4')->nullable();
            $table->string('month_5')->nullable();
            $table->string('month_6')->nullable();
            $table->string('month_7')->nullable();
            $table->string('month_8')->nullable();
            $table->string('month_9')->nullable();
            $table->string('month_10')->nullable();
            $table->string('month_11')->nullable();
            $table->string('month_12')->nullable();
            $table->string('min')->nullable();
            $table->string('max')->nullable();
            $table->string('avg')->nullable();
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
        Schema::dropIfExists('planned_sop_sale_data');
    }
};
