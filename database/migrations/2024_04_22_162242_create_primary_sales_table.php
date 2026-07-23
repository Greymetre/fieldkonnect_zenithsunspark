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
        Schema::create('primary_sales', function (Blueprint $table) {
            $table->id();
            $table->string('active',1)->default('Y');
            $table->string('invoiceno',250)->index()->default('');
            $table->date('invoice_date')->index()->nullable();
            $table->string('month',50)->default('');
            $table->string('division',50)->default('');
            $table->string('dealer',50)->default('');
            $table->string('city',50)->default('');
            $table->string('state',50)->default('');
            $table->string('final_branch',250)->default('');
            $table->string('sales_person',250)->default('');
            $table->string('product_name',250)->default('');
            $table->bigInteger('quantity')->index()->default(0);
            $table->decimal('rate', 19, 2)->index()->default(0.00);
            $table->decimal('net_amount', 19, 2)->index()->default(0.00);
            $table->decimal('tax_amount', 19, 2)->default(0.00);
            $table->decimal('cgst_amount', 19, 2)->default(0.00);
            $table->decimal('sgst_amount', 19, 2)->default(0.00);
            $table->decimal('igst_amount', 19, 2)->default(0.00);
            $table->decimal('total_amount', 19, 2)->index()->default(0.00);
            $table->string('store_name',250)->default('');
            $table->string('group_name',250)->default('');
            $table->string('branch',250)->default('');
            $table->string('new_group_name',250)->default('');
            $table->unsignedBigInteger('product_id')->nullable();
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
        Schema::dropIfExists('primary_sales');
    }
};
