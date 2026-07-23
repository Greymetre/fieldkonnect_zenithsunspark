<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('product_id');
            $table->string('product_dec')->nullable();
            $table->string('hsn_sac')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('mrp', 15, 2)->default(0);
            $table->decimal('tax', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            $table->timestamps();

            // Foreign keys
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_details');
    }
};
