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
        Schema::create('gifts', function (Blueprint $table) {
            $table->id();
            $table->string('active',1)->default('Y');
            $table->string('product_name',250)->index();
            $table->string('display_name',250)->index()->default('');
            $table->string('description',450)->index()->default('');
            $table->string('product_image',300)->index()->default('');
            $table->decimal('mrp', 8, 2)->index()->default(0);
            $table->decimal('price', 8, 2)->index()->default(0);
            $table->bigInteger('points')->index()->default(0);
            $table->unsignedBigInteger('subcategory_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('category_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('brand_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('unit_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('created_by')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('updated_by')->unsigned()->index()->nullable();
            $table->softDeletes('deleted_at');
            $table->timestamps();
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('subcategory_id')->references('id')->on('subcategories');
            $table->foreign('brand_id')->references('id')->on('brands');
            $table->foreign('unit_id')->references('id')->on('unit_measures');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gifts');
    }
};
