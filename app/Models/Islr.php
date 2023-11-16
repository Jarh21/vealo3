<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Islr extends Model
{
    use HasFactory;
    public static function ultimoPorcentajeDelProveedor($proveedorRif,$empresaRif){
    	$resultado= DB::select('SELECT ultimo_porcentaje_retener_islr as porcentaje_retencion FROM proveedors WHERE rif=:proveedorRif ORDER BY id DESC LIMIT 1',['proveedorRif'=>$proveedorRif]);
    	if(!empty($resultado)){
    		return $resultado[0]->porcentaje_retencion;
    	}else{
    		return 0;
    	}
    	
    }
}
