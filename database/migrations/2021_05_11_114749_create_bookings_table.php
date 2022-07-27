<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booker_id')->constrained('users');
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('locker_id')->constrained();
            $table->dateTime('start');
            $table->dateTime('end');
            $table->integer('amount');
            $table->integer('status')->default(config('constants.booking_status.active'));
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
        Schema::dropIfExists('bookings');
    }
}
