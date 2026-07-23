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
        Schema::create('expense_logs', function (Blueprint $table) {
            $table->id();
            $table->date('log_date')->nullable();
            $table->unsignedBigInteger('expense_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('created_by')->unsigned()->index()->nullable();
            $table->string('status_type')->nullable();
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
        Schema::dropIfExists('expense_logs');
    }
};
