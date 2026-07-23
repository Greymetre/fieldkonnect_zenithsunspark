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
        Schema::create('coupon_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('active',1)->default('Y');
            $table->string('profile_name',250)->index();
            $table->string('coupon_length',250)->default('8');
            $table->string('excluding_character',450)->default('');
            $table->string('coupon_count',50)->default('');
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
        Schema::dropIfExists('coupon_profiles');
    }
};
