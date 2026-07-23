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
        Schema::create('scheme_details', function (Blueprint $table) {
            $table->id();
            $table->string('active',1)->default('Y');
            $table->unsignedBigInteger('scheme_id')->unsigned()->index();
            $table->unsignedBigInteger('product_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('category_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('subcategory_id')->unsigned()->index()->nullable();
            $table->bigInteger('minimum')->index()->nullable();
            $table->bigInteger('maximum')->index()->nullable();
            $table->bigInteger('points')->index()->nullable();
            $table->timestamps();
            $table->foreign('scheme_id')->references('id')->on('scheme_headers');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('subcategory_id')->references('id')->on('subcategories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scheme_details');
    }
};
