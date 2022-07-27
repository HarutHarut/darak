<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_to_id')->constrained('users');
            $table->string('from_email')->nullable();
            $table->string('to_email')->nullable();
            $table->string('cc')->nullable();
            $table->string('bcc')->nullable();
            $table->string('subject')->nullable();
            $table->binary('content')->nullable();
            $table->string('priority')->nullable();
            $table->integer('attempts')->default(0);
            $table->string('template_name')->nullable();
            $table->integer('status')->default(0);
            $table->timestamp('schedule_date')->nullable();
            $table->timestamp('date_sent')->nullable();
            $table->string('email_type')->nullable();
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
        Schema::dropIfExists('emails');
    }
}
