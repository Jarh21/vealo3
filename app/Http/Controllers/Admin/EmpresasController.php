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

	public function listarEmpresasApi(){
		return Empresa::get();
	}

	public function cambiarEmpresa($empresaRif){
		$empresa = Empresa::where('rif',$empresaRif)->first();
		
		session(['empresaRif'=>$empresa->rif,'empresaNombre'=>$empresa->nombre,'basedata'=>$empresa->basedata]);
		
    }

	public function obtenerEmpresaSeleccionada(){
        $empresa= array('empresaRif'=>session('empresaRif'),'empresaNombre'=>session('empresaNombre'));
        return json_encode($empresa);
    }

	public function create(){
		
		return view('admin.empresas.create');
	}
	public function save(Request $request){
		
		$empresa = new Empresa();
		$empresa->rif = $request->get('rif');
		$empresa->color = $request->get('color');
		$empresa->nom_corto= $request->get('nom_corto');
		$empresa->nombre=	 $request->get('nombre');	
		$empresa->direccion= $request->get('direccion');
		$empresa->telefono= $request->get('telefono');
		$empresa->basedata=$request->get('basedata');
		if($empresa->save()){
			return redirect('/admin/empresas');
		}else{
			return redirect('/admin/empresas');
		}
	}

	public function edit($id){
		
		return view('admin.empresas.edit',['empresa' => Empresa::findOrFail($id)]);

	}

	public function update(Request $request,$id){
		$agenteRetencion=0;
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
		return redirect('/admin/empresas'); 
	}

	public function delete($id){
		
		$empresa = Empresa::findOrFail($id);
		if($empresa->delete()){
			return redirect('/admin/empresas');
		}else{
			return redirect('/admin/empresas');
		}
	}
}
