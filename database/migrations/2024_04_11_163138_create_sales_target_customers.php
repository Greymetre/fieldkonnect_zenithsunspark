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
        Schema::create('salestargetcustomers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->unsigned()->index()->nullable();
            $table->string('month')->nullable();
            $table->date('year')->nullable();
            $table->decimal('target', 19, 2)->index()->default(0.00);
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
        Schema::dropIfExists('salestargetcustomers');
    }
};
