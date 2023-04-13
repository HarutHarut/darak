<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDescriptionColumnInStaticPages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('static_pages', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->binary('description_am')->nullable()->default('');
            $table->binary('description_ru')->nullable()->default('');
            $table->binary('description_en')->nullable()->default('');
            $table->binary('description_sp')->nullable()->default('');
            $table->binary('description_ch')->nullable()->default('');
            $table->binary('description_de')->nullable()->default('');
            $table->binary('description_fr')->nullable()->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('static_pages', function (Blueprint $table) {
            //
        });
    }
}
