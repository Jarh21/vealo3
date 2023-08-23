<?php

namespace App\Http\Controllers\Cuadres;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Herramientas\HerramientasController;

class CuadresController extends Controller
{
    private $empresas = '';
    private $herramientas='';
    public function __construct(){
        /*
        buscamos las empresas registradas y sus datos para la vista
        */
        $this->herramientas = new HerramientasController();
        $this->empresas= $this->herramientas->listarEmpresas();
    }

    public function index(){
        $anioYmesCuadre = date("Y-m");
        return view('cuadres.index',['anioYmesCuadre'=>$anioYmesCuadre,'empresas'=>$this->empresas]);
    }

    public function buscarMes(Request $request){
        $anioYmesCuadre = $request->anioYmesCuadre;        
        return view('cuadres.index',['anioYmesCuadre'=>$anioYmesCuadre,'empresas'=>$this->empresas]);
    }

    public function vistaRegistrarCuadre(){
        
    	
        return view('cuadres.registrarCuadre');
    }

    public function seleccionFechaRegistroCuadre(Request $request){
        $conexionSQL = $this->herramientas->conexionDinamicaBD(session('basedata'));
        $reporteZetas = $conexionSQL->select("select * from reporte_zeta where fecha=:fecha",[$request->fecha]);
        $reportesCxc = $conexionSQL->select("SELECT equipo,fecha,codorigen,fiscalserial,fiscalz,SUM(debitos)debitos,SUM(creditos)creditos,SUM(exento)exento,SUM(gravado)gravado,SUM(montoiva)iva FROM cxc  WHERE fecha='2023-03-20' AND codorigen IN(1000,1001) GROUP BY fiscalz,fiscalserial,codorigen");
        session(['fechaCuadre'=>$request->fecha]);
        $fechaCuadre =  $request->fecha;
        return view('cuadres.registrarCuadre',compact('reporteZetas','reportesCxc','fechaCuadre'));
    }
}
