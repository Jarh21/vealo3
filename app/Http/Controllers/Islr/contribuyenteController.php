<?php

namespace App\Http\Controllers\Islr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contribuyente;
class contribuyenteController extends Controller
{
    public function index()
    {

    	return view('islr.contribuyente.index',['contribuyentes'=>Contribuyente::all()]);
    }
    
    public function create(){
    	return view('islr.contribuyente.create');
    }
 
    public function save(Request $request)
    {
    	$contribuyentes = new Contribuyente();
    	$contribuyentes->nombre = $request->get('nombre');
    	$contribuyentes->codigo = $request->get('codigo');
    	$contribuyentes->porcentaje_retencion = $request->get('porcentaje_retencion');
    	$contribuyentes->save();
    	return redirect()->route('contribuyente.index');
    }

    public function edit($id){

        return view('islr.contribuyente.edit',['contribuyente'=>Contribuyente::findOrFail($id)]);
    }

    public function destroy($id){
    	$contribuyente = Contribuyente::findOrFail($id);
    	$contribuyente->delete();
    	return redirect()->route('contribuyente.index');
    }

    public function update(Request $request,$id){
        $contribuyente = Contribuyente::findOrFail($id);
        $contribuyente->nombre = $request->get('nombre');
        $contribuyente->codigo = $request->get('codigo');
        $contribuyente->porcentaje_retencion = $request->get('porcentaje_retencion');
        $contribuyente->update();
        return redirect()->route('contribuyente.index');
    }
}
