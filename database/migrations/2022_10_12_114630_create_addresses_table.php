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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('active',1)->default('Y');
            $table->string('address1',250)->index()->default('');
            $table->string('address2',250)->index()->default('');
            $table->string('landmark',250)->index()->default('');
            $table->string('locality',250)->index()->default('');
            $table->unsignedBigInteger('customer_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('user_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('country_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('state_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('district_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('city_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('pincode_id')->unsigned()->index()->nullable();
            $table->string('zipcode',250)->index()->default('');
            $table->bigInteger('created_by')->unsigned()->index()->nullable();
            $table->softDeletes('deleted_at');
            $table->timestamps();
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('country_id')->references('id')->on('countries');
            $table->foreign('state_id')->references('id')->on('states');
            $table->foreign('district_id')->references('id')->on('districts');
            $table->foreign('city_id')->references('id')->on('cities');
            $table->foreign('pincode_id')->references('id')->on('pincodes');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addresses');
    }
};
