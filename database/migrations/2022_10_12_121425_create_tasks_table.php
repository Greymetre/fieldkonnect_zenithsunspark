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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('active',1)->default('Y');
            $table->unsignedBigInteger('user_id')->unsigned()->index()->nullable();
            $table->string('title',300)->index()->default('');
           // $table->string('descriptions',1000)->index()->default('');
           $table->string('descriptions')->index()->default('');
            $table->dateTime('datetime')->index()->nullable();
            $table->dateTime('reminder')->index()->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->boolean('completed')->default(false);
            $table->boolean('is_done')->default(false);
            $table->string('remark',1000)->default('');
            $table->unsignedBigInteger('customer_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('status_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('created_by')->unsigned()->index()->nullable();
            $table->timestamps();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};
