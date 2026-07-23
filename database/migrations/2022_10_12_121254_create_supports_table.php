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
        Schema::create('supports', function (Blueprint $table) {
            $table->id();
            $table->string('active',1)->default('Y');
            $table->string('subject',200)->index()->default('');
            $table->string('description',450)->index()->default('');
            $table->string('full_name',450)->index()->default('');
            $table->bigInteger('user_id')->unsigned()->index()->nullable();
            $table->bigInteger('status_id')->unsigned()->index()->nullable();
            $table->bigInteger('customer_id')->unsigned()->index()->nullable();
            $table->bigInteger('assigned_to')->unsigned()->index()->nullable();
            $table->integer('isoverdue')->default(0);
            $table->integer('reopened')->default(0);
            $table->integer('isanswered')->default(0);
            $table->tinyInteger('is_transferred')->default(0);
            $table->dateTime('assigned_at')->nullable();
            $table->dateTime('transferred_at')->nullable();
            $table->dateTime('reopened_at')->nullable();
            $table->dateTime('duedate')->nullable();
            $table->dateTime('closed_at')->nullable();
            $table->dateTime('last_message_at')->nullable();
            $table->dateTime('lock_at')->nullable();
            $table->softDeletes('deleted_at');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
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
        Schema::dropIfExists('supports');
    }
};
