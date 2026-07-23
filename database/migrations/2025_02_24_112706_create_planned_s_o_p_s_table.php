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
        Schema::create('planned_s_o_p_s', function (Blueprint $table) {
            $table->id();
            $table->date('planning_month')->nullable();
            $table->string('order_id')->nullable();
            $table->bigInteger('division_id')->nullable();
            $table->bigInteger('branch_id')->nullable();
            $table->bigInteger('product_id')->nullable();
            $table->string('opening_stock')->nullable();
            $table->integer('plan_next_month')->nullable();
            $table->bigInteger('budget_for_month')->nullable();
            $table->bigInteger('last_month_sale')->nullable();
            $table->bigInteger('last_three_month_avg')->nullable();
            $table->bigInteger('last_year_month_sale')->nullable();
            $table->integer('sku_unit_price')->nullable();
            $table->integer('s_op_val')->nullable();
            $table->string('top_sku')->nullable();
            $table->integer('dispatch_against_plan')->default(0);
            $table->string('created_by')->nullable();
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('planned_s_o_p_s');
    }
};
