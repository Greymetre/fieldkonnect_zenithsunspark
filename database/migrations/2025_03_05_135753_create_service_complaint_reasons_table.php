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
        Schema::create('service_complaint_reasons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_bill_complaint_id')->constrained('service_bill_complaint_types')->onDelete('cascade');
            $table->string('service_complaint_reasons');
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
        Schema::dropIfExists('service_complaint_reasons');
    }
};
