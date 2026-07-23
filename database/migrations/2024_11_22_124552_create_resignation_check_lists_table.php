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
        Schema::create('resignation_check_lists', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('resignation_id');
            $table->string('document_file')->nullable();
            $table->string('exit_interview')->nullable();
            $table->string('advance')->nullable();
            $table->string('laptop')->nullable();
            $table->string('sim_card')->nullable();
            $table->string('keys')->nullable();
            $table->string('visiting_card')->nullable();
            $table->string('income_tax')->nullable();
            $table->string('laptop_bag')->nullable();
            $table->string('expense_voucher')->nullable();
            $table->string('crm_id')->nullable();
            $table->string('unpaid_salary')->nullable();
            $table->string('data_email')->nullable();
            $table->string('id_card')->nullable();
            $table->string('payable_expense')->nullable();
            $table->string('pen_drive')->nullable();
            $table->string('bouns')->nullable();
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
        Schema::dropIfExists('resignation_check_lists');
    }
};
