<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('booking_number')->unique();
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('user_id');
            $table->dateTime('check_in');
            $table->dateTime('check_out');
            $table->float('price');
            $table->text('currency');
            $table->string('status')->default('pending');
            $table->string('pay_type')->default('cache');
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
        Schema::dropIfExists('orders');
    }
}
