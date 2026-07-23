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
        Schema::create('dealer_appointments', function (Blueprint $table) {
            $table->id();
            $table->string('branch')->nullable();
            $table->string('district')->nullable();
            $table->string('appointment_date')->nullable();
            $table->string('customertype')->nullable();
            $table->string('division')->nullable();
            $table->string('SDPUMPMOTORS')->nullable();
            $table->string('SDF&A')->nullable();
            $table->string('gst_type')->nullable();
            $table->string('gst_no')->nullable();
            $table->string('firm_type')->nullable();
            $table->string('firm_name')->nullable();
            $table->string('cin_no')->nullable();
            $table->string('related_firm_name')->nullable();
            $table->text('line_business')->nullable();
            $table->text('office_address')->nullable();
            $table->text('office_pincode')->nullable();
            $table->text('office_mobile')->nullable();
            $table->text('office_email')->nullable();
            $table->text('godown_address')->nullable();
            $table->text('godown_pincode')->nullable();
            $table->text('godown_mobile')->nullable();
            $table->text('godown_email')->nullable();
            $table->text('status')->nullable();
            $table->text('ppd_name_1')->nullable();
            $table->text('ppd_adhar_1')->nullable();
            $table->text('ppd_pan_1')->nullable();
            $table->text('ppd_name_2')->nullable();
            $table->text('ppd_adhar_2')->nullable();
            $table->text('ppd_pan_2')->nullable();
            $table->text('ppd_name_3')->nullable();
            $table->text('ppd_adhar_3')->nullable();
            $table->text('ppd_pan_3')->nullable();
            $table->text('ppd_name_4')->nullable();
            $table->text('ppd_adhar_4')->nullable();
            $table->text('ppd_pan_4')->nullable();
            $table->text('contact_person_name')->nullable();
            $table->text('mobile_email')->nullable();
            $table->text('bank_name')->nullable();
            $table->text('bank_address')->nullable();
            $table->text('account_type')->nullable();
            $table->text('account_number')->nullable();
            $table->text('ifsc_code')->nullable();
            $table->text('payment_term')->nullable();
            $table->text('credit_period')->nullable();
            $table->text('cheque_no_1')->nullable();
            $table->text('cheque_account_number_1')->nullable();
            $table->text('cheque_bank_1')->nullable();
            $table->text('cheque_no_2')->nullable();
            $table->text('cheque_account_number_2')->nullable();
            $table->text('cheque_bank_2')->nullable();
            $table->text('manufacture_company_1')->nullable();
            $table->text('manufacture_product_1')->nullable();
            $table->text('manufacture_business_1')->nullable();
            $table->text('manufacture_turn_over_1')->nullable();
            $table->text('manufacture_company_2')->nullable();
            $table->text('manufacture_product_2')->nullable();
            $table->text('manufacture_business_2')->nullable();
            $table->text('manufacture_turn_over_2')->nullable();
            $table->text('present_annual_turnover')->nullable();
            $table->text('motor_anticipated_business_1')->nullable();
            $table->text('motor_next_year_business_1')->nullable();
            $table->text('pump_anticipated_business_1')->nullable();
            $table->text('pump_next_year_business_1')->nullable();
            $table->text('F&A_anticipated_business_1')->nullable();
            $table->string('F&A_next_year_business_1')->nullable();
            $table->string('lighting_anticipated_business_1')->nullable();
            $table->string('lighting_next_year_business_1')->nullable();
            $table->string('agri_anticipated_business_1')->nullable();
            $table->string('agri_next_year_business_1')->nullable();
            $table->string('solar_anticipated_business_1')->nullable();
            $table->string('solar_next_year_business_1')->nullable();
            $table->string('anticipated_business_total')->nullable();
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
        Schema::dropIfExists('dealer_appointments');
    }
};
