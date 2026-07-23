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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('active',1)->default('Y');
            $table->string('name',200)->index();
            $table->string('first_name',250)->index()->default('');
            $table->string('last_name',250)->index()->default('');
            $table->string('mobile',15)->unique()->index()->nullable();
            $table->string('contact_number',15)->index()->nullable();
            $table->string('email',250)->unique()->index()->nullable();
            $table->string('password')->default('');
            $table->string('notification_id',450)->default('');
            $table->string('latitude',50)->index()->nullable();
            $table->string('longitude',50)->index()->nullable();
            $table->string('device_type',50)->default('');
            $table->string('gender',20)->index()->default('');
            $table->string('profile_image',350)->index()->default('');
            $table->string('customer_code',250)->index()->default('');
            $table->unsignedBigInteger('status_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('customertype')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('region_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('firmtype')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('created_by')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('updated_by')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('executive_id')->unsigned()->index()->nullable();
            $table->softDeletes('deleted_at');
            $table->timestamps();
            $table->unsignedBigInteger('beatscheduleid')->unsigned()->index()->nullable();
            $table->string('manager_name',250)->index()->default('');
            $table->string('manager_phone',50)->index()->default('');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('executive_id')->references('id')->on('users');
            $table->foreign('status_id')->references('id')->on('statuses');
            $table->foreign('customertype')->references('id')->on('customer_types');
            $table->foreign('firmtype')->references('id')->on('firm_types');
            $table->foreign('beatscheduleid')->references('id')->on('beat_schedules');
            $table->foreign('region_id')->references('id')->on('regions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
};
