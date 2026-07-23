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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->string('active',1)->default('Y');
            $table->unsignedBigInteger('user_id')->unsigned()->index()->nullable();
            $table->date('punchin_date')->index();
            $table->time('punchin_time')->index();
            $table->string('punchin_longitude',250)->index()->nullable();
            $table->string('punchin_latitude',250)->index()->nullable();
            $table->string('punchin_address',250)->index()->default('');
            $table->string('punchin_image',400)->index()->default('');
            $table->date('punchout_date')->index()->nullable();
            $table->time('punchout_time')->index()->nullable();
            $table->string('punchout_latitude',250)->index()->nullable();
            $table->string('punchout_longitude',250)->index()->nullable();
            $table->string('punchout_address',250)->index()->default('');
            $table->string('punchout_image',400)->index()->default('');
            //$table->text('punchin_summary')->index()->default('');
            $table->string('punchin_summary')->index()->default('');
           // $table->text('punchout_summary')->index()->default('');
            $table->string('punchout_summary')->index()->default('');
            $table->string('worked_time',50)->index()->default('');
            $table->softDeletes('deleted_at');
            $table->timestamps();
            $table->string('working_type',400)->index()->default('fields');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendances');
    }
};
