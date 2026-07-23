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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('active',1)->default('Y');
            $table->integer('ranking')->default(1);
            $table->string('product_name',250)->index();
            $table->string('display_name',250)->index()->default('');
            $table->string('description',450)->index()->default('');
            $table->unsignedBigInteger('subcategory_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('category_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('brand_id')->unsigned()->index()->nullable();
            $table->string('product_image',300)->index()->default('');
            $table->unsignedBigInteger('unit_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('created_by')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('updated_by')->unsigned()->index()->nullable();
            $table->softDeletes('deleted_at');
            $table->timestamps();
            //$table->string('specification',1000)->index()->default('');
            $table->string('specification')->index()->default('');
            $table->string('part_no',250)->index()->default('');
            $table->string('product_no',250)->index()->default('');
            $table->string('model_no',250)->index()->default('');
            $table->string('suc_del',250)->index()->default('');
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
        Schema::dropIfExists('products');
    }
};
