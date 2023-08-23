<?php

namespace App\Http\Controllers\Islr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Retencion;
use App\Models\UnidadTributaria;

class RetencionController extends Controller
{
    public function index(){
        
        $valorUt=self::unidadTributaria();
        //ordenar con elocuen 
        // sortByo sortByDesc
        // ejemplo $posts = Post::all()->sortBy('created_at');
        
    	return view('islr.retencion.index',['retenciones'=>Retencion::all()->sortBy('procent_retencion'),'ut'=>$valorUt]);
    }

    public function unidadTributaria(){

        return $ut= DB::select('select monto from unidad_tributarias');
    }

    public function create(){
        
        //pasamos a la vista el valor de la unidad tributaria
    	return view('islr.retencion.create',['valorUT'=>self::unidadTributaria()]);
    }

    public function save(Request $request){
        //buscamos el valor delaunidad tributaria 
        foreach(self::unidadTributaria() as $valorUt){
            $ut=$valorUt->monto;
        }
        if(isset($ut)){
        
        	$reten = new Retencion();

            //calculamos el sustraendo que es (%retencion*UnidadTributaria*factor)
            $porcentaje=($request->get('procent_retencion')/100);
            $sustraendo=$porcentaje*$ut*$request->get('factor');
            
            $reten ->procent_retencion = $request->get('procent_retencion');
            $reten ->valorUT = $ut;
            $reten ->factor = $request->get('factor');
            $reten ->sustraendo = $sustraendo;

            //monto a retener es el sustraendo dividido entre el porcentaje de retencion
            $reten ->monto_min_retencion = $sustraendo/$porcentaje;

            $reten->save();
            return redirect()->route('retencion.index');
            
        }else{
            return redirect()->route('ut.index');
        }
    }



    public function edit($id){
        
        return view('retencion.edit',['retencion'=>Retencion::findOrFail($id)]);
    }



    public function update(){
        //buscamos todos los registros a modificar
        $retenciones = Retencion::all();

        //buscamos el valor delaunidad tributaria 
        foreach(self::unidadTributaria() as $valorUt){
            $ut=$valorUt->monto;
        }

        //actualizamos todos los porcentajes de retencion segun el valor de la unidad tributaria
        foreach ($retenciones as $retencion) {
            //buscamos cada uno otra vez para actualizar
            $reten = Retencion::findOrFail($retencion->id);
        
            $porcentaje=($reten->procent_retencion/100);
            $sustraendo=$porcentaje*$ut*$reten->factor; 
            $reten->valorUT = $ut;
            //$reten->factor = $request->get('factor');
            $reten->sustraendo = $sustraendo;
            $reten->monto_min_retencion = $sustraendo/$porcentaje;

            $reten->update();
        }    
        return redirect()->route('retencion.index');
    }



    public function destroy($id){
        

        $reten = Retencion::findOrFail($id);
        if($reten->delete()){
            return redirect()->route('retencion.index');
        }else{
            return redirect()->route('retencion.index');
        }
        
    }
}
