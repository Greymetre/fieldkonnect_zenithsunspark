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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('active',1)->default('Y');
            $table->unsignedBigInteger('buyer_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('seller_id')->unsigned()->index()->nullable();
            $table->bigInteger('total_qty')->index()->default(0);
            $table->bigInteger('shipped_qty')->default(0);
            $table->string('orderno',250)->index()->default('');
            $table->date('order_date')->index()->nullable();
            $table->dateTime('completed_date')->nullable();
            $table->date('estimated_date')->nullable();
            $table->decimal('total_gst', 19, 2)->default(0.00);
            $table->decimal('total_discount', 19, 2)->default(0.00);
            $table->decimal('extra_discount', 8, 2)->default(0.00);
            $table->decimal('extra_discount_amount', 19, 2)->default(0.00);
            $table->decimal('sub_total', 19, 2)->index()->default(0.00);
            $table->decimal('grand_total', 19, 2)->index()->default(0.00);
            $table->string('order_taking',250)->default('');
            $table->unsignedBigInteger('status_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('address_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('created_by')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('updated_by')->unsigned()->index()->nullable();
            $table->softDeletes('deleted_at');
            $table->timestamps();
            $table->unsignedBigInteger('beatscheduleid')->unsigned()->index()->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('buyer_id')->references('id')->on('customers');
            $table->foreign('seller_id')->references('id')->on('customers');
            $table->foreign('status_id')->references('id')->on('statuses');
            $table->foreign('address_id')->references('id')->on('addresses');
            $table->foreign('beatscheduleid')->references('id')->on('beat_schedules');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
