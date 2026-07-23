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
        Schema::create('payment_details', function (Blueprint $table) {
            $table->id();
            $table->string('active',1)->default('Y');
            $table->unsignedBigInteger('payment_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('sales_id')->unsigned()->index()->nullable();
            $table->string('invoice_no',200)->index()->default('');
            $table->decimal('amount', 19, 2)->index()->default(0.00);
            $table->timestamps();
            $table->foreign('payment_id')->references('id')->on('payments');
            $table->foreign('sales_id')->references('id')->on('sales');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_details');
    }
};
