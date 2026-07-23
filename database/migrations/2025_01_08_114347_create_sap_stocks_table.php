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
        Schema::create('sap_stocks', function (Blueprint $table) {
            $table->id();
            $table->string('product_sap_code')->nullable();
            $table->string('product_description')->nullable();;
            $table->string('product_category_sap_code')->nullable();;
            $table->string('product_category_name')->nullable();;
            $table->string('warehouse_code')->nullable();;
            $table->string('warehouse_name')->nullable();;
            $table->string('instock_qty')->nullable();;
            $table->string('itm_remarks')->nullable();;
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
        Schema::dropIfExists('sap_stocks');
    }
};
