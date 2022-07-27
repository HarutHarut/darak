<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateErrorLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('error_logs', function (Blueprint $table) {
            $table->id();
            $table->string('level');
            $table->string('category', 255);
            $table->integer('user_id')->nullable();
            $table->string('user_ip', 255)->nullable();
            $table->text('message');
            $table->string('server_ip', 255)->nullable();
            $table->string('request_url', 255)->nullable();
            $table->string('request_id', 255)->nullable();
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
        Schema::dropIfExists('error_logs');
    }
}
