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
        Schema::create('service_charge_categories', function (Blueprint $table) {
            $table->id();
            $table->string('active',1)->default('Y');
            $table->integer('ranking')->default(1);
            $table->string('subcategory_name',250)->index();
            $table->string('subcategory_image',350)->index()->default('');
            $table->unsignedBigInteger('division_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('created_by')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('updated_by')->unsigned()->index()->nullable();
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
        Schema::dropIfExists('service_charge_categories');
    }
};
