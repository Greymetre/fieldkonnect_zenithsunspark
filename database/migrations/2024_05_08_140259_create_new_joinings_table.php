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
        Schema::create('new_joinings', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('gender')->nullable();
            $table->date('dob')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('father_name')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('spouse_name')->nullable();
            $table->date('spouse_dob')->nullable();
            $table->string('spouse_education')->nullable();
            $table->string('spouse_occupation')->nullable();
            $table->string('anniversary')->nullable();
            $table->string('present_address')->nullable();
            $table->string('present_city')->nullable();
            $table->string('present_state')->nullable();
            $table->string('present_pincode')->nullable();
            $table->string('permanent_address')->nullable();
            $table->string('permanent_city')->nullable();
            $table->string('permanent_state')->nullable();
            $table->string('permanent_pincode')->nullable();
            
            $table->string('pan')->nullable();
            $table->string('aadhar')->nullable();
            $table->string('driving_licence')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('language')->nullable();
            $table->string('other_language')->nullable();
            $table->string('qualification')->nullable();
            $table->string('experience')->nullable();
            $table->string('skill')->nullable();
            $table->string('occupy')->nullable();
            $table->string('branch')->nullable();
            $table->string('department')->nullable();
            $table->date('date_of_joining')->nullable();
            $table->string('designation')->nullable();
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
        Schema::dropIfExists('new_joinings');
    }
};
