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
        Schema::table('users', function (Blueprint $table) {
            $table->string('active',1)->default('Y');
            $table->string('first_name',250)->index();
            $table->string('last_name',250)->index();
            $table->string('mobile',11)->unique()->index();
            $table->string('notification_id',450)->default('');
            $table->string('device_type',50)->default('');
            $table->string('gender',20)->default('');
            $table->string('profile_image',350)->index()->default('');
            $table->string('latitude',50)->default('');
            $table->string('longitude',50)->default('');
            $table->string('user_code',50)->default('');
            $table->string('location',250)->index()->default('');
            $table->unsignedBigInteger('reportingid')->unsigned()->index()->nullable();
            $table->bigInteger('region_id')->unsigned()->index()->nullable();
            $table->string('employee_codes',250)->index()->nullable();
            $table->unsignedBigInteger('branch_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('designation_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('department_id')->unsigned()->index()->nullable();
            $table->string('sales_type',20)->default('');
            $table->bigInteger('created_by')->unsigned()->index()->nullable();
            $table->softDeletes('deleted_at');
            $table->foreign('reportingid')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
