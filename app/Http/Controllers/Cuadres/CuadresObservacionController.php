<?php

namespace App\Http\Controllers\Cuadres;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cuadres\CuadresObservacion;
use App\Http\Controllers\Herramientas\HerramientasController;

class CuadresObservacionController extends Controller
{

    private $herramientas='';
    private $conexionSQL='';
    public function __construct(){
        /*
        iiciamos herramientas para conexiones externas
        */
        $this->herramientas = new HerramientasController();
        
    }

    public function index(){
        return CuadresObservacion::get();
    }

    public function obtenerObservacionCuadre($tipoObservacion){
        return CuadresObservacion::where('tipo_observacion',$tipoObservacion)
        ->where('fecha',session('fechaCuadre'))
        ->where('empresa_rif',session('empresaRif'))
        ->get();
    }

    public function listaEmpleadosCuadre(){
        $this->conexionSQL = $this->herramientas->conexionDinamicaBD(session('basedata'));
        return $this->conexionSQL->select("SELECT codusua, usuario FROM arqueo_caja WHERE cerrado=1 AND fecha=:fechaCuadre GROUP BY usuario",['fechaCuadre'=>session('fechaCuadre')]);
    }


    public function guardarObservacion(Request $request){
       //return json_encode(['mensaje'=>$request->all()]);
        //CuadresObservacion::create(['fecha'=>date('Y-m-d'),'usuario'=>'jose rivero','tipo_observacion'=>$request->tipo_observacion,'monto'=>$request->monto,'numero'=>$request->numero,'aprobacion'=>$request->aprobacion]);
        $observacion = new CuadresObservacion();
        $codEmpleado='';
        $nomEmpleado='';
        if(isset($request->empleado)){
            $empleado = explode('|',$request->empleado);//separamos el codigo del nombre del empleado
            $codEmpleado=$empleado[0];
            $nomEmpleado=$empleado[1];
            $observacion->cod_usuario = $codEmpleado;
            $observacion->usuario = $nomEmpleado;
        }
        if(isset($request->tipo_observacion)){
            $observacion->tipo_observacion = $request->tipo_observacion;
        }
        if(isset($request->observacion)){
            $observacion->observacion = $request->observacion;
        }
        if(isset($request->monto)){
          $observacion->monto = $request->monto;  
        }
        if(isset($request->tarjeta)){
           $observacion->numero = $request->tarjeta; 
           $observacion->aprobacion = $request->aprobacion;
        }
        if(isset($request->bancos)){
            $observacion->banco = $request->bancos;
        }
        $observacion->empresa_rif = session('empresaRif');
        $observacion->fecha = session('fechaCuadre');   
        
        
        $observacion->save();
        
    }

    public function eliminarObservacionCuadre($id){
        $observacion = CuadresObservacion::find($id);
        $observacion->delete();
    }

}
