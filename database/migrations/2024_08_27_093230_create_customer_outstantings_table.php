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
        Schema::create('customer_outstantings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('branch_id')->nullable();
            $table->bigInteger('customer_id')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('amount')->nullable();
            $table->string('days')->nullable();
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
        Schema::dropIfExists('customer_outstantings');
    }
};
