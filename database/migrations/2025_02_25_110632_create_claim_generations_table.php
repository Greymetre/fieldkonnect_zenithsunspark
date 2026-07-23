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
        Schema::create('claim_generations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('service_center_id')->nullable();
            $table->string('month')->nullable();
            $table->string('year')->nullable();
            $table->string('claim_number')->nullable();
            $table->double('claim_amount', 8, 2)->nullable();
            $table->string('courier_details')->nullable();
            $table->date('courier_date')->nullable();
            $table->string('asc_bill_no')->nullable();
            $table->date('asc_bill_date')->nullable();
            $table->double('asc_bill_amount')->nullable();
            $table->text('claim_sattlement_details')->nullable();
            $table->tinyInteger('submitted_by_se')->nullable();
            $table->tinyInteger('claim_approved')->nullable();
            $table->tinyInteger('claim_done')->nullable();
            $table->date('claim_date')->nullable();
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
        Schema::dropIfExists('claim_generations');
    }
};
