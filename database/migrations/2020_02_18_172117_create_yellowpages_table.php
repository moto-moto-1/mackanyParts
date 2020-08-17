<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYellowpagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yellowpages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('url');
            $table->string('cat');
            $table->string('cat_num');
            $table->string('search_id');
            $table->string('name');
            $table->text('address');
            $table->text('about');
            $table->string('tel');
            $table->string('other_tel');
            $table->string('web');
            $table->string('map');
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
        Schema::dropIfExists('yellowpages');
    }
}
