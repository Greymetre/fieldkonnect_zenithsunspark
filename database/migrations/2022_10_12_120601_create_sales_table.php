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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('active',1)->default('Y');
            $table->unsignedBigInteger('buyer_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('seller_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('order_id')->unsigned()->index()->nullable();
            $table->bigInteger('total_qty')->index()->default(0);
            $table->bigInteger('shipped_qty')->default(0);
            $table->string('orderno',250)->index()->default('');
            $table->string('fiscal_year',50)->default('');
            $table->string('sales_no',250)->index()->default('');
            $table->string('invoice_no',250)->index()->default('');
            $table->date('invoice_date')->index()->nullable();
            $table->decimal('total_gst', 19, 2)->default(0.00);
            $table->decimal('total_discount', 19, 2)->nullable();
            $table->decimal('extra_discount', 8, 2)->nullable();
            $table->decimal('extra_discount_amount', 19, 2)->nullable();
            $table->decimal('sub_total', 19, 2)->index()->default(0.00);
            $table->decimal('grand_total', 19, 2)->index()->default(0.00);
            $table->decimal('paid_amount', 19, 2)->default(0.00);
            $table->string('description',400)->index()->default('');
            $table->unsignedBigInteger('status_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('created_by')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('updated_by')->unsigned()->index()->nullable();
            $table->softDeletes('deleted_at');
            $table->timestamps();
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('buyer_id')->references('id')->on('customers');
            $table->foreign('seller_id')->references('id')->on('customers');
            $table->foreign('status_id')->references('id')->on('statuses');
            $table->foreign('order_id')->references('id')->on('orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
    }
};
