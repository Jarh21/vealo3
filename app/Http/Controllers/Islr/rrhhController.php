<?php

namespace App\Http\Controllers\Islr;

use App\Http\Controllers\Controller;
/*use Illuminate\Http\Request; */
use App\Models\Rrhh;
use App\Models\Empresa;
use App\Http\Controllers\Herramientas\HerramientasController;
use App\Http\Controllers\Islr\xmlController;
use App\Exports\dowloadExcelRRHH;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
class rrhhController extends Controller
{
    public function index()
    {
                
    	return view('islr.rrhh.index',['empleados'=>Rrhh::listarEmpleados(config('empresa_rif')),'empresas'=>Empresa::all(),'empresa_seleccionada'=>'']);
    }

    public function postIndex(Request $request)
    {
       
       
       $rif = explode('|', $request->get('empresa'));
       //variable globales
       config(['empresa_rif'=>$rif[0],'empresa_nombre'=>$rif[1]]);

        return view('islr.rrhh.index',['empleados'=>Rrhh::listarEmpleados(config('empresa_rif')),'empresas'=>Empresa::all(),'empresa_seleccionada'=>$rif[0]]);
    }

    public function importExcel(Request $request){


    	/* $file = $request->file('excel');

        $objRRhhImport = new rrhhImport();
       
    	//con toArray tengo los datos del archivo en excel en un array de 3 niveles
        $datos = Excel::toArray($objRRhhImport,$file);
       
        //comparamos si el archivo tiene el campo rif de la empresa y si existe esa empresa
        if(isset($datos[0][0]['empresa_rif']) and !empty(DB::select('select id from empresas where rif=:rifEmpresa',['rifEmpresa'=>$datos[0][0]['empresa_rif']]))){

            //accedo segun los niveles del arreglo al valor que necesito extraer el rif de la empresa
            $empresaRif = $datos[0][0]['empresa_rif'];            
            
            //colocamos en inactivo todos los registros de la empresa a importar para luego activar solo los activos
            DB::select("update rrhhs set activo=false where empresa_rif =:empresaRif",['empresaRif'=>$empresaRif]);
            //obtenemos los registros del archivo en excel
            foreach ($datos[0] as $registros) {
                //si la fecha de ingreso del archivo no es valida cancelamos la importacion

                if(is_numeric($registros['fecha'])){
                    return view('rrhh.index',['empleados'=>Rrhh::where('activo',1)->get(),'messageMalo'=>'El archivo tiene un formato de fecha no admitido, le sugerimos modificar el formato de celda del archivo excel donde se encuentra la fecha y colocarla tipo texto luego escribir nuevamente las fechas y continuar con la importaci�n, �no se realiz� la importaci�n!','empresas'=>Empresa::all(),'empresa_seleccionada'=>'']);
                }

               //si llego al final de los registros siempre quedan celdas en blanco en el archivo de excel, verificamos si no hay nombres detenerse
                if($registros['nombres']==''){
                    continue;
                }
                //buscamos si el registro no existe lo insertamos de lo contrario lo actualizamos
                $empleados=DB::select('select id from rrhhs where rif=:empleado_rif',['empleado_rif'=>trim($registros['empleado_rif'])]);

                if(empty($empleados)){
                    
                    DB::table('rrhhs')->insert([
                            'nombres' => $registros['nombres'],
                            'fecha_ingreso' => date('Y-m-d',strtotime($registros['fecha'])),
                            'sueldo_base' => $registros['salario'],
                            'rif' => trim($registros['empleado_rif']),
                            'empresa_rif' => $registros['empresa_rif'],
                            'activo' => true
                   
                            ]);
                }else{
                    
                    foreach ($empleados as $empleado) {
                        
                        
                        DB::select("update rrhhs set nombres=:nombreE, fecha_ingreso=:fechaE, sueldo_base=:sueldoE,empresa_rif=:empresa_rifE,activo=true where id=:empleadoId",['empleadoId'=>$empleado->id,'nombreE'=>$registros['nombres'],'fechaE'=>date('Y-m-d',strtotime($registros['fecha'])),'sueldoE'=>$registros['salario'],'empresa_rifE'=>trim($registros['empresa_rif'])]);
                         
                    }      
                }
            }
            //por ultimo actualizamos el campo empresa id porque lo requerimos al generar el xml
            DB::update('update rrhhs,empresas set rrhhs.empresa_id=empresas.id where rrhhs.empresa_rif =empresas.rif');
            
            return view('rrhh.index',['empleados'=>Rrhh::listarEmpleados(config('empresa_rif')),'messageBueno'=>'Importacion de datos completada','empresas'=>Empresa::all(),'empresa_seleccionada'=>'']);
        }else{
            return view('rrhh.index',['empleados'=>Rrhh::listarEmpleados(config('empresa_rif')),'messageMalo'=>"El archivo no es compatible o la empresa del archivo no se encuentra registrada, por lo tanto, no se realiz� la importaci�n",'empresas'=>Empresa::all(),'empresa_seleccionada'=>'']);
        } */       

    }

