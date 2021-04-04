<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dates', function (Blueprint $table) {
            //$table->id();
            $table->Increments('DateID');
            $table->Integer('periode_id')->unsigned();
            $table->date('date');
            $table->boolean('haveSessionFromYesterday')->default(false); // pour minimiser temps de reponse
            $table->timestamps();
            $table->foreign('periode_id')->references('PeriodeID')->on('periodes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dates');
    }
}
