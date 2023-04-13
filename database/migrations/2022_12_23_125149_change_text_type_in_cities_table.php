<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTextTypeInCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->binary('about_city')->change()->default('{"en": "", "ru": "", "ch": "", "am": "", "fr": ""}')->after('name');
            $table->binary('description')->change()->default('{"en": "", "ru": "", "ch": "", "am": "", "fr": ""}')->after('about_city');
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
