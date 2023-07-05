<?php

namespace App\Http\Controllers\RecepcionDivisas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Herramientas\HerramientasController;

class OperacionesPuntosController extends Controller
{
    public function index(){
		$herramientas = new HerramientasController();
		return view('divisasCustodio.relacionPuntosVentas.index',['empresas'=>$herramientas->listarEmpresas()]);
	}


    public function relacionPorcentualPuntosVentas(Request $request){
    	
    	$fecha 	   = $request->get('fecha');
    	$conexion  = $request->get('conexion');
    	$herramientas = new HerramientasController();
    	$conexionSQL = $herramientas->conexionDinamicaBD($conexion);
    	$registros = $conexionSQL->select("
    		SELECT
			  entidad,
			  CONCAT(ROUND((SUM(monto) * 100) / (SELECT SUM(monto) total_bancos FROM mov_pagos WHERE fecha=:valor1 AND codentidad IN(1,3,10)),2),'%') as detallado_del_dia
			FROM
			  mov_pagos
			WHERE
			  fecha =:fechas
			  AND codentidad IN(1, 3, 10)
			GROUP BY entidad
		",['valor1'=>$fecha,'fechas'=>$fecha]);
    	return view('divisasCustodio.relacionPuntosVentas.index',[
    		'registros'=>$registros,
    		'fecha'=>$request->get('fecha'),
    		'conexion'=>$request->get('conexion'),
    		'empresas'=>$herramientas->listarEmpresas()
    	]);
    }
}
