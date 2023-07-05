<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banco;
class BancoController extends Controller
{
    public function index(){
    	return view('admin.banco.index',['bancos'=>Banco::all()]);
    }

    public function listaBancos(){
        return Banco::get();
    }

    public function create(Request $request){
    	$banco = new Banco();
    	$banco->nombre = $request->get('nombre');
    	$banco->nombre_corto = $request->get('nombreCorto');        
        $banco->primeros_cuatro_digitos = $request->get('primeros_cuatro_digitos');
    	$banco->save();

    	return view('admin.banco.index',['bancos'=>Banco::all()]);
    }

    public function edit($id){
        $banco = Banco::findOrFail($id);

        return view('admin.banco.edit',['banco'=>$banco]);
    }

    public function update(Request $request,$id){
        $banco = Banco::findOrFail($id);
        $banco->nombre = $request->get('nombre');
        $banco->nombre_corto = $request->get('nombre_corto');
        $banco->primeros_cuatro_digitos = $request->get('primeros_cuatro_digitos');
        $banco->update();

        return redirect('/bancos');
    }

    public function delete($id){
        $banco = Banco::findOrFail($id);
        $banco->delete();
        return redirect('/bancos');
    }
}
