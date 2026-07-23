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
        Schema::create('visit_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('checkin_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('user_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('customer_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('visit_type_id')->unsigned()->index()->nullable();
            $table->string('report_title',200)->index()->default('');
            $table->string('description',450)->index()->default('');
            $table->string('visit_image',450)->index()->default('');
            $table->dateTime('next_visit')->index()->nullable();
            $table->unsignedBigInteger('status_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('created_by')->unsigned()->index()->nullable();
            $table->softDeletes('deleted_at');
            $table->timestamps();
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('checkin_id')->references('id')->on('check_in');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('visit_type_id')->references('id')->on('visit_types');
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
        Schema::dropIfExists('visit_reports');
    }
};
