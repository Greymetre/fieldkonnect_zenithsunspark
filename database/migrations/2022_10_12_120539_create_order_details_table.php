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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->string('active',1)->default('Y');
            $table->unsignedBigInteger('order_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('product_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('product_detail_id')->unsigned()->index()->nullable();
            $table->bigInteger('quantity')->index()->default(0);
            $table->bigInteger('shipped_qty')->default(0);
            $table->decimal('price', 19, 2)->index()->default(0.00);
            $table->decimal('discount', 19, 2)->default(0.00);
            $table->decimal('discount_amount', 19, 2)->default(0.00);
            $table->decimal('tax_amount', 19, 2)->default(0.00);
            $table->decimal('line_total', 19, 2)->index()->default(0.00);
            $table->unsignedBigInteger('status_id')->unsigned()->index()->nullable();
            $table->timestamps();
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('product_detail_id')->references('id')->on('product_details');
            $table->foreign('status_id')->references('id')->on('statuses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_details');
    }
};
