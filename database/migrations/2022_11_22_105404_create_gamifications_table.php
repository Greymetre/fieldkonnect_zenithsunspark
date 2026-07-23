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
        Schema::create('gamifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('customer_id')->unsigned()->index()->nullable();
            $table->string('type',150)->index();
            $table->bigInteger('points')->index()->default(0);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gamifications');
    }
};
