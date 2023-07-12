<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class FacturasPorPagar extends Model
{
    use HasFactory;
    public function listarFacturas($pagadasOPorPagar=0){    
            //facturas por por pagar
            $listar_cuentas_por_pagar = DB::table('facturas_por_pagars')                      
            ->select('facturas_por_pagars.id',
              'facturas_por_pagars.empresa_rif',                            
              'facturas_por_pagars.pago_efectuado',             
              'facturas_por_pagars.fecha_factura',
              'facturas_por_pagars.n_control',
              'facturas_por_pagars.cierre',
              'facturas_por_pagars.proveedor_rif',
              'facturas_por_pagars.proveedor_nombre',              
              'facturas_por_pagars.documento',
              'facturas_por_pagars.debitos',
              'facturas_por_pagars.creditos',
              DB::raw('0 as resto'),                
              'facturas_por_pagars.poriva',
              'facturas_por_pagars.montoiva',
              'facturas_por_pagars.gravado',
              'facturas_por_pagars.excento',
              'facturas_por_pagars.concepto',
              'facturas_por_pagars.dias_credito',
              'facturas_por_pagars.porcentaje_descuento',
              'facturas_por_pagars.porcentaje_retencion_iva',
              'facturas_por_pagars.cod_modo_pago',
              'facturas_por_pagars.modo_pago',
              'facturas_por_pagars.moneda_secundaria',
              'facturas_por_pagars.observacion',
              'facturas_por_pagars.codigo_relacion_pago',
              'facturas_por_pagars.usuario',
              'facturas_por_pagars.is_apartada_pago',
              'facturas_por_pagars.desapartada_pago',
              'facturas_por_pagars.is_retencion_iva',
              'facturas_por_pagars.retencion_iva',
              'facturas_por_pagars.is_retencion_islr',
              'facturas_por_pagars.retencion_islr',
              'facturas_por_pagars.fecha_real_pago',
              'facturas_por_pagars.retencion_iva',
              'facturas_por_pagars.igtf',
              'facturas_por_pagars.monto',
              'facturas_por_pagars.origen',
              'facturas_por_pagars.is_factura_revisada',
              'facturas_por_pagars.monto_divisa')
            ->where('facturas_por_pagars.pago_efectuado',0)
            ->where('facturas_por_pagars.modo_pago',session('modoPago'))                        
            ->where('facturas_por_pagars.empresa_rif',session('empresaRif'))
            ->where('facturas_por_pagars.concepto','FAC') 
            ->orderBy('id','desc')
            ->orderBy('proveedor_rif','desc')
            ->get();
       
        return $listar_cuentas_por_pagar;    
    }
    public function listarFacturasConNotaCredito($pagoEfectuado=0,$isApartadaPago=0){    
            //facturas por por pagar
            $listar_cuentas_por_pagar = DB::table('facturas_por_pagars')                       
            ->select('facturas_por_pagars.id',
              'facturas_por_pagars.empresa_rif',                            
              'facturas_por_pagars.pago_efectuado',              
              'facturas_por_pagars.fecha_factura',
              'facturas_por_pagars.n_control',
              'facturas_por_pagars.cierre',
              'facturas_por_pagars.proveedor_rif',
              'facturas_por_pagars.proveedor_nombre',              
              'facturas_por_pagars.documento',
              'facturas_por_pagars.debitos',
              'facturas_por_pagars.creditos',
               DB::raw('SUM(facturas_por_pagars.debitos-facturas_por_pagars.creditos) as resto'),
                'facturas_por_pagars.fecha_real_pago',            
              'facturas_por_pagars.poriva',
              'facturas_por_pagars.montoiva',
              'facturas_por_pagars.gravado',
              'facturas_por_pagars.excento',
              'facturas_por_pagars.concepto',
              'facturas_por_pagars.dias_credito',
              'facturas_por_pagars.fecha_real_pago',
              'facturas_por_pagars.porcentaje_descuento',
              'facturas_por_pagars.porcentaje_retencion_iva',
              'facturas_por_pagars.cod_modo_pago',
              'facturas_por_pagars.modo_pago',
              'facturas_por_pagars.moneda_secundaria',
              'facturas_por_pagars.observacion',
              'facturas_por_pagars.codigo_relacion_pago',
              'facturas_por_pagars.usuario',
              'facturas_por_pagars.is_apartada_pago',
              'facturas_por_pagars.is_retencion_iva',
              'facturas_por_pagars.retencion_iva',
              'facturas_por_pagars.is_retencion_islr',
              'facturas_por_pagars.retencion_islr',
              'facturas_por_pagars.retencion_iva',
              'facturas_por_pagars.igtf',
              'facturas_por_pagars.monto',
              'facturas_por_pagars.monto_divisa')
            ->where('facturas_por_pagars.pago_efectuado',$pagoEfectuado)
            ->where('facturas_por_pagars.modo_pago',session('modoPago'))                        
            ->where('facturas_por_pagars.empresa_rif',session('empresaRif'))            
            ->groupBy('facturas_por_pagars.empresa_rif','facturas_por_pagars.documento','facturas_por_pagars.proveedor_rif','facturas_por_pagars.modo_pago','facturas_por_pagars.pago_efectuado')
            ->orderBy('id','desc')
            ->orderBy('proveedor_rif','desc')
            ->get();
       //->where('facturas_por_pagars.is_apartada_pago',$isApartadaPago)
        return $listar_cuentas_por_pagar;    
    }

    public function listarFacturasPagoCalculado($fechaPago,$proveedorRif){
        $listadoFacturas = DB::table('facturas_por_pagars')            
            ->where('facturas_por_pagars.modo_pago',session('modoPago'))                        
            ->where('facturas_por_pagars.empresa_rif',session('empresaRif'))
            ->where('facturas_por_pagars.fecha_real_pago',$fechaPago)
            ->where('facturas_por_pagars.is_apartada_pago',1)
            ->orderBy('facturas_por_pagars.proveedor_rif')
            ->get();
        return $listadoFacturas;     
    }

    public function fechasPagoFacturasApartadas($fechaini,$fechafin){
        $fechaFacturas = DB::table('facturas_por_pagars')
            ->select('fecha_real_pago','proveedor_rif',
            DB::raw('SUM(facturas_por_pagars.monto_divisa + facturas_por_pagars.igtf) as monto'))            
            ->where('facturas_por_pagars.modo_pago',session('modoPago'))                        
            ->where('facturas_por_pagars.empresa_rif',session('empresaRif'))            
            ->where('facturas_por_pagars.is_apartada_pago',1)
            ->where('fecha_real_pago','>=',$fechaini)
            ->where('fecha_real_pago','<=',$fechafin)
            ->groupBy('fecha_real_pago')
            
            ->get();
        
        return $fechaFacturas;     
    }

    public function contarFacturasDelProveedorPorRangoDeFechaCalculada($fechaPago,$proveedorRif){
      $resultado= DB::select("select count(*)as total from facturas_por_pagars where fecha_real_pago =:fechaPago and proveedor_rif=:proveedorRif and empresa_rif=:empresaRif and modo_pago=:modoPago and is_apartada_pago=1",[$fechaPago,$proveedorRif,session('empresaRif'),session('modoPago')]);      
      if(!empty($resultado)){
        return $resultado[0]->total;
      }else{
        return 0;
      }
      
    }

    public function filtrarPagosPorProveedor($fechaRealPago){
      $pagos = DB::select("
      SELECT
          pago_proveedor.proveedor_rif,
          pago_proveedor.proveedor_nombre,
          pago_proveedor.fecha_real_pago,
          SUM(pago_proveedor.pago_divisa) AS pago_divisas,
          SUM(pago_proveedor.pago_bolivares)As pago_bolivares
      FROM   
        (SELECT
        detalle_fac.proveedor_rif,
        detalle_fac.proveedor_nombre,
        detalle_fac.fecha_real_pago,
        SUM(detalle_fac.debitos-detalle_fac.creditos)AS pago_bolivares,
        ROUND(SUM(detalle_fac.debitos-detalle_fac.creditos)/detalle_fac.moneda_secundaria,2) AS pago_divisa
        FROM
          (SELECT 
            facturas_por_pagars.documento,
            facturas_por_pagars.proveedor_rif,
            facturas_por_pagars.proveedor_nombre,
            facturas_por_pagars.fecha_real_pago,	  
            cuentas_por_pagars.debitos,
            cuentas_por_pagars.creditos,  
            facturas_por_pagars.moneda_secundaria  		  
          FROM 
            facturas_por_pagars,
            cuentas_por_pagars
          WHERE 
            facturas_por_pagars.id = cuentas_por_pagars.factura_id
            AND facturas_por_pagars.modo_pago=:modoPago
            AND facturas_por_pagars.is_apartada_pago
            AND facturas_por_pagars.fecha_real_pago=:fechaPago
            AND cuentas_por_pagars.concepto <>'CAN'
            AND facturas_por_pagars.empresa_rif=:empresaRif) AS detalle_fac
        GROUP BY detalle_fac.documento) AS pago_proveedor
        GROUP BY proveedor_rif
      ",['modoPago'=>session('modoPago'),'fechaPago'=>$fechaRealPago,'empresaRif'=>session('empresaRif')]);
      return $pagos;
    }

    public function buscarFacturaConNotaCredito($proveedorRif,$nFactura,$fechaFactura){
        $listar_cuentas_por_pagar = DB::table('facturas_por_pagars')                        
            ->select('facturas_por_pagars.id',
              'facturas_por_pagars.empresa_rif',                            
              'facturas_por_pagars.pago_efectuado',              
              'facturas_por_pagars.fecha_factura',
              'facturas_por_pagars.n_control',
              'facturas_por_pagars.cierre',
              'facturas_por_pagars.proveedor_rif',
              'facturas_por_pagars.proveedor_nombre',              
              'facturas_por_pagars.documento',
              'facturas_por_pagars.debitos',
              'facturas_por_pagars.creditos',
               DB::raw('SUM(facturas_por_pagars.debitos-facturas_por_pagars.creditos) as resto'),            
              'facturas_por_pagars.poriva',
              'facturas_por_pagars.montoiva',
              'facturas_por_pagars.gravado',
              'facturas_por_pagars.excento',
              'facturas_por_pagars.concepto',
              'facturas_por_pagars.dias_credito',
              'facturas_por_pagars.porcentaje_descuento',
              'facturas_por_pagars.cod_modo_pago',
              'facturas_por_pagars.modo_pago',
              'facturas_por_pagars.moneda_secundaria',
              'facturas_por_pagars.observacion',
              'facturas_por_pagars.codigo_relacion_pago',
              'facturas_por_pagars.usuario',
              'facturas_por_pagars.is_apartada_pago',
              'facturas_por_pagars.is_retencion_iva',
              'facturas_por_pagars.retencion_iva',
              'facturas_por_pagars.is_retencion_islr',
              'facturas_por_pagars.retencion_islr')
            ->where('facturas_por_pagars.pago_efectuado',0)
            ->where('facturas_por_pagars.modo_pago',session('modoPago'))                        
            ->where('facturas_por_pagars.empresa_rif',session('empresaRif'))
            ->where('facturas_por_pagars.proveedor_rif',$proveedorRif)
            ->where('facturas_por_pagars.documento',$nFactura)
            ->where('facturas_por_pagars.fecha_factura',$fechaFactura)
            ->groupBy('facturas_por_pagars.empresa_rif','facturas_por_pagars.documento','facturas_por_pagars.proveedor_rif','facturas_por_pagars.modo_pago','facturas_por_pagars.pago_efectuado')     
            ->first();
       //->where('facturas_por_pagars.is_apartada_pago',$isApartadaPago)
        return $listar_cuentas_por_pagar;
    }

    public function listarFacturasPagadas(){
        
         $listar_cuentas_por_pagar = DB::table('facturas_por_pagars')
            ->join('empresas','facturas_por_pagars.empresa_rif','=','empresas.rif')
            ->leftJoin('cuentas_por_pagars','facturas_por_pagars.id','=','cuentas_por_pagars.factura_id')
            ->select('facturas_por_pagars.id',
              'facturas_por_pagars.empresa_rif',
              'empresas.nombre',
              'empresas.nom_corto',
              'cuentas_por_pagars.fecha_pago',
              'facturas_por_pagars.pago_efectuado',              
              'facturas_por_pagars.fecha_factura',
              'facturas_por_pagars.n_control',
              'facturas_por_pagars.cierre',
              'facturas_por_pagars.proveedor_rif',
              'facturas_por_pagars.proveedor_nombre',              
              'facturas_por_pagars.documento',
              'facturas_por_pagars.debitos',
              'facturas_por_pagars.creditos',                
              'facturas_por_pagars.poriva',
              'facturas_por_pagars.montoiva',
              'facturas_por_pagars.gravado',
              'facturas_por_pagars.excento',
              'facturas_por_pagars.dias_credito',
              'facturas_por_pagars.porcentaje_descuento',
              'facturas_por_pagars.cod_modo_pago',
              'facturas_por_pagars.modo_pago',
              'facturas_por_pagars.moneda_secundaria',
              'facturas_por_pagars.observacion',
              'facturas_por_pagars.codigo_relacion_pago',
              'facturas_por_pagars.usuario')
            ->where('facturas_por_pagars.pago_efectuado',1)   
            ->where('facturas_por_pagars.modo_pago',session('modoPago'))                        
            ->where('facturas_por_pagars.empresa_rif',session('empresaRif')) 
            ->orderBy('id','desc')
            ->orderBy('proveedor_rif','desc')
            ->groupBy('documento')
            ->paginate(20);
        return $listar_cuentas_por_pagar;    
    }

    public function filtarFacturasPagadas($empresaRif,$fechaDesde,$fechaHasta,$proveedor){
        
            $busqueda_facturas_pagadas =DB::table('facturas_por_pagars')            
            ->join('empresas','facturas_por_pagars.empresa_rif','=','empresas.rif')
            ->leftJoin('cuentas_por_pagars','facturas_por_pagars.cuentas_por_pagar_id','=','cuentas_por_pagars.id')
            ->select('facturas_por_pagars.id',
              'facturas_por_pagars.empresa_rif',
              'empresas.nombre',
              'empresas.nom_corto',
              'cuentas_por_pagars.fecha_pago',
              'facturas_por_pagars.pago_efectuado',              
              'facturas_por_pagars.fecha_factura',
              'facturas_por_pagars.n_control',
              'facturas_por_pagars.cierre',
              'facturas_por_pagars.proveedor_rif',
              'facturas_por_pagars.proveedor_nombre',              
              'facturas_por_pagars.documento',
              'facturas_por_pagars.debitos',
              'facturas_por_pagars.creditos',                
              'facturas_por_pagars.poriva',
              'facturas_por_pagars.montoiva',
              'facturas_por_pagars.gravado',
              'facturas_por_pagars.excento',
              'facturas_por_pagars.dias_credito',
              'facturas_por_pagars.porcentaje_descuento',
              'facturas_por_pagars.cod_modo_pago',
              'facturas_por_pagars.modo_pago',
              'facturas_por_pagars.moneda_secundaria',
              'facturas_por_pagars.observacion',
              'facturas_por_pagars.codigo_relacion_pago',
              'facturas_por_pagars.usuario')
            ->where('facturas_por_pagars.pago_efectuado',1)                       
            ->where('facturas_por_pagars.empresa_rif',session('empresaRif'))
            ->where('facturas_por_pagars.modo_pago',session('modoPago'))             
            ->where('cuentas_por_pagars.fecha_pago','>=',$fechaDesde)
            ->where('cuentas_por_pagars.fecha_pago','<=',$fechaHasta)
            ->where('facturas_por_pagars.proveedor_rif',$proveedor)
            ->where('facturas_por_pagars.pago_efectuado',1)
                       
            ->paginate(400);
           
        return $busqueda_facturas_pagadas;    
    }
}
