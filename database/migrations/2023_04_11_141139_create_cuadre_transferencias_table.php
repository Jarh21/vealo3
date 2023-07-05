<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuadreTransferenciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuadre_transferencias', function (Blueprint $table) {
            $table->id();
            $table->string('empresa_rif')->nullable();
            $table->date('fecha')->nullable();
            $table->integer('banco_emisor_id')->nullable();
            $table->integer('banco_receptor_id')->nullable();
            $table->string('numero_transferencia')->nullable();
            $table->string('descripcion')->nullable();
            $table->date('fecha_transferencia')->nullable();
            $table->decimal('monto',28,2)->nullable();
            $table->string('creado_por')->nullable();
            $table->string('actualizado_por')->nullable();
            $table->string('eliminado_por')->nullable();
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
        Schema::dropIfExists('cuadre_transferencias');
    }
}
