<?php

namespace App\Http\Controllers\Cuadres;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cuadres\CuadreTransferencia;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Herramientas\HerramientasController;

class CuadresTransferenciaController extends Controller
{
    //
    public function index(){
        return DB::select('
        SELECT
            cuadre_transferencias.id,
            cuadre_transferencias.empresa_rif,
            cuadre_transferencias.fecha,
            cuadre_transferencias.descripcion,
            cuadre_transferencias.monto,
            cuadre_transferencias.numero_transferencia,
            cuadre_transferencias.banco_emisor_id,
            bancos1.nombre banco_emisor,
            cuadre_transferencias.banco_receptor_id,
            bancos2.nombre banco_receptor,
            cuadre_transferencias.fecha_transferencia   
        FROM
            cuadre_transferencias,
            bancos bancos1,
            bancos bancos2
        WHERE cuadre_transferencias.banco_emisor_id = bancos1.id
        AND cuadre_transferencias.banco_receptor_id = bancos2.id
        AND cuadre_transferencias.empresa_rif =:empresaRif
        AND cuadre_transferencias.fecha =:fechaCuadre 
        ',[session('empresaRif'),session('fechaCuadre')]);
        
    }

    public function transferenciasDelSiace(){
        $herramientas = new HerramientasController();
        $conexionSQL = $herramientas->conexionDinamicaBD(session('basedata'));

        return  $conexionSQL->select('
        SELECT mov_pagos.keycodigo,mov_pagos.cliente,mov_pagos.codpago,mov_pagos.tipo,SUM(mov_pagos.monto)AS monto, mov_pagos.codentidad, mov_pagos.entidad,mov_pagos.numero,mov_pagos.clave AS aprobacion,facturas.fiscalcomp FROM mov_pagos,facturas WHERE facturas.documento = mov_pagos.recibo AND mov_pagos.fecha =:fecha AND mov_pagos.codpago=11 AND mov_pagos.codentidad  NOT IN(41,42) GROUP BY recibo
        ',[session('fechaCuadre')]);
    }

    public function guardarTransferenciaCuadre(Request $request){
        
        $transferencia = new CuadreTransferencia();
        $transferencia->empresa_rif = session('empresaRif');
        $transferencia->fecha = session('fechaCuadre');
        $transferencia->banco_emisor_id = $request->banco_emisor;
        $transferencia->banco_receptor_id = $request->banco_receptor;
        $transferencia->descripcion = $request->descripcion;
        $transferencia->numero_transferencia=$request->numero_transferencia;
        $transferencia->fecha_transferencia = $request->fecha_transferencia;
        $transferencia->creado_por =  Auth::user()->name;
        $transferencia->monto=$request->monto;
        $transferencia->save();
       
    }

    public function eliminarTransferenciaCuadre($id){
        $transferencia = CuadreTransferencia::find($id);
        $transferencia->delete();
    }
}
