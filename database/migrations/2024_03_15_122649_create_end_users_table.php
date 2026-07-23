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
        Schema::create('end_users', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name')->nullable();
            $table->string('customer_number')->nullable()->unique();
            $table->string('customer_email')->nullable();
            $table->string('customer_address')->nullable();
            $table->string('customer_place')->nullable();
            $table->string('customer_pindcode')->nullable();
            $table->string('customer_country')->nullable();
            $table->string('customer_state')->nullable();
            $table->string('customer_district')->nullable();
            $table->string('customer_city')->nullable();
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
        Schema::dropIfExists('end_users');
    }
};
