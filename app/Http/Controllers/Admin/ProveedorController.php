<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Proveedor;
use Illuminate\Support\Facades\DB;
class ProveedorController extends Controller
{
    public function listarProveedor(){
        $proveedores = Proveedor::where('id','>',0)->orderBy('nombre','asc')->get();
        return $proveedores;
    }

    public function listarProveedorServicio(){
        $proveedores = Proveedor::where('id','>',0)->where('tipo_proveedor','servicios')->orderBy('nombre','asc')->get();
        return $proveedores;
    }
    public function listarProveedorProducto(){
        $proveedores = Proveedor::where('id','>',0)->where('tipo_proveedor','productos')->orderBy('nombre','asc')->get();
        return $proveedores;
    }
    public function index($origen='admin.proveedor.index'){
        //verificar si tiene permisos para acceder a la ruta true tiene permiso false no tiene permiso
        /*$permisos = UserController::permisos(Auth::user(),'proveedor.index');        
        if($permisos==false){
            return redirect('/');
        }*/

    	return view($origen,['proveedores'=>$this->listarProveedor()]);
    }

    public function create($origen='admin.proveedor.create'){
        //verificar si tiene permisos para acceder a la ruta true tiene permiso false 
        /*$permisos = UserController::permisos(Auth::user(),'proveedor.create');        
        if($permisos==false){
            return redirect('/proveedor');
        }*/
        $porceRetencionIva = DB::select("select id,porcentaje from porcentaje_retencion_iva");
    	return view($origen,['porceRetencionIva'=>$porceRetencionIva]);
    }

    public function save(Request $request,$origen='admin.proveedor.index'){
        
    	$proveedor = new Proveedor();
    	$proveedor->nombre = $request->get('nombre');    	
    	$proveedor->direccion = $request->get('direccion');
        $proveedor->codigoFiscal = $request->get('codigoFiscal');
        $rif = $request->get('tipoRif').'-'.$request->get('rifNumero').'-'.$request->get('rifTerminal');
        $proveedor->rif = $rif;
        $proveedor->tipo_proveedor = $request->get('tipo_proveedor');
        $proveedor->porcentaje_retener = $request->get('porcentaje_retener');
        $proveedor->tipo_contribuyente = $request->get('tipo_contribuyente');
    	$proveedor->save();

    	return self::index($origen);
    }

    public function ver($id,$vista='admin.proveedor.show'){
    	return view($vista,['proveedor'=>Proveedor::findOrFail($id)]);
    }

    public function edit($rif,$origen='admin.proveedor.edit'){
        //verificar si tiene permisos para acceder a la ruta true tiene permiso false 
        /*$permisos = UserController::permisos(Auth::user(),'proveedor.edit');        
        if($permisos==false){
            return redirect('/proveedor');
        }*/

        $cadenaRif[]="";
    	$proveedor= Proveedor::where('rif',$rif)->get();
        
        if(empty($proveedor[0])){
            return self::create();

        }else{
            $porceRetencionIva = DB::select("select id,porcentaje from porcentaje_retencion_iva");             
            $cadenaRif = explode('-',$proveedor[0]->rif);
        return view($origen,['proveedor'=>$proveedor[0],'cadenaRif'=>$cadenaRif,'origen'=>$origen,'porceRetencionIva'=>$porceRetencionIva]);  
        }
        
    }

    public function update(Request $request,$id,$origen='admin.proveedor.show'){
        $descontarNotaCredito=0;
        $agregar_nota_debito=0;
        $agregar_igtf=0;
        $agregar_islr=0;

        if($request->descontar_nota_credito =='on'){
            $descontarNotaCredito = 1;
        }
        if($request->agregar_nota_debito =='on'){
            $agregar_nota_debito = 1;
        }
        if($request->agregar_igtf =='on'){
            $agregar_igtf = 1;
        } 
        if($request->agregar_islr =='on'){
            $agregar_islr = 1;
        }
        $proveedor = Proveedor::findOrFail($id);
        $rif = $request->get('tipoRif').'-'.$request->get('rifNumero').'-'.$request->get('rifTerminal');
        $proveedor->rif = $rif;
        $proveedor->nombre = $request->get('nombre');
        $proveedor->codigoFiscal = $request->get('codigoFiscal');
        $proveedor->tipo_proveedor = $request->get('tipo_proveedor');
        $proveedor->porcentaje_retener = $request->get('porcentaje_retener');
        $proveedor->direccion = $request->get('direccion');
        $proveedor->tipo_contribuyente = $request->get('tipo_contribuyente');
        $proveedor->descontar_nota_credito=$descontarNotaCredito;
        $proveedor->agregar_nota_debito=$agregar_nota_debito;
        $proveedor->agregar_igtf=$agregar_igtf;
        $proveedor->agregar_islr=$agregar_islr;
        if(!empty($request->dias_credito)){
            $proveedor->dias_credito = $request->dias_credito;
        }
        if(!empty($request->ultimo_porcentaje_retener_islr)){
            $proveedor->ultimo_porcentaje_retener_islr = $request->ultimo_porcentaje_retener_islr;
        }
        $proveedor->update(); 

        return self::ver($id,$origen);
    }

    public static function search($id){
       return $proveedor = Proveedor::findOrFail($id);
    }

    public function actualizarUltimoPorcentajeRetencionIslr($id,$porcentaje){
        Proveedor::where('id','=',$id)->update(['ultimo_porcentaje_retener_islr'=>$porcentaje]);
    }
}
