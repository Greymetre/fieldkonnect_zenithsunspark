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
        Schema::create('redemptions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('customer_id');
            $table->string('redeem_mode');
            $table->string('account_holder');
            $table->string('account_number');
            $table->string('bank_name');
            $table->string('ifsc_code');
            $table->string('redeem_amount');
            $table->tinyInteger('status')->default(0);
            $table->bigInteger('created_by');
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
        Schema::dropIfExists('redemptions');
    }
};
