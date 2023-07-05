<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Islr extends Model
{
    use HasFactory;
    public static function ultimoPorcentajeDelProveedor($proveedorRif,$empresaRif){
    	$resultado= DB::select('SELECT d.porcentaje_retencion FROM islrs i,islr_detalles d WHERE i.proveedor_rif=:proveedorRif AND i.empresa_rif=:empresaRif AND i.id=d.islr_id ORDER BY i.id DESC LIMIT 1',[$proveedorRif,$empresaRif]);
    	if(!empty($resultado)){
    		return $resultado[0]->porcentaje_retencion;
    	}else{
    		return 0;
    	}
    	
    }
}
