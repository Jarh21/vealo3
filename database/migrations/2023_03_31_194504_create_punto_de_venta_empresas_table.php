<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePuntoDeVentaEmpresasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('punto_de_venta_empresas', function (Blueprint $table) {
            $table->id();
            $table->string('empresa_rif')->nullable();
            $table->integer('banco_id')->nullable();
            $table->string('numero_de_afiliacion')->nullable();
            $table->string('numero_de_terminal')->nullable();
            $table->boolean('is_prestado')->default(false);
            $table->boolean('is_activo')->default(true);
            $table->string('prestamista_rif')->nullable();
            $table->string('prestamista_nombre')->nullable();            
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
        Schema::dropIfExists('punto_de_venta_empresas');
    }
}
