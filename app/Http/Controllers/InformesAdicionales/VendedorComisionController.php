<?php

namespace App\Http\Controllers\InformesAdicionales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ParametroCalculoComision;
use App\Http\Controllers\Herramientas\HerramientasController;
class VendedorComisionController extends Controller
{
    public function index(){
        if(empty(session('empresaRif'))){
            return view('informesAdicionales.vendedores.comisionPorVendedor',['empresas'=>$herramientas->listarEmpresas()]);
        }

        $herramientas = new HerramientasController();
        $conexionSQL = $herramientas->conexionDinamicaBD(session('basedata'));
        $usuarios =  $conexionSQL->select("SELECT keycodigo,nombre from usuarios");
        $grupos = $conexionSQL->select("SELECT keycodigo,nombre from grupos");
        return view('informesAdicionales.vendedores.comisionPorVendedor',['parametros'=>ParametroCalculoComision::where('empresa_rif',session('empresaRif'))->get(),'empresas'=>$herramientas->listarEmpresas(),'usuarios'=>$usuarios,'grupos'=>$grupos]);
    }

    public function guardar(Request $request){
        //si el campo id_parametro_edit no es vacio es porque se esta editando un registro
        //y ese id es del registro que queremos editar, antes de guardar los datos eliminamos el registro anterior 
        if(!empty($request->id_parametro_edit)){
            $registroAelimanar = ParametroCalculoComision::find($request->id_parametro_edit);
            $registroAelimanar->delete();
        }
        $isForaneo=0;
        $vendedoresEspeciales='';        
        if($request->is_foraneo=='on'){
            $isForaneo=1;
        }
        if(!empty($request->vendedores_especiales_id)){
            $vendedoresEspeciales = implode(',',$request->vendedores_especiales_id);
            
        }
        $parametroComision = new ParametroCalculoComision();
        $parametroComision->codgrupo = $request->grupo_usuario;
        $parametroComision->porcentaje_calculo_comision = $request->porcentaje_calculo_comision;
        $parametroComision->porcentaje_descuento_comision = $request->porcentaje_descuento_comision;
        $parametroComision->vendedores_especiales_id = $vendedoresEspeciales;
        $parametroComision->is_foraneo = $isForaneo;
        $parametroComision->empresa_rif = session('empresaRif');
        $parametroComision->save();

        return self::index();
    }

    public function editar($id){
        $parametroSeleccionado = ParametroCalculoComision::find($id);
        $herramientas = new HerramientasController();
        $conexionSQL = $herramientas->conexionDinamicaBD(session('basedata'));
        $usuarios =  $conexionSQL->select("SELECT keycodigo,nombre from usuarios");
        $grupos = $conexionSQL->select("SELECT keycodigo,nombre from grupos");
       
        return view('informesAdicionales.vendedores.comisionPorVendedor',['parametros'=>ParametroCalculoComision::all(),'parametroSeleccionado'=>$parametroSeleccionado,'empresas'=>$herramientas->listarEmpresas(),'usuarios'=>$usuarios,'grupos'=>$grupos]);

    }

    public function eliminar($id){
        $parametros = ParametroCalculoComision::find($id);
        
         if($parametros->delete()){
            return self::index();
        }else{
            return self::index();
        }
    }
}
