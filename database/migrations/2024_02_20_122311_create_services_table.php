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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('product_code')->nullable();
            $table->string('invoice_no')->nullable();
            $table->date('invoice_date')->nullable();
            $table->string('branch_code')->nullable();
            $table->string('party_name')->nullable();
            $table->string('product_name')->nullable();
            $table->string('qty')->nullable();
            $table->string('group')->nullable();
            $table->string('new_group')->nullable();
            $table->string('serial_no')->nullable();
            $table->string('narration')->nullable();
            $table->string('created_by')->nullable();
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
        Schema::dropIfExists('services');
    }
};
