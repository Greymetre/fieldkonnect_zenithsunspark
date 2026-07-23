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
        Schema::create('check_in', function (Blueprint $table) {
            $table->id();
            $table->string('active',1)->default('Y');
            $table->unsignedBigInteger('customer_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('user_id')->unsigned()->index()->nullable();
            $table->date('checkin_date')->index();
            $table->time('checkin_time')->index();
            $table->string('checkin_latitude',250)->index()->nullable();
            $table->string('checkin_longitude',250)->index()->nullable();
            $table->string('checkin_address',250)->index()->nullable();
            $table->date('checkout_date')->index()->nullable();
            $table->time('checkout_time')->index()->nullable();
            $table->string('checkout_latitude',250)->index()->nullable();
            $table->string('checkout_longitude',250)->index()->nullable();
            $table->string('checkout_address',250)->index()->nullable();
            $table->softDeletes('deleted_at');
            $table->timestamps();
            $table->string('distance',250)->index()->nullable();
            $table->unsignedBigInteger('beatscheduleid')->unsigned()->index()->nullable();
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('beatscheduleid')->references('id')->on('beat_schedules');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('check_in');
    }
};
