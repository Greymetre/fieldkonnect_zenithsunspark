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
        Schema::create('user_logins', function (Blueprint $table) {
            $table->id();
            $table->string('active',1)->default('Y');
            $table->bigInteger('user_id')->unsigned()->index()->nullable();
            $table->string('entry_from',250)->index()->default('');
            $table->string('provider',250)->index()->default('');
            $table->string('mobile',250)->index()->default('');
            $table->dateTime('login_at')->index()->nullable();
            $table->dateTime('logout_at')->index()->nullable();
            $table->softDeletes('deleted_at');
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
        Schema::dropIfExists('user_logins');
    }
};
