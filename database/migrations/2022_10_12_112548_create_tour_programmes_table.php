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
        Schema::create('tour_programmes', function (Blueprint $table) {
            $table->id();
            $table->date('date')->index()->nullable();
            $table->unsignedBigInteger('userid')->unsigned()->index()->nullable();
            $table->string('town',250)->index()->default('');
           // $table->string('objectives',1000)->index()->default('');
           $table->string('objectives')->default('');
            $table->string('type',50)->index()->default('');
            $table->string('status',50)->index()->default('');
            $table->unsignedBigInteger('created_by')->unsigned()->index()->nullable();
            $table->timestamps();
            $table->softDeletes('deleted_at');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tour_programmes');
    }
};
