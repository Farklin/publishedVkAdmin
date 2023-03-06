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
        Schema::table('parsing_chanel_words', function (Blueprint $table) {
            $table->integer('count_view')->default(0);
            $table->integer('count_published_post')->default(0);  
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('parsing_chanel_words', function (Blueprint $table) {
            $table->dropColumn('count_view');
            $table->dropColumn('count_published_post');
        });
    }
};
