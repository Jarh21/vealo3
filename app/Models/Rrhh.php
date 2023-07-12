<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Rrhh extends Model
{
    use HasFactory;
    protected $guarded = [];

    public static function listarEmpleados($rif){
        if($rif!=''){
            return DB::select('SELECT r.id,r.nombres,r.fecha_ingreso,r.sueldo_base,e.color,r.rif,e.nombre AS empresa_nombre FROM rrhhs r,empresas e WHERE r.activo=1 AND r.empresa_rif = e.rif AND e.rif=:eRif order by e.rif',[$rif]);
        }else{
            return DB::select('SELECT r.id,r.nombres,r.fecha_ingreso,r.sueldo_base,e.color,r.rif,e.nombre AS empresa_nombre FROM rrhhs r,empresas e WHERE r.activo=1 AND r.empresa_rif = e.rif order by e.rif');
        }
    	
    }
}
