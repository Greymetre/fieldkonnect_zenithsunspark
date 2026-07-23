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
        Schema::create('deal_ins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->unsigned()->index()->nullable();
            $table->string('types',150)->index();
            $table->boolean('hcv')->default(false);
            $table->boolean('mav')->default(false);
            $table->boolean('lmv')->default(false);
            $table->boolean('lcv')->default(false);
            $table->boolean('other')->default(false);
            $table->boolean('tractor')->default(false);
            $table->timestamps();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deal_ins');
    }
};
