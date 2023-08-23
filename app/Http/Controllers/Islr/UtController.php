<?php

namespace App\Http\Controllers\Islr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UnidadTributaria;
use App\Http\Controllers\Islr\RetencionController;



class UtController extends Controller
{
    public function index()
   {
      
        $ut = UnidadTributaria::all();
   	    return view('islr.ut.index',['uts'=>$ut]);
   }

   public function save(Request $request)
   {

   		$ut = new UnidadTributaria();
   		$ut->anio = $request->get('anio');
   		$ut->monto = $request->get('monto');
   		$ut->observacion = $request->get('observacion');

   		$ut->save();

   		return self::index();
   }

   public function create(){
      
      //verificamos si ya se creoun registro del Valor de la unidad tributaria si ya se creo regresa al index de locontrario abre la vista crear con su formulario
      $ut = UnidadTributaria::count();
      if($ut==0){
         return view('islr.ut.create');
      }else{
         return back();
      }
      
   }

   public function edit($id)
   {
      

   	$ut = UnidadTributaria::findOrFail($id);
   	return view('islr.ut.edit',['utEdit'=>$ut]);
   }

   public function update(Request $request,$id)
   {
   		$ut = UnidadTributaria::findOrFail($id);
   		$ut->anio = $request->get('anio');
   		$ut->monto = $request->get('monto');
   		$ut->observacion = $request->get('observacion');

   		$ut->update();
         
         //actualizamos los datos de retencion porque cambio el valor de la unidad tributaria
         $retenciones = new RetencionController();
         $retenciones->update();

   		return self::index();

   }
}
