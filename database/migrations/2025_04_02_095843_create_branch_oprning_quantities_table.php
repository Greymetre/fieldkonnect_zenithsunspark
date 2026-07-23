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
        Schema::create('branch_oprning_quantities', function (Blueprint $table) {
            $table->id();
            $table->string('item_code')->nullable();
            $table->string('item_description')->nullable();
            $table->string('item_group')->nullable();
            $table->string('branch_id')->nullable();
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
        Schema::dropIfExists('branch_oprning_quantities');
    }
};
