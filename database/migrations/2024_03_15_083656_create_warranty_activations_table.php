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
        Schema::create('warranty_activations', function (Blueprint $table) {
            $table->id();
            $table->string('product_serail_number')->nullable();
            $table->bigInteger('product_id')->nullable();
            $table->bigInteger('branch_id')->nullable();
            $table->bigInteger('end_user_id')->nullable();
            $table->string('customer_id')->nullable();
            $table->string('sale_bill_no')->nullable();
            $table->date('sale_bill_date')->nullable();
            $table->date('warranty_date')->nullable();
            $table->string('status')->nullable();
            $table->bigInteger('created_by')->nullable();
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
        Schema::dropIfExists('warranty_activations');
    }
};
