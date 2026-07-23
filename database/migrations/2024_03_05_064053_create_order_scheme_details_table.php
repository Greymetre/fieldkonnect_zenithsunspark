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
        Schema::create('order_scheme_details', function (Blueprint $table) {
            $table->id();
            $table->string('active',1)->default('Y');
            $table->unsignedBigInteger('order_scheme_id')->unsigned()->index();
            $table->unsignedBigInteger('product_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('category_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('subcategory_id')->unsigned()->index()->nullable();
            $table->bigInteger('points')->index()->nullable();
            $table->foreign('order_scheme_id')->references('id')->on('order_schemes');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('subcategory_id')->references('id')->on('subcategories');
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
        Schema::dropIfExists('order_scheme_details');
    }
};
