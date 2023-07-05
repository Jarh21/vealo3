<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpresasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('rif')->nullable();
            $table->string('color')->nullable();
            $table->string('nom_corto')->nullable();
            $table->string('nombre')->nullable();
            $table->string('servidor')->nullable();
            $table->integer('puerto')->nullable();
            $table->string('nomusua')->nullable();
            $table->string('basedata')->nullable();
            $table->string('clave')->nullable();
            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('firma')->nullable();
            $table->integer('is_agente_retencion')->default(0);
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
        Schema::dropIfExists('empresas');
    }
}
