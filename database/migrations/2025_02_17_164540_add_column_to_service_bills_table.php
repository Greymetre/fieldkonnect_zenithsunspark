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
        Schema::table('service_bills', function (Blueprint $table) {
            $table->string('line_voltage')->nullable()->after('replacement_tag_number');
            $table->string('load_voltage')->nullable()->after('line_voltage');
            $table->string('current')->nullable()->after('load_voltage');
            $table->string('water_source')->nullable()->after('current');
            $table->string('panel_rating_running')->nullable()->after('water_source');
            $table->string('panel_rating_starting')->nullable()->after('panel_rating_running');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_bills', function (Blueprint $table) {
            $table->dropColumn(['line_voltage', 'load_voltage', 'current', 'water_source', 'panel_rating_running', 'panel_rating_starting']);
        });
    }
};
