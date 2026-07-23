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
        Schema::create('market_intelligence_serveys', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->unsignedBigInteger('division_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->string('product_name')->nullable();
            $table->unsignedBigInteger('cooling_arrangement_id')->nullable();
            $table->unsignedBigInteger('type_of_construction_id')->nullable();
            $table->unsignedBigInteger('hp_id')->nullable();
            $table->integer('stage')->nullable();
            $table->unsignedBigInteger('phase_id')->nullable();
            $table->string('head_range_mtr')->nullable();
            $table->string('discharge_range_lpm')->nullable();
            $table->string('sucx_del')->nullable();
            $table->integer('speed')->nullable();
            $table->integer('mrp')->nullable();
            $table->integer('list_price')->nullable();
            $table->integer('landed_to_dealers')->nullable();
            $table->text('remark')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
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
        Schema::dropIfExists('market_intelligence_serveys');
    }
};
