<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class CuentasPorPagar extends Model
{
    use HasFactory;
    public function listarFacturas($pagadasOPorPagar=0){
       
        if($pagadasOPorPagar==0){
            //facturas por por pagar
            $listar_cuentas_por_pagar = DB::table('cuentas_por_pagars')
            ->join('empresas','cuentas_por_pagars.empresa_rif','=','empresas.rif')
            ->select('cuentas_por_pagars.id',
              'cuentas_por_pagars.empresa_rif',
              'empresas.nombre',
              'empresas.nom_corto',
              'cuentas_por_pagars.pago_efectuado',
              'cuentas_por_pagars.banco_id',
              'cuentas_por_pagars.fecha_pago',
              'cuentas_por_pagars.n_control',
              'cuentas_por_pagars.cierre',
              'cuentas_por_pagars.proveedor_rif',
              'cuentas_por_pagars.proveedor_nombre',
              'cuentas_por_pagars.concepto',
              'cuentas_por_pagars.documento',
              'cuentas_por_pagars.debitos',
              'cuentas_por_pagars.creditos',  
              DB::raw('SUM(cuentas_por_pagars.debitos-cuentas_por_pagars.creditos) as resto'),
              'cuentas_por_pagars.poriva',
              'cuentas_por_pagars.montoiva',
              'cuentas_por_pagars.gravado',
              'cuentas_por_pagars.excento',
              'cuentas_por_pagars.observacion',
              'cuentas_por_pagars.usuario',
              'cuentas_por_pagars.codigo_relacion_pago')
            ->where('cuentas_por_pagars.pago_efectuado',$pagadasOPorPagar)
            ->where('cuentas_por_pagars.concepto', '=', 'FAC')           
            ->groupBy('cuentas_por_pagars.codigo_relacion_pago','cuentas_por_pagars.proveedor_rif','cuentas_por_pagars.n_control','cierre')
            ->orderBy('cierre','desc')
            
            ->get();
        }else{
            //facturas ya pagas
            $listar_cuentas_por_pagar = DB::table('cuentas_por_pagars')
            ->join('empresas','cuentas_por_pagars.empresa_rif','=','empresas.rif')
            ->select('cuentas_por_pagars.id',
              'cuentas_por_pagars.empresa_rif',
              'empresas.nombre',
              'empresas.nom_corto',
              'cuentas_por_pagars.pago_efectuado',
              'cuentas_por_pagars.banco_id',
              'cuentas_por_pagars.fecha_pago',
              'cuentas_por_pagars.n_control',
              'cuentas_por_pagars.cierre',
              'cuentas_por_pagars.proveedor_rif',
              'cuentas_por_pagars.proveedor_nombre',
              'cuentas_por_pagars.concepto',
              'cuentas_por_pagars.documento',
              'cuentas_por_pagars.debitos',
              'cuentas_por_pagars.creditos',  
              DB::raw('SUM(cuentas_por_pagars.debitos-cuentas_por_pagars.creditos) as resto'),
              'cuentas_por_pagars.poriva',
              'cuentas_por_pagars.montoiva',
              'cuentas_por_pagars.gravado',
              'cuentas_por_pagars.excento',
              'cuentas_por_pagars.observacion',
              'cuentas_por_pagars.usuario',
              'cuentas_por_pagars.codigo_relacion_pago')
            ->where('cuentas_por_pagars.pago_efectuado',$pagadasOPorPagar)
            ->where('cuentas_por_pagars.concepto', '=', 'FAC')           
            ->groupBy('cuentas_por_pagars.codigo_relacion_pago','cuentas_por_pagars.proveedor_rif','cuentas_por_pagars.n_control','cierre')
            ->orderBy('cierre','desc')
            
            ->paginate(10);
        }
        return $listar_cuentas_por_pagar;    
    }
    public static function debitosMenosCredito($facturaId){
        $resta = DB::table('cuentas_por_pagars')
        ->select('factura_id',DB::raw('SUM(cuentas_por_pagars.debitos-cuentas_por_pagars.creditos) as resto'))
        ->where('factura_id',$facturaId)        
        ->first();        
        return $resta;

    }

    public static function debitosMenosCreditoSoloDeuda($facturaId){
        $resta = DB::table('cuentas_por_pagars')
        ->select('factura_id',DB::raw('SUM(cuentas_por_pagars.debitos-cuentas_por_pagars.creditos) as resto'))
        ->where('factura_id',$facturaId)
        ->where('concepto','<>','CAN')
        ->where('cod_concepto','<>',7)        
        ->first();        
        return $resta;
    }

    public static function sumaDebitosPorAumentoTasa($facturaId){
        $resultado=0;
        $suma = DB::select("SELECT sum(debitos) as debitos from cuentas_por_pagars  WHERE factura_id=:facturaId AND cod_concepto=7",['facturaId'=>$facturaId]);
        foreach($suma as $valor){
            $resultado = $valor->debitos;
        }
        if($resultado== null){
            $resultado=0;
        }
        return $resultado;
    }

    public function filtarFacturasDB($empresaRif,$fechaDesde,$fechaHasta,$proveedor){
        
            $busqueda_cuentas_por_pagar =DB::table('cuentas_por_pagars')
            ->join('empresas','cuentas_por_pagars.empresa_rif','=','empresas.rif')
            ->select('cuentas_por_pagars.id',
              'cuentas_por_pagars.empresa_rif',
              'empresas.nombre',
              'empresas.nom_corto',
              'cuentas_por_pagars.pago_efectuado',
              'cuentas_por_pagars.banco_id',
              'cuentas_por_pagars.fecha_pago',
              'cuentas_por_pagars.n_control',
              'cuentas_por_pagars.cierre',
              'cuentas_por_pagars.proveedor_rif',
              'cuentas_por_pagars.proveedor_nombre',
              'cuentas_por_pagars.concepto',
              'cuentas_por_pagars.documento',
              'cuentas_por_pagars.debitos',
              'cuentas_por_pagars.creditos',  
              DB::raw('SUM(cuentas_por_pagars.debitos-cuentas_por_pagars.creditos) as resto'),
              'cuentas_por_pagars.poriva',
              'cuentas_por_pagars.montoiva',
              'cuentas_por_pagars.gravado',
              'cuentas_por_pagars.excento',
              'cuentas_por_pagars.observacion',
              'cuentas_por_pagars.usuario',
              'cuentas_por_pagars.codigo_relacion_pago')
            ->where('cuentas_por_pagars.empresa_rif',$empresaRif)
            ->where('cuentas_por_pagars.fecha_pago','>=',$fechaDesde)
            ->where('cuentas_por_pagars.fecha_pago','<=',$fechaHasta)
            ->where('proveedor_nombre','like','%'.$proveedor.'%')
            ->where('cuentas_por_pagars.pago_efectuado',1)
            ->where('cuentas_por_pagars.concepto', '=', 'FAC')
            ->groupBy('cuentas_por_pagars.id')
            ->paginate(400);
           
        return $busqueda_cuentas_por_pagar;    
    }

    
    public function buscarCuentasPagadasPorBanco($empresa,$bancos,$fechaini,$fechafin){
        $bancoTodosRegistrosCxp=array();
        foreach($bancos as $banco){
            $registrosCxp =array();
            $todosRegistrosCxp=array();
            //
            $resultados=DB::select("SELECT cuentas_por_pagars.id,tipo_moneda,empresa_rif,bancos.nombre as banco_nombre,fecha_pago,proveedor_rif,proveedor_nombre,referencia_pago,concepto_descripcion,codigo_relacion_pago,creditos,factura_id FROM cuentas_por_pagars, bancos WHERE concepto='CAN' AND bancos.id =:bancoId1 AND banco_id=:bancoId2 AND empresa_rif=:empresaRif AND fecha_pago >=:fechaIni AND fecha_pago <=:fechaFin",[$banco,$banco,$empresa,$fechaini,$fechafin]);
            /* $resultados=DB::select("SELECT cuentas_por_pagars.id,tipo_moneda,empresa_rif,bancos.nombre as banco_nombre,fecha_pago,proveedor_rif,proveedor_nombre,referencia_pago,concepto_descripcion,codigo_relacion_pago,creditos FROM cuentas_por_pagars, bancos WHERE cod_tipo_moneda=1 AND concepto='CAN' AND bancos.id =:bancoId1 AND banco_id=:bancoId2 AND empresa_rif=:empresaRif AND fecha_pago >=:fechaIni AND fecha_pago <=:fechaFin",[$banco,$banco,$empresa,$fechaini,$fechafin]); */
            if(empty($resultados)){ continue; }
            foreach ($resultados as $resultado) {
                //buscamos las facturqas relacionadas al pago con el codiogo de relacion
                $facturas = DB::select("select documento from facturas_por_pagars where id=:facturaId",[$resultado->factura_id]);
                $registrosCxp['empresa_rif']=$resultado->empresa_rif;
                $registrosCxp['banco_nombre']=$resultado->banco_nombre;
                $registrosCxp['fecha_pago']=$resultado->fecha_pago;
                $registrosCxp['proveedor_rif']=$resultado->proveedor_rif;
                $registrosCxp['proveedor_nombre']=$resultado->proveedor_nombre;
                $registrosCxp['referencia_pago']=$resultado->referencia_pago;
                $registrosCxp['pago']=$resultado->creditos;
                $registrosCxp['facturas']=$facturas;
                $registrosCxp['concepto_descripcion']=$resultado->concepto_descripcion;
                $registrosCxp['id']=$resultado->id;
                $todosRegistrosCxp[]=$registrosCxp;
            }
            $bancoTodosRegistrosCxp[] = $todosRegistrosCxp;
        }
       
              
        return $bancoTodosRegistrosCxp;
    }

    public function listarAsientosDeFacturas($codRelacion){
        $listadoCuentas = DB::table('cuentas_por_pagars')
            ->join('empresas','cuentas_por_pagars.empresa_rif','=','empresas.rif')
            ->leftJoin('facturas_por_pagars','cuentas_por_pagars.id','=','facturas_por_pagars.cuentas_por_pagar_id') 
            ->select('cuentas_por_pagars.id',
                  'cuentas_por_pagars.empresa_rif',
                  'empresas.nombre',
                  'empresas.nom_corto',
                  'facturas_por_pagars.moneda_secundaria',
                  'facturas_por_pagars.porcentaje_descuento',
                  'facturas_por_pagars.cuentas_por_pagar_id',
                  'facturas_por_pagars.modo_pago',
                  'cuentas_por_pagars.pago_efectuado',
                  'cuentas_por_pagars.banco_id',
                  'cuentas_por_pagars.fecha_pago',
                  'cuentas_por_pagars.n_control',
                  'cuentas_por_pagars.cierre',
                  'cuentas_por_pagars.proveedor_rif',
                  'cuentas_por_pagars.proveedor_nombre',
                  'cuentas_por_pagars.concepto_descripcion',
                  'cuentas_por_pagars.concepto',
                  'cuentas_por_pagars.documento',
                  'cuentas_por_pagars.debitos',
                  'cuentas_por_pagars.creditos',              
                  'cuentas_por_pagars.poriva',
                  'cuentas_por_pagars.montoiva',
                  'cuentas_por_pagars.gravado',
                  'cuentas_por_pagars.excento',
                  'cuentas_por_pagars.observacion',
                  'cuentas_por_pagars.usuario',
                  'cuentas_por_pagars.porcentaje_retencion_iva',
                  'cuentas_por_pagars.codigo_relacion_pago')
            ->where('cuentas_por_pagars.codigo_relacion_pago',$codRelacion)       
            ->get();
        return $listadoCuentas;    
    }
}
