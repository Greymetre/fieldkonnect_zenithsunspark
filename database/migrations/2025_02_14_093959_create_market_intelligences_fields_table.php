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
        Schema::create('market_intelligences_fields', function (Blueprint $table) {
            $table->id();
            $table->string('active',1)->default('Y');
            $table->integer('ranking')->default(1);
            $table->string('field_name',250)->index()->default('');
            $table->string('field_type',250)->index()->default('');
            $table->string('is_required',10)->default('false');
            $table->string('is_multiple',10)->default('false');
            $table->string('label_name',250)->index()->default('');
            $table->string('placeholder',250)->index()->default('');
            $table->string('module',250)->index()->default('');
            $table->unsignedBigInteger('created_by')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('division_id')->nullable();
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
        Schema::dropIfExists('market_intelligences_fields');
    }
};
