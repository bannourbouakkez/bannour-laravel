<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sessions', function (Blueprint $table) {
            //$table->id();
            $table->Increments('SessionID');
            $table->Integer('date_id')->unsigned();
            $table->dateTime('timeStart');
            $table->dateTime('timeEnd');
            $table->boolean('isReserved')->default(false);
            $table->timestamps();
            $table->foreign('date_id')->references('DateID')->on('dates')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sessions');
    }
}
