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
        Schema::create('customer_details', function (Blueprint $table) {
            $table->id();
            $table->string('active',1)->default('Y');
            $table->unsignedBigInteger('customer_id')->unique()->unsigned()->index()->nullable();
            $table->string('gstin_no',250)->index()->default('');
            $table->string('pan_no',250)->index()->default('');
            $table->string('aadhar_no',250)->index()->default('');
            $table->string('otherid_no',250)->index()->default('');
            $table->dateTime('enrollment_date')->nullable();
            $table->dateTime('approval_date')->nullable();
            $table->string('shop_image',250)->index()->default('');
            $table->string('visiting_card',250)->index()->default('');
            $table->string('grade',250)->index()->default('');
            $table->string('visit_status',250)->index()->default('');
            $table->softDeletes('deleted_at');
            $table->timestamps();
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
        Schema::dropIfExists('customer_details');
    }
};
