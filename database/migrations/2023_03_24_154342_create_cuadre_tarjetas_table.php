<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuadreTarjetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuadre_tarjetas', function (Blueprint $table) {
            $table->id();
            $table->integer('empresa_id');
            $table->date('fecha')->nullable();
            $table->integer('banco_id')->nullable();
            $table->string('banco')->nullable();
            $table->decimal('monto',28,2)->nullable();
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
        Schema::dropIfExists('cuadre_tarjetas');
    }
}
