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
        Schema::create('scheme_headers', function (Blueprint $table) {
            $table->id();
            $table->string('active',1)->default('Y');
            $table->string('scheme_name',250)->index();
            $table->string('scheme_description',450)->index()->default('');
            $table->date('start_date')->index();
            $table->date('end_date')->index();
            $table->date('points_start_date')->nullable();
            $table->date('points_end_date')->nullable();
            $table->bigInteger('block_points')->default(0);
            $table->bigInteger('block_percents')->default(0);
            $table->string('scheme_image',450)->index()->default('');
            $table->string('scheme_type',200)->default('');
            $table->decimal('point_value', 8, 2)->default(0);
            $table->string('regions',450)->default('');
            $table->tinyInteger('redeem_percents')->default(0);
            $table->string('schemes',450)->default('');
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
        Schema::dropIfExists('scheme_headers');
    }
};
