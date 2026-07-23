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
        Schema::create('fieldsdata', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('field_id')->unsigned()->index()->nullable();
            $table->string('value',250)->index()->nullable();
            $table->timestamps();
            $table->foreign('field_id')->references('id')->on('fields');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fieldsdata');
    }
};
