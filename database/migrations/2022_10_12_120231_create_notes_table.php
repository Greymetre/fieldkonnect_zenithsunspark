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
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->string('active',1)->default('Y');
            $table->unsignedBigInteger('user_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('customer_id')->unsigned()->index()->nullable();
           // $table->text('note')->index()->default('');
            $table->text('note')->nullable();
            $table->string('purpose',250)->index()->default('');
            $table->string('callstatus',450)->index()->default('');
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
        Schema::dropIfExists('notes');
    }
};
