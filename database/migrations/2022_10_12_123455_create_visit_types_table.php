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
        Schema::create('visit_types', function (Blueprint $table) {
            $table->id();
            $table->string('active',1)->default('Y');
            $table->string('type_name',250)->index();
            $table->unsignedBigInteger('created_by')->unsigned()->index()->nullable();
            $table->softDeletes('deleted_at');
            $table->timestamps();
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
        Schema::dropIfExists('visit_types');
    }
};
