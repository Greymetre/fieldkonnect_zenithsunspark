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
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->string('active',1)->default('Y');
            $table->unsignedBigInteger('user_id')->unsigned()->index()->nullable();
            $table->date('date_of_birth')->index()->nullable();
            $table->date('date_of_joining')->index()->nullable();
            $table->string('marital_status',50)->index()->default('');
            $table->softDeletes('deleted_at');
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
        Schema::dropIfExists('user_details');
    }
};
