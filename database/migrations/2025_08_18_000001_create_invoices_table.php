<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->boolean('gst')->default(0);
            $table->string('gst_no')->nullable();
            $table->string('place_of_supply')->nullable();
            $table->string('invoice_no')->unique();
            $table->string('order_no')->nullable();
            $table->date('invoice_date');
            $table->string('payment_term')->nullable();
            $table->date('due_date')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->decimal('sub_total', 15, 2)->default(0);
            $table->enum('discount_type', ['flat', 'percent'])->nullable();
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('tds', 5, 2)->default(0);
            $table->decimal('tds_amount', 15, 2)->default(0);
            $table->text('t_c')->nullable();
            $table->timestamps();

            // Foreign keys (optional)
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
