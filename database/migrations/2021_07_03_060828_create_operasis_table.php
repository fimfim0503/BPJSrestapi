<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperasisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operasis', function (Blueprint $table) {
            $table->id();
            $table->string('kodebooking',15);
            $table->date('tanggaloperasi');
            $table->string('jenistindakan');
            $table->char('kodepoli',3);
            $table->char('terlaksana',1);
            $table->string('nopeserta');
            $table->string('lastupdate');
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
        Schema::dropIfExists('operasis');
    }
}
