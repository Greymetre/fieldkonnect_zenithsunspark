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
        Schema::create('gift_redemption_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('redemption_id');
            $table->string('redemption_no')->nullable();
            $table->string('purchase_rate')->nullable();
            $table->string('gst')->nullable();
            $table->string('total_purchase')->nullable();
            $table->string('purchase_invoice_no')->nullable();
            $table->string('purchase_return_no')->nullable();
            $table->string('client_invoice_no')->nullable();            
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
        Schema::dropIfExists('gift_redemption_details');
    }
};
