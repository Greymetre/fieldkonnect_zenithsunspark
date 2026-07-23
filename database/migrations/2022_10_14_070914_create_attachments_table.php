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
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->string('active',1)->default('Y');
            $table->unsignedBigInteger('product_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('user_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('customer_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('order_id')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('sales_id')->unsigned()->index()->nullable();
            $table->string('file_path',450)->index()->default('');
            $table->string('document_name',250)->index()->default('');
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
        Schema::dropIfExists('attachments');
    }
};
