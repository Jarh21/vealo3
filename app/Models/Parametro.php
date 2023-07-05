<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Parametro extends Model
{
    use HasFactory;
    public static function buscarVariable($var){
    	$variable='';
    	$resultado = DB::select('select valor from parametros where variable=:var',['var'=>$var]);
    	foreach ($resultado as $value) {
    		$variable = $value->valor;
    	}
        
    	return $variable;
    }

    public static function actualizarVariable($variable,$valor){
        
		$existe = DB::select('SELECT variable FROM parametros WHERE variable=:variables',['variables'=>$variable]);

		if(empty($existe)){
            
			DB::insert(" INSERT	INTO parametros (variable,valor) VALUES (?,?)",[$variable,$valor]);
		}else{
           
			DB::update('update parametros set valor=? where variable =?',[$valor,$variable]);
		}  
    	
    }
}
