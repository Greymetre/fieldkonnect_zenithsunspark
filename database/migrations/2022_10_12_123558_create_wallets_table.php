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
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->string('active',1)->default('Y');
            $table->unsignedBigInteger('customer_id')->unsigned()->index();
            $table->unsignedBigInteger('scheme_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('schemedetail_id')->unsigned()->index()->nullable();
            $table->bigInteger('points')->index()->default(0);
            $table->string('point_type',20)->index()->default('');
            $table->decimal('invoice_amount', 19, 2)->index()->default(0.00);
            $table->string('invoice_no',200)->index()->default('');
            $table->string('coupon_code',250)->index()->default('');
            $table->date('invoice_date')->index()->nullable();
            $table->timestamp('transaction_at', 0)->useCurrent();
            $table->string('transaction_type',20)->index()->default('');
            $table->unsignedBigInteger('sales_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('status_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('checkinid')->unsigned()->index()->nullable();
            $table->bigInteger('quantity')->index()->default(0);
            $table->unsignedBigInteger('userid')->unsigned()->index()->nullable();
            $table->softDeletes('deleted_at');
            $table->timestamps();
            $table->foreign('status_id')->references('id')->on('statuses');
            $table->foreign('sales_id')->references('id')->on('sales');
            $table->foreign('customer_id')->references('id')->on('customers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wallets');
    }
};
