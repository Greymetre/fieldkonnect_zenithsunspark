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
        Schema::create('loyalty_app_settings', function (Blueprint $table) {
            $table->id();
            $table->text('customer_types')->nullable();
            $table->text('product_catalogue')->nullable();
            $table->text('scheme_catalogue')->nullable();
            $table->text('terms_condition')->nullable();
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
        Schema::dropIfExists('loyalty_app_settings');
    }
};
