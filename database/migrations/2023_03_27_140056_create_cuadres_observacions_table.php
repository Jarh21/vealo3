<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuadresObservacionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuadres_observacions', function (Blueprint $table) {
            $table->id();
            $table->string('empresa_rif');
            $table->date('fecha');
            $table->integer('cod_usuario')->nullable();
            $table->string('usuario')->nullable();
            $table->string('tipo_observacion');
            $table->string('sumarOrestar',5);
            $table->decimal('monto',28,2)->nullable();
            $table->string('numero')->nullable();
            $table->string('aprobacion')->nullable();
            $table->string('banco')->nullable();
            $table->string('observacion')->nullable();
            $table->string('creado_por')->nullable();
            $table->string('actualizado_por')->nullable();
            $table->string('eliminado_por')->nullable();
            $table->integer('es_eliminado')->default(0);
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
        Schema::dropIfExists('cuadres_observacions');
    }
}
