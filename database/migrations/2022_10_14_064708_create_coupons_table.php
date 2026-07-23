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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('active',1)->default('Y');
            $table->string('coupon',50)->index()->unique();
            $table->bigInteger('points')->index()->default(0);
            $table->date('expiry_date')->index()->nullable();
            $table->unsignedBigInteger('product_id')->index()->nullable();
            $table->unsignedBigInteger('coupon_profile_id')->index()->nullable();
            $table->softDeletes('deleted_at');
            $table->timestamps();
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('coupon_profile_id')->references('id')->on('coupon_profiles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupons');
    }
};
