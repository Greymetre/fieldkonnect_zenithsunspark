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
        Schema::create('wallet_details', function (Blueprint $table) {
            $table->id();
            $table->string('active',1)->default('Y');
            $table->unsignedBigInteger('wallet_id')->unsigned()->index();
            $table->bigInteger('points')->index()->default(0);
            $table->string('coupon_code',250)->index()->default('');
            $table->unsignedBigInteger('product_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('category_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('subcategory_id')->unsigned()->index()->nullable();
            $table->bigInteger('quantity')->index()->default(0);
            $table->softDeletes('deleted_at');
            $table->unsignedBigInteger('status_id')->unsigned()->index()->nullable();
            $table->timestamps();
            $table->foreign('status_id')->references('id')->on('statuses');
            $table->foreign('subcategory_id')->references('id')->on('subcategories');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('wallet_id')->references('id')->on('wallets');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wallet_details');
    }
};
