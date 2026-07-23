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
        Schema::create('opening_stocks', function (Blueprint $table) {
            $table->id();
            $table->string('item_code')->nullable();
            $table->string('item_description')->nullable();
            $table->string('item_group')->nullable();
            $table->string('ware_house_name')->nullable();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->integer('opening_stocks')->default(0);
            $table->integer('open_order_qty')->default(0);
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
        Schema::dropIfExists('opening_stocks');
    }
};
