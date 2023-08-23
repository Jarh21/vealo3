<?php

namespace App\Http\Controllers\AsistenteCompras;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Herramientas\HerramientasController;
class AsistenteComprasController extends Controller
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
        
        return view('asistenteCompras.visualizadorPrecios');
    }

    public function apiListadoPrecioDrogueria(){
        $conexionSQL = $this->herramientas->conexionDinamicaBD('hptal');
        $preciosDroguerias = $conexionSQL->select("select * from visualizador_precios_drogueria_descripcion order by costo,drogueria");
        
        /* return datatables($preciosDroguerias)
        ->addColumn('btn','asistenteCompras.acciones')//agregamos una columna y va a contener la vista que se encuentra en asistenteCompras.acciones esto es como una plantilla
        ->rawColumns(['btn'])//para que renderice(interpretar) el html
        ->toJson(); */
        return $preciosDroguerias;
    }


    public function guardarPedidoDetallado(Request $request){
        $empresas = $request->empresaRif;
        
        return $request;
    }
}
