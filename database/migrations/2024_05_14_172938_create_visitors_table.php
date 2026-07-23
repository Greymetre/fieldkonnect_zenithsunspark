<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitorsTable extends Migration
{
    public function up()
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address');
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('system_name');
            $table->string('device')->nullable();
            $table->string('browser')->nullable();
            $table->boolean('is_mobile');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('visitors');
    }
}
