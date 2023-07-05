<?php

namespace App\Http\Controllers\Islr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmpleadoDeclarante;
use App\Models\Contribuyente;
use App\Models\Empresa;
use App\Http\Controllers\Herramientas\HerramientasController;
use App\Http\Controllers\Islr\xmlController;
use Illuminate\Support\Facades\DB;

class EmpleadoDeclaranteController extends Controller
{
    //$accion='',$encabezadoId='',$fechaIniFin=''
    public function index(){
        
        $declarantes = DB::select('SELECT empleado_declarantes.id,empleado_declarantes.rif,empleado_declarantes.nombre,contribuyentes.nombre AS contribuyente_id,empleado_declarantes.sueldo_base,empleado_declarantes.empresa,empleado_declarantes.e_rif FROM empleado_declarantes,contribuyentes WHERE empleado_declarantes.contribuyente_id=contribuyentes.id');
    	return view('islr.empleadosDeclarantes.index',['declarantes'=>$declarantes]);
    }

    
    public function create(){
       
        
        $contribuyentes=DB::select('select * from contribuyentes');
        $empresas = Empresa::all();        
    	return view('islr.empleadosDeclarantes.create',['tiposDirectivos'=>$contribuyentes,'empresas'=>$empresas]);
    }


    public function edit($id,$accion='',$encabezadoId='',$fechaIniFin=''){
        $declarante = EmpleadoDeclarante::findOrFail($id);
        $contribuyentes=DB::select('select * from contribuyentes');
        $empresas = Empresa::all();        
        return view('islr.empleadosDeclarantes.edit',[
            'tiposDirectivos'=>$contribuyentes,
            'empresas'=>$empresas,
            'declarante'=>$declarante,
            'accion'=>$accion,
            'encabezadoId'=>$encabezadoId,
            'fechaIniFin'=>$fechaIniFin
        ]); 
    }

    public function updateSalarios(Request $request,$id,$accion='',$encabezadoId='',$fechaIniFin=''){
        //actualizamos el salario de los empleados declarantes como directivos
        //solo la jefa se RRHH tiene acceso
        $sueldo = HerramientasController::convertirMonto($request->get('sueldo'));
        $empresa = explode('|', $request->get('empresa'));
        $declarante=EmpleadoDeclarante::findOrFail($id);
        $declarante->rif = $request->get('rif');
        $declarante->nombre = $request->get('nombre');
        $declarante->contribuyente_id=$request->get('tipoDirectivo');
        $declarante->e_rif = $empresa[0];
        $declarante->empresa = $empresa[1];
        $declarante->sueldo_base = $sueldo;
        $declarante->fecha = $request->get('fecha');
        $declarante->update();
        
        if($accion=='xml'){

            $xmlController = new xmlController();
            $encabezadoXml = $xmlController->xmlEncabezadoConsultar($encabezadoId);
            $xmlController->xmlDelete($encabezadoId);//eliminamos el xml anterior

            //return $xmlController->xmlver($ultimaFechaMes,$islr->empresa_rif,$encabezadoId);
            //creamos el nuevo xml con los nuevos cambios
            return $xmlController->xmlCrearGet($fechaIniFin,$encabezadoXml->rif_empresa);
        }else{
        
            return redirect('/declarantes');
        }     

    }


    public function save(Request $request){
        $empresa=$request->get('empresa');
        $empresa=explode('|',$empresa);
        $monto = HerramientasController::convertirMonto($request->get('sueldo'));
    	$declarantes = new EmpleadoDeclarante();
    	$declarantes->rif = $request->get('rif');
    	$declarantes->nombre = $request->get('nombre');
    	$declarantes->contribuyente_id = $request->get('tipoDirectivo');
        $declarantes->e_rif = $empresa[0];
        $declarantes->empresa = $empresa[1];
        $declarantes->sueldo_base = $monto;
        $declarantes->fecha = $request->get('fecha');
    	$declarantes->save();
    	return redirect('/declarantes');
    }


    public function destroy($id,$empresaRif='',$accion='',$encabezadoId='',$fechaIniFin=''){
       
    	$declarante = EmpleadoDeclarante::findOrFail($id);
    	$declarante->delete();
        if($accion == 'xml'){
            $xmlController = new xmlController();
            $xmlController->xmlDelete($encabezadoId);//eliminamos el xml anterior

            //return $xmlController->xmlver($ultimaFechaMes,$islr->empresa_rif,$encabezadoId);
            //creamos el nuevo xml con los nuevos cambios
            return $xmlController->xmlCrearGet($fechaIniFin,$empresaRif);
        }else{
    	   return redirect('/declarantes');
        }
    }
}
