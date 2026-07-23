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
        Schema::create('marketings', function (Blueprint $table) {
            $table->id();
            $table->date('event_date')->nullable();
            $table->string('event_center')->nullable();
            $table->string('place_of_participant')->nullable();
            $table->string('event_district')->nullable();
            $table->string('state')->nullable();
            $table->string('event_under_type')->nullable();
            $table->string('event_under_name')->nullable();
            $table->string('branch')->nullable();
            $table->string('responsible_for_event')->nullable();
            $table->string('branding_team_member')->nullable();
            $table->string('name_of_participant')->nullable();
            $table->string('category_of_participant')->nullable();
            $table->string('mob_no_of_participant')->nullable();
            $table->string('google_drivelink')->nullable();
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
        Schema::dropIfExists('marketings');
    }
};
