<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewTextColumnsInCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->string('about_city')->nullable()->default('{"en": "", "ru": "", "ch": "", "am": "", "fr": ""}')->after('name');
            $table->string('description')->nullable()->default('{"en": "", "ru": "", "ch": "", "am": "", "fr": ""}')->after('about_city');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cities', function (Blueprint $table) {
            //
        });
    }
}
