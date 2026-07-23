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
        Schema::create('user_live_locations', function (Blueprint $table) {
            $table->id();
            $table->string('active',1)->default('Y');
            $table->bigInteger('userid')->unsigned()->index();
            $table->string('latitude',50)->index()->nullable();
            $table->string('longitude',50)->index()->nullable();
            $table->dateTime('time')->index()->nullable();
            $table->string('address',450)->index()->default('');
            $table->softDeletes('deleted_at');
            $table->timestamps();
            $table->foreign('userid')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_live_locations');
    }
};
