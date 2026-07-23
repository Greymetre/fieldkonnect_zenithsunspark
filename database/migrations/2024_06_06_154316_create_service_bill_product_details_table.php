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
        Schema::create('service_bill_product_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('service_bill_id')->nullable();
            $table->string('service_type')->nullable();
            $table->string('product_id')->nullable();
            $table->string('quantity')->nullable();
            $table->string('distance')->nullable();
            $table->string('appreciation')->nullable();
            $table->string('price')->nullable();
            $table->string('subtotal')->nullable();
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
        Schema::dropIfExists('service_bill_product_details');
    }
};
