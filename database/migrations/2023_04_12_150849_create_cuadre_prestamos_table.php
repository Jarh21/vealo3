<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuadrePrestamosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuadre_prestamos', function (Blueprint $table) {
            $table->id();
            $table->string('empresa_rif')->nullable();
            $table->date('fecha')->nullable();
            $table->string('rif')->nullable();
            $table->string('nombre')->nullable();
            $table->string('descripcion')->nullable();
            $table->decimal('monto',28,2)->nullable();
            $table->string('creado_por')->nullable();
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
        Schema::dropIfExists('cuadre_prestamos');
    }
}
