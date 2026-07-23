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
        Schema::create('resignations', function (Blueprint $table) {
            $table->id();
            $table->date('submit_date')->nullable();
            $table->bigInteger('division_id')->nullable();
            $table->bigInteger('branch_id')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('employee_code')->nullable();
            $table->Integer('notice')->nullable();
            $table->date('date_of_joining')->nullable();
            $table->date('last_working_date')->nullable();
            $table->string('cug_sim_no')->nullable();
            $table->string('reason')->nullable();
            $table->string('persoanla_email')->nullable();
            $table->string('persoanla_mobile')->nullable();
            $table->string('address')->nullable();
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
        Schema::dropIfExists('resignations');
    }
};