    public function exportarRrhhCsv($rif=''){
        //return (new rrhhExport)->download();
        //return Excel::download(new rrhhExport, 'Empleados.csv');
        //return (new rrhhExport($rif))->download('Empleados-'.time().'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        //return (new rrhhExport)->download('invoices.csv', \Maatwebsite\Excel\Excel::CSV);
        return Excel::download(new dowloadExcelRRHH, 'empleados_grupo_Farma_Descuento.xlsx');
    }

    public function create(){
        $empresas = Empresa::all(); 
        return view('islr.rrhh.create',['empresas'=>$empresas]);
    }

    public function saveRrhh(Request $request){

        $buscarEmpleado = Rrhh::where('rif',$request->get('rif'))->count();
	    $sueldo = HerramientasController::convertirMonto($request->get('sueldo_base'));//quitar separador de miles
        $empresa_rif = explode('|',$request->get('empresa'));
        $empresaId = Empresa::where('rif',$empresa_rif[0])->select('id')->get();
        if($buscarEmpleado==0){            

            $empleado = new Rrhh();
            $empleado->nombres = $request->get('nombres');
            $empleado->fecha_ingreso = $request->get('fecha_ingreso');
            $empleado->sueldo_base = $sueldo;
            $empleado->rif = $request->get('rif');
            $empleado->empresa_rif = $empresa_rif[0];
            $empleado->empresa_id = $empresaId[0]->id;
            $empleado->save();
           
        }else{
            $empleado = Rrhh::where('rif',$request->get('rif'));            
            $empleado->update(['activo'=>1,'fecha_ingreso'=>$request->get('fecha_ingreso'),'sueldo_base'=>$sueldo,'empresa_rif'=>$empresa_rif[0],'empresa_id'=>$empresaId[0]->id]); 			
            
        } 
	 return redirect()->route('rrhh.index');   
        
    }


    public function edit($id,$empresa,$accion='',$encabezadoId='',$fechaIniFin=''){
        
        $empleado=Rrhh::findOrFail($id);
        return view('islr.rrhh.edit',[
            'empleado'=>$empleado,
            'empresa'=>$empresa,
            'accion'=>$accion,
            'encabezadoId'=>$encabezadoId,
            'fechaIniFin'=>$fechaIniFin
        ]);
    }


    public function update(Request $request,$id,$accion='',$encabezadoId='',$fechaIniFin=''){
        $sueldo = $request->get('sueldo_base');
        $sueldo = HerramientasController::convertirMonto($sueldo);//quitar separador de miles para guardar en mysql
        $empleado=Rrhh::findOrFail($id);
        $empleado->rif = $request->get('rif');
        $empleado->fecha_ingreso = $request->get('fecha_ingreso');
        $empleado->sueldo_base = $sueldo;
        $empleado->update();

        if($accion=='xml'){
            $xmlController = new xmlController();
            $xmlController->xmlDelete($encabezadoId);//eliminamos el xml anterior

            //return $xmlController->xmlver($ultimaFechaMes,$islr->empresa_rif,$encabezadoId);
            //creamos el nuevo xml con los nuevos cambios
            return $xmlController->xmlCrearGet($fechaIniFin,$request->get('empresa_rif'));
        }else{

         return view('islr.rrhh.index',['empleados'=>Rrhh::listarEmpleados($request->get('empresa_rif')),'empresas'=>Empresa::all(),'empresa_seleccionada'=>$request->get('empresa_rif')]);
        }
        
    }
    
    public function updateMasivo(Request $request){
        $sueldoNuevo=$request->sueldo_nuevo;
        foreach($request->id as $index=>$idEmpleado){
            $rrhh = Rrhh::where('id',$idEmpleado)->update(['sueldo_base'=>$sueldoNuevo[$index]]);
        }
        return self::index();
    }


    public function destroy($id,$empresaRif,$accion='',$encabezadoId='',$fechaIniFin=''){
        $rrhh = Rrhh::findOrFail($id);        
        $rrhh->activo=false;
        $rrhh->update();
        if($accion=='xml'){
            $xmlController = new xmlController();
            $xmlController->xmlDelete($encabezadoId);//eliminamos el xml anterior

            //return $xmlController->xmlver($ultimaFechaMes,$islr->empresa_rif,$encabezadoId);
            //creamos el nuevo xml con los nuevos cambios
            return $xmlController->xmlCrearGet($fechaIniFin,$empresaRif);
        }else{
           return view('islr.rrhh.index',['empleados'=>Rrhh::listarEmpleados($rrhh->empresa_rif),'empresas'=>Empresa::all(),'empresa_seleccionada'=>$rrhh->empresa_rif]);
        }
        
    }
}
