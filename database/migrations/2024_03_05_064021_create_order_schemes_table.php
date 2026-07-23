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
        Schema::create('order_schemes', function (Blueprint $table) {
            $table->id();
            $table->string('active',1)->default('Y');
            $table->string('scheme_name',250)->index();
            $table->longText('scheme_description')->index()->nullable();
            $table->date('start_date')->index();
            $table->date('end_date')->index();
            $table->string('customer_type')->nullable();
            $table->string('scheme_type',200)->nullable();
            $table->string('scheme_basedon',200)->nullable();
            $table->string('assign_to',200)->nullable();
            $table->string('branch')->nullable();
            $table->string('state')->nullable();
            $table->string('customer')->nullable();
            $table->bigInteger('minimum')->index()->nullable();
            $table->bigInteger('maximum')->index()->nullable();
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
        Schema::dropIfExists('order_schemes');
    }
};
