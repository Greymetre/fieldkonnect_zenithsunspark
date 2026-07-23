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
        Schema::create('product_details', function (Blueprint $table) {
            $table->id();
            $table->string('active',1)->default('Y');
            $table->string('detail_title',200)->index()->default('');
            $table->string('detail_description',450)->index()->default('');
            $table->unsignedBigInteger('product_id')->unsigned()->index()->nullable();
            $table->string('detail_image',400)->index()->default('');
            $table->decimal('mrp', 8, 2)->index()->nullable();
            $table->decimal('price', 8, 2)->index()->nullable();
            $table->decimal('discount', 8, 2)->comment('in percent')->nullable();
            $table->decimal('max_discount', 8, 2)->comment('in percent')->nullable();
            $table->decimal('selling_price', 8, 2)->index()->nullable();
            $table->decimal('gst')->comment('gst in percent')->nullable();
            $table->smallInteger('isprimary')->nullable();
            $table->bigInteger('stock_qty')->default(0)->nullable();
            $table->string('hsn_code',250)->index()->unique()->nullable();
            $table->string('ean_code',250)->index()->unique()->nullable();
            $table->softDeletes('deleted_at');
            $table->timestamps();
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_details');
    }
};
