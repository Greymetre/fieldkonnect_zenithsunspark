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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('active',1)->default('Y');
            $table->unsignedBigInteger('user_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('customer_id')->unsigned()->index()->nullable();
            $table->string('customer_name',200)->index()->default('');
            $table->date('payment_date')->index()->nullable();
            $table->string('payment_mode',200)->index()->default('');
            $table->string('payment_type',200)->index()->default('');
            $table->string('bank_name',200)->index()->default('');
            $table->string('reference_no',200)->index()->default('');
            $table->decimal('amount', 19, 2)->index()->default(0.00);
            $table->string('response',500)->index()->default('');
            $table->string('description',500)->index()->default('');
            $table->string('file_path',500)->index()->default('');
            $table->unsignedBigInteger('status_id')->unsigned()->index()->nullable();
            $table->softDeletes('deleted_at');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('status_id')->references('id')->on('statuses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
