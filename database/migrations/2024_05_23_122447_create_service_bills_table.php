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
        Schema::create('service_bills', function (Blueprint $table) {
            $table->id();
            $table->string('bill_no')->nullable();
            $table->string('complaint_id')->nullable();
            $table->string('complaint_no')->nullable();
            $table->string('division')->nullable();
            $table->string('category')->nullable();
            $table->string('complaint_type')->nullable();
            $table->string('complaint_reason')->nullable();
            $table->string('condition_fo_service')->nullable();
            $table->string('received_product')->nullable();
            $table->string('nature_of_fault')->nullable();
            $table->string('service_location')->nullable();
            $table->string('repaired_replacement')->nullable();
            $table->string('replacement_tag')->nullable();
            $table->string('replacement_tag_number')->nullable();
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
        Schema::dropIfExists('service_bills');
    }
};
