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
        Schema::create('mobile_user_login_details', function (Blueprint $table) {
            $table->id();
            $table->string('active',1)->default('Y');
            $table->unsignedBigInteger('customer_id')->unique()->unsigned()->index()->nullable();
            $table->string('app_version',250)->default('');
            $table->string('device_type',250)->default('');
            $table->string('device_name',250)->default('');
            $table->dateTime('first_login_date')->nullable();
            $table->dateTime('last_login_date')->nullable();
            $table->string('login_status',250)->default('');
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
        //
    }
};
