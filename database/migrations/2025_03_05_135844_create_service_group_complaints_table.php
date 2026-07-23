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
        Schema::create('service_group_complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subcategory_id')->constrained('subcategories')->onDelete('cascade');
            $table->foreignId('service_bill_complaint_id')->constrained('service_bill_complaint_types')->onDelete('cascade');
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
        Schema::dropIfExists('service_group_complaints');
    }
};
