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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('complaint_number')->nullable();
            $table->date('complaint_date')->nullable();
            $table->bigInteger('seller')->nullable();
            $table->bigInteger('end_user_id')->nullable();
            $table->bigInteger('party_name')->nullable();
            $table->string('product_laying')->nullable();
            $table->bigInteger('service_center')->nullable();
            $table->bigInteger('assign_user')->nullable();
            $table->bigInteger('product_id')->nullable();
            $table->string('product_serail_number')->nullable();
            $table->string('product_code')->nullable();
            $table->string('category')->nullable();
            $table->string('specification')->nullable();
            $table->string('product_no')->nullable();
            $table->string('phase')->nullable();
            $table->string('seller_branch')->nullable();
            $table->string('purchased_branch')->nullable();
            $table->string('product_group')->nullable();
            $table->string('company_sale_bill_no')->nullable();
            $table->date('company_sale_bill_date')->nullable();
            $table->date('customer_bill_date')->nullable();
            $table->string('customer_bill_no')->nullable();
            $table->string('company_bill_date_month')->nullable();
            $table->string('under_warranty')->nullable();
            $table->string('service_type')->nullable();
            $table->string('customer_bill_date_month')->nullable();
            $table->string('warranty_bill')->nullable();
            $table->string('fault_type')->nullable();
            $table->string('service_centre_remark')->nullable();
            $table->string('remark')->nullable();
            $table->string('division')->nullable();
            $table->string('register_by')->nullable();
            $table->string('complaint_type')->nullable();
            $table->string('description')->nullable();
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
        Schema::dropIfExists('complaints');
    }
};
