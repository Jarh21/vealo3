<?php

namespace App\Http\Controllers\CuentasPorPagar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Herramientas\HerramientasController;
use App\Http\Controllers\Admin\ProveedorController;
use App\Models\Banco;
use App\Models\Proveedor;
use App\Models\CuentasPorPagar;
use App\Models\Parametro;
use App\Models\FacturasPorPagar;
use App\Models\Empresa;
use App\Models\Retencion;
use Illuminate\Support\Facades\DB;

class ReportesCuentasPorPagarController extends Controller
{
    public function reporteRelacionPagosPorEmpresa(){
		if(empty(session('empresaRif')) or empty(session('modoPago'))){
    		
			return redirect()->route('cuentasporpagar.inicio','reporteRelacionPagosPorEmpresa');
    	}
        return view('cuentasPorPagar.reportes.reporteRelacionPagoPorEmpresa');
    }

	public function reporteCuentasPagadas(){
		if(empty(session('empresaRif'))){
    		return redirect()->route('cuentasporpagar.inicio','reportecuntaspagas');
    	}
    	$herramientas  = new HerramientasController();
    	$bancos = Banco::all();
    	return view('cuentasPorPagar.reportes.reportecuentaspagas',['empresas'=>$herramientas->listarEmpresas(),'bancos'=>$bancos,]);
    }

    public function buscarReporteCuentasPagadas(Request $request){
		//por banco
    	//este metodo filtra los pagos realizados segun empresa o banco o ambos y retorna a la vista reportecuentaspagadas.blade.php
    	$bancos = Banco::all();
		$empresa = explode('|',$request->get('empresa_rif'));
    	$banco = $request->get('banco_id'); 
		//si se selecciona todos los bancos recorremos el arreglo de los bancos y comparamos aquellos que tienen tildado lista de bancos
		//luego llenamos un nuevo arreglo donde se asignan los bancos que si estan en la lista.
		foreach($banco as $valoBanco){
			if($valoBanco =='0'){
				$todosBanco=array();
					foreach($bancos as $banco){
						if($banco->is_bank_list==1){
							$todosBanco[]=$banco->id;
						}
					}
					
				$banco = $todosBanco;	   
			}	
		}
			
    	$fechaini = $request->fechaini;
    	$fechafin = $request->fechafin;
    	$herramientas  = new HerramientasController();    	    	
    	$buscarPagos = new CuentasPorPagar();
    	$resultadoBusqueda =$buscarPagos->buscarCuentasPagadasPorBanco($empresa[0],$banco,$fechaini,$fechafin);
		
    	return view('cuentasPorPagar.reportes.reportecuentaspagas',['empresas'=>$herramientas->listarEmpresas(),'bancos'=>$bancos,'listadoPagos'=>$resultadoBusqueda,'datosEmpresa'=>$empresa,'datosBanco'=>$banco,'fechaini'=>$fechaini,'fechafin'=>$fechafin]);
    }

    public function resultadoReporteRelacionPagosPorEmpresa(Request $request){
        if(empty(session('empresaRif'))){
			//si no selecciono la empresa y el modo de pago retorna a esa vista
    		return self::seleccionarEmpresa();
    	}
    	$fechaini='';
    	$fechafin='';
    	$fechaini = $request->fechaIni;
    	$fechafin = $request->fechaFin;
    	$arrayFechas=[];
    	$arrayFacturas=[];
		$logo='';
		//buscamos en cuentas por pagars el total a cancelar
    				
		$facturasCalculadas = new FacturasPorPagar();
		$fechasPagos = $facturasCalculadas->fechasPagoFacturasApartadas($fechaini,$fechafin);
		
		foreach ($fechasPagos as $fechaPago) {
			$pagoProveedores = $facturasCalculadas->filtrarPagosPorProveedor($fechaPago->fecha_real_pago);
			$arrayFacturas['fechaPagoAcordado']=$fechaPago->fecha_real_pago;
			$arrayFacturas['montoPagar']=$fechaPago->monto;
			$arrayFacturas['pagoProveedores'] = $pagoProveedores;
			$arrayFechas[]=(object)$arrayFacturas;
			
		}
	    if(!empty(session('logo'))){
			$logo = session('logo');
		}
        return view('cuentasPorPagar.reportes.reporteRelacionPagoPorEmpresa',['pagos'=>$arrayFechas,'logo'=>$logo]);

    }

	public function reportePagoPorProvedorTodasEmpresas(){
		if(empty(session('empresaRif')) or empty(session('modoPago'))){
    		
			return redirect()->route('cuentasporpagar.inicio','reportePagoPorProvedorTodasEmpresas');
    	}
		return view('cuentasPorPagar.reportes.pagoProveedorTodasEmpresas',['proveedores'=>Proveedor::all()]);
	}

	public function resultadoReportePagoPorProvedorTodasEmpresas(Request $request){
		$proveedor = explode('|',$request->proveedor);
		$proveedorRif = $proveedor[0];
		$proveedorNombre = $proveedor[1];
		$fechaIni = $request->fechaIni;
		$fechaFin = $request->fechaFin;
		$facturasDelProveedor = DB::select("
		SELECT
		facturas.empresa_rif,
		empresas.nombre as empresa_nombre,
		SUM(facturas.divisa) AS divisa,
		GROUP_CONCAT(facturas.documento) AS documento
		FROM
			(SELECT
			CONCAT(f.documento,' monto -',SUM(c.debitos - c.creditos)/f.moneda_secundaria,'-',f.pago_efectuado) AS documento,
			f.fecha_real_pago,
			f.empresa_rif,	  
			SUM(c.debitos - c.creditos)/f.moneda_secundaria AS divisa	  
			FROM
			facturas_por_pagars f,
			cuentas_por_pagars c
			WHERE f.id = c.factura_id
			AND f.proveedor_rif =:proveedorRif
			AND f.is_apartada_pago = 1
			AND f.fecha_real_pago >=:fechaIni
			AND fecha_real_pago <=:fechaFin
			AND (c.concepto <> 'CAN' AND c.cod_concepto <> 7 AND c.cod_concepto <> 8)			 
			AND f.modo_pago =:modoPago 
			GROUP BY f.documento) AS facturas,
			empresas
		WHERE
		facturas.empresa_rif = empresas.rif
		GROUP BY facturas.empresa_rif
		",['proveedorRif'=>$proveedorRif,'fechaIni'=>$fechaIni,'fechaFin'=>$fechaFin,'modoPago'=>session('modoPago')]);
		
		return view('cuentasPorPagar.reportes.pagoProveedorTodasEmpresas',
		[
			'proveedores'=>Proveedor::all(),
			'pagos'=>$facturasDelProveedor,
			'proveedorSeleccionado'=>$proveedorRif.' '.$proveedorNombre,
			'fechaIni'=>$fechaIni,
			'fechaFin'=>$fechaFin
		]);

	}
}
