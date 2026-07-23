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
        Schema::create('branchwise_targets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unsigned()->index()->nullable();
            $table->string('user_name')->nullable();
            $table->unsignedBigInteger('branch_id')->unsigned()->index()->nullable();
            $table->string('branch_name')->nullable();
            $table->unsignedBigInteger('div_id')->unsigned()->index()->nullable();
            $table->string('division_name')->nullable();
            $table->string('month')->nullable();
            $table->date('year')->nullable();
            $table->decimal('target', 19, 2)->index()->default(0.00);
            $table->decimal('achievement', 19, 2)->index()->default(0.00);
            $table->string('type')->nullable();
            $table->decimal('amount', 19, 2)->index()->default(0.00);
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
        Schema::dropIfExists('branchwise_targets');
    }
};
