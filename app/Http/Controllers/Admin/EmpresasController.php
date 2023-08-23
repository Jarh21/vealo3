<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Herramientas\HerramientasController;
use App\Models\Empresa;

class EmpresasController extends Controller
{
    public function index(){
		

		return view('admin.empresas.index',['empresas'=>Empresa::all()]);
	}


	public function listarSeleccion(){
		 //********Datos de las Empresas**********////
        $herramientas  = new HerramientasController();
        $empresa = $herramientas->listarEmpresas();
		return view('admin.empresas.seleccion',['empresas'=>$empresa]);
	}


	public function create(){
		

		return view('admin.empresas.create');
	}
	public function save(Request $request){
		$agenteRetencion=0;
		$sincronizacion =0;
		if($request->is_sincronizacion_remota=='on'){
			$sincronizacion=1;
		}else{
			$sincronizacion = 0;
		}
		if($request->is_agente_retencion =='on'){
			$agenteRetencion=1;
		}else{
			$agenteRetencion=0;
		}

		$empresa = new Empresa();
		
		$empresa->rif = $request->get('rif');
		$empresa->color = $request->get('color');
		$empresa->nom_corto= $request->get('nom_corto');
		$empresa->nombre=	 $request->get('nombre');	
		$empresa->direccion= $request->get('direccion');
		$empresa->telefono= $request->get('telefono');
		$empresa->servidor = $request->get('servidor');
		$empresa->puerto = $request->get('puerto');
		$empresa->nomusua = $request->get('nomusua');
		$empresa->clave = $request->get('clave');
		$empresa->basedata = $request->get('basedata');
		$empresa->servidor2 = $request->get('servidor2');
		$empresa->puerto2 = $request->get('puerto2');
		$empresa->nomusua2 = $request->get('nomusua2');
		$empresa->clave2 = $request->get('clave2');
		$empresa->basedata2 = $request->get('basedata2');
		$empresa->is_agente_retencion = $agenteRetencion;
		$empresa->is_sincronizacion_remota = $sincronizacion;
		if($empresa->save()){
			return redirect()->route('admin.empresas.index');
		}else{
			return redirect()->route('admin.empresas.index');
		}
	}

	public function edit($id){
		
		return view('admin.empresas.edit',['empresa' => Empresa::findOrFail($id)]);

	}

	public function update(Request $request,$id){
		$agenteRetencion=0;
		$sincronizacion =0;
		if($request->is_sincronizacion_remota=='on'){
			$sincronizacion=1;
		}else{
			$sincronizacion = 0;
		}
		if($request->is_agente_retencion =='on'){
			$agenteRetencion=1;
		}else{
			$agenteRetencion=0;
		}
		$empresa = Empresa::findOrFail($id);
		$empresa->rif = $request->get('rif');
		$empresa->color = $request->get('color');
		$empresa->nom_corto = $request->get('nom_corto');
		$empresa->nombre = $request->get('nombre');
		$empresa->direccion = $request->get('direccion');
		$empresa->telefono = $request->get('telefono');
		$empresa->is_agente_retencion = $agenteRetencion;
		$empresa->servidor = $request->get('servidor');
		$empresa->puerto = $request->get('puerto');
		$empresa->nomusua = $request->get('nomusua');
		$empresa->clave = $request->get('clave');
		$empresa->basedata = $request->get('basedata');
		$empresa->is_sincronizacion_remota = $sincronizacion;
		$empresa->servidor2 = $request->get('servidor2');
		$empresa->puerto2 = $request->get('puerto2');
		$empresa->nomusua2 = $request->get('nomusua2');
		$empresa->clave2 = $request->get('clave2');
		$empresa->basedata2 = $request->get('basedata2');
		//copiar el archivo a la carpeta storage/app/imagen
    /*    if($request->hasFile('firma')){
            $request->file('firma')->store('imagen');        
            $empresa->firma = 'storage/app/'.$request->file('firma')->store('imagen');
        }*/


        if($request->hasfile('firma')){

			$file = $request->file('firma');
			$destinatinoPath ='imagen/';
			$filename = time().'-'.$file->getClientOriginalName();
			$uploadsuccess = $request->file('firma')->move($destinatinoPath,$filename);    
			$empresa->firma= $destinatinoPath.$filename;

		}

		$empresa->update();
		return redirect()->route('admin.empresas.index'); 
	}

	public function delete($id){
		

		$empresa = Empresa::findOrFail($id);
		if($empresa->delete()){
			return redirect()->route('admin.empresas.index');
		}else{
			return redirect()->route('admin.empresas.index');
		}
	}
}
