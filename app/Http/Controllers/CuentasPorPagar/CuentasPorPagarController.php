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

class CuentasPorPagarController extends Controller
{
    public function seleccionarEmpresa($rutaSolicitante=''){
		$herramientas  = new HerramientasController();		
        return view('cuentasPorPagar.seleccionEmpresa',['empresas'=>$herramientas->listarEmpresas(),'rutaSolicitante'=>$rutaSolicitante]);
	}

    public function guardarSeleccionEmpresa(Request $request){
        $empresa =explode('|', $request->empresa);
        $modoPago = explode('|',$request->modo_pago);
		$rutaSolicitante = $request->ruta_solicitante;
		if(empty($empresa[2])){
			dd('En la tabla empresa debe agregar el nombre de la base de datos de la empresa seleccionada en el campo basedata');
		}
		$herramientas  = new HerramientasController();
		$monedaBase = $herramientas->consultarMonedaBase();
        session(['empresaNombre'=>$empresa[1],'empresaRif'=>$empresa[0],'codTipoMoneda'=>$modoPago[0],'modoPago'=>$modoPago[1],'basedata'=>$empresa[2],'logo_empresa'=>$empresa[3],'monedaBase'=>$monedaBase]);
       
		if(empty($rutaSolicitante)){
			return self::facturasPorPagar();
		}else{
			return redirect()->route($rutaSolicitante);
		}
		
    }

    public function facturasPorPagar($mensaje=''){
    	if(empty(session('empresaRif')) or empty(session('modoPago'))){

    		return self::seleccionarEmpresa('cuentasporpagar.facturasPorPagar');
    	}
    	//listamos todas las facturas por pagar o pagadas y las enviamos a la vista index
    	$herramientas  = new HerramientasController();
		//buscamos parametros de configuracion
        $pago_facturas_desde_facturas_por_pagar = Parametro::buscarVariable('pago_facturas_desde_facturas_por_pagar');  
		$importar_server2_a_server1_cxp = Parametro::buscarVariable('importar_server2_a_server1_cxp');
		$fecha_actualizacion_servidor_remoto = Parametro::buscarVariable('fecha_actualizacion_servidor_remoto');
				
		if($pago_facturas_desde_facturas_por_pagar==''){
			dd('error en parametro pago_facturas_desde_facturas_por_pagar contacte al programador tlf 0414-4649934 Email jarh18@gmail.com');
		}

    	$listadoFacturasPorPagar = self::prepararFacturasPorPagar();    	
    		
    	return view('cuentasPorPagar.facturasPorPagar',[
    		'empresas'=>$herramientas->listarEmpresas(),
    		'cuentas'=>$listadoFacturasPorPagar,
    		'empresaRif'=>session('empresaRif'),
    		'modoPagoSelect' => session('modoPago'),    		
    		'proveedores'=>Proveedor::all(),
    		'mensaje'=>$mensaje,
			'pago_facturas_desde_facturas_por_pagar'=>$pago_facturas_desde_facturas_por_pagar,
			'importar_server2_a_server1_cxp'=>$importar_server2_a_server1_cxp,
			'fecha_actualizacion_servidor_remoto'=>$fecha_actualizacion_servidor_remoto,
    	]);
    }

    public function prepararFacturasPorPagar($tipoMoneda='bolivares'){
    	///metodo que trae las facturas cargadas en el sistema para luego seleccionarlas y procesarlas
    	//en la vista facturasPorPagar
    	$listadoFacturasPorPagar=array();
    	$herramientas  = new HerramientasController();
    	$facturas_por_pagar = new FacturasPorPagar();

		//comprobar si esta activa la opcion de verificar facturas en el siace
		//si lo esta la buscamos en la tabla cxp del siace en caso de no encontrarla enviar una bandera par resaltar el error
		$verificarFacturaSiace = Parametro::buscarVariable('verificar_facturas_en_siace');
		if($verificarFacturaSiace==''){
			dd('No esta definido en la configuracion si se puede o no verificar facturas en el siace');
		}

			$listar_cuentas_por_pagar = $facturas_por_pagar->listarFacturas(0);
     	
    	
    		foreach($listar_cuentas_por_pagar as $facturaPorPagar){
				$banderaFacturaSiaceEncontrada=0;
    			$retencionIslr=0;
    			$banderaIslar=0;
				$montoOrigenFactura = 0;    			
    			$fechaPago = $herramientas->sumarDiasAFecha($facturaPorPagar->fecha_factura,$facturaPorPagar->dias_credito);
    			$diasParaPago = $herramientas->diferenciaEntreFechas($fechaPago,date('Y-m-d'));
    			$porcentajeRetencionIva=0.00;
    			//verificamos si el porcentaje de retencion del iva del proveedor, si no esta
    			//en la tabla factura se busca en los datos del proveedor ya que es necesario
    			//para el calculo del pago en las facturas
    			if($facturaPorPagar->porcentaje_retencion_iva==0.00){
    				$proveedor = Proveedor::select('porcentaje_retener')->where('rif',$facturaPorPagar->proveedor_rif)->get();
				foreach($proveedor as $datos){
					$porcentajeRetencionIva=floatval($datos->porcentaje_retener);
				}
    				
    			}else{
    				$porcentajeRetencionIva=floatval($facturaPorPagar->porcentaje_retencion_iva);
    			}
				
				//verificar si hay que verificar la factura y buscar en el siace
				//esto es por si elimina del siace una factura por relacionar o ya relacionada
				//ya que si las elimnan no se les debe pagar all proveedor bien sea por devolucion
				if($verificarFacturaSiace==1 and $facturaPorPagar->origen=='siace'){
					$conexionSQL = $herramientas->conexionDinamicaBD(session('basedata')); 
					$proveedorRifSinCaracteres= str_replace('-','',$facturaPorPagar->proveedor_rif);  				
					$registros = $conexionSQL->select("SELECT keycodigo,debitos from cxp where codorigen=2000 and documento=:nfactura and REPLACE(rif, '-', '') =:rifProveedor order by keycodigo",['nfactura'=>$facturaPorPagar->documento,'rifProveedor'=>$proveedorRifSinCaracteres]);
					foreach($registros as $registro){
						if($registro->keycodigo > 0){
							$banderaFacturaSiaceEncontrada=1;
							$montoOrigenFactura = $registro->debitos;
						}
					}
				}else{
					//si no esta habilitada la configuracion de veriifcar las facturas en el siace, la bandela la dejamos en 1 
					//para que asuma que la encontron y no tilde de rojo la factura ya que la verificacion esta desactivada
					$banderaFacturaSiaceEncontrada=1;
				}

    				$factura= array(
    				  'id'=>$facturaPorPagar->id,
		              'empresa_rif'=>$facturaPorPagar->empresa_rif,
		            /*  'nombre'=>$facturaPorPagar->nombre,
		              'nom_corto'=>$facturaPorPagar->nom_corto,*/
		              'pago_efectuado'=>$facturaPorPagar->pago_efectuado,		              
		              'fecha_factura'=>$facturaPorPagar->fecha_factura,
		              'n_control'=>$facturaPorPagar->n_control,
		              'cierre'=>$facturaPorPagar->cierre,
		              'proveedor_rif'=>$facturaPorPagar->proveedor_rif,
		              'proveedor_nombre'=>$facturaPorPagar->proveedor_nombre,
		              'is_apartada_pago'=>$facturaPorPagar->is_apartada_pago,
		              'documento'=>$facturaPorPagar->documento,
		              'debitos'=>$facturaPorPagar->debitos,
		              'creditos'=>$facturaPorPagar->creditos,  
		              'resto'=>$facturaPorPagar->resto,
		              'concepto'=>$facturaPorPagar->concepto,
		              'codigo_relacion_pago'=>$facturaPorPagar->codigo_relacion_pago,
		              'poriva'=>$facturaPorPagar->poriva,
		              'montoiva'=>$facturaPorPagar->montoiva,
		              'gravado'=>$facturaPorPagar->gravado,
		              'excento'=>$facturaPorPagar->excento,
		              'dias_credito'=>$facturaPorPagar->dias_credito,
		              'fecha_pago'=>$fechaPago,
		              'fecha_real_pago'=>$facturaPorPagar->fecha_real_pago,
		              'dias_para_pago'=>$diasParaPago,
		              'porcentaje_descuento'=>$facturaPorPagar->porcentaje_descuento,
		              'modo_pago'=>$facturaPorPagar->modo_pago,
		              'moneda_secundaria'=>$facturaPorPagar->moneda_secundaria,
		              'cod_modo_pago'=>$facturaPorPagar->cod_modo_pago,
		              'is_apartada_pago'=>$facturaPorPagar->is_apartada_pago,
					  'desapartada_pago'=>$facturaPorPagar->desapartada_pago,
		              'is_retencion_islr'=>$facturaPorPagar->is_retencion_islr,
		              'retencion_islr'=>$facturaPorPagar->retencion_islr,
		              'retencion_iva'=>$facturaPorPagar->retencion_iva,
					  'is_factura_revisada'=>$facturaPorPagar->is_factura_revisada,
		              'observacion'=>$facturaPorPagar->observacion,
					  'is_igtf' =>$facturaPorPagar->is_igtf,
		              'igtf'=>$facturaPorPagar->igtf,
					  'banderaFacturaSiaceEncontrada'=>$banderaFacturaSiaceEncontrada,
		              'usuario'=>$facturaPorPagar->usuario,
		              'porcentaje_retencion_iva'=>$porcentajeRetencionIva,
		              'montoOrigenFactura'=>$montoOrigenFactura,
    				);
    				$listadoFacturasPorPagar[]=(object)$factura;   			
    			
    		}
    	return 	$listadoFacturasPorPagar;
    }

    public function buscarRetencionISLR($proveedorRif,$nFactura,$fechaFactura){
    	$retencionIslr=DB::table('islrs')
    	->join('islr_detalles','islrs.id','=','islr_detalles.islr_id')
    	->select('islr_detalles.total_retener')
    	->where('islrs.empresa_rif',session('empresaRif'))
    	->where('islrs.proveedor_rif',$proveedorRif)
    	->where('islr_detalles.nFactura',$nFactura)
    	->where('islr_detalles.fecha_factura',$fechaFactura)
    	->first();
    	if(isset($retencionIslr->total_retener)){
    		return $retencionIslr->total_retener;
    	}else{
    		return 0;
    	}    	
    }

	public function calcularRetencionISLR($proveedorRif,$montoExcento){
		//realizamos el calculo de la retencion de impuestos localmente solo se requiere que
		//el proveedor tenga registrado el ultimo porcentaje de retencion
		if($montoExcento > 0){
			$datos_proveedor = Proveedor::where('rif',$proveedorRif)->first();
			$PorcRetencion = $datos_proveedor->ultimo_porcentaje_retener_islr;

			//si el proveedor tiene porcentaje de retencion islr
			if($datos_proveedor->agregar_islr == true or $PorcRetencion > 0){
				if($datos_proveedor->tipo_contribuyente=="Juridico"){					
					$sustraendo=0;
				}else{
					
					$porcenReten = Retencion::where('procent_retencion',$PorcRetencion)->first();
					$sustraendo=$porcenReten->sustraendo;
				}					
				
				$retencionIslr = ((($montoExcento*$PorcRetencion)/100)-$sustraendo);
				

			}else{
				$retencionIslr=0;
				
			}
		}else{
			$retencionIslr=0;
		}
		
		return	$retencionIslr;	
	}

    public function buscarRetencionIva($rifProveedor,$montoIva){
    	//metodo que calcula el valor a retener en el iva
    	//buscamos datos del proveedor	
    		 

    		//si la factura tiene monto en IVA se calcula la retencion del iva
    		if($montoIva > 0){
				$datos_proveedor = Proveedor::where('rif',$rifProveedor)->first();
    			//verificamos el % de retencion de iva del proveedor si no lo tiene tomara el 100%
    			$banderaRetencionIva = 1;
    				 		
		 		if(!empty($datos_proveedor->porcentaje_retener)){ 			
		 			$porcentaje_retener =$datos_proveedor->porcentaje_retener; 			 			
		 		}else{ 			
		 			$porcentaje_retener =100;	 			
		 		}
		 				 		
    			//calculamos la retencion del iva
    			$ivaRetener = ($montoIva * $porcentaje_retener)/100;
    	
    		}else{
    			$ivaRetener = 0;
    		}//fin retencion IVA
			
    	return $ivaRetener;	
    }

    public function facturasPagadas(){
    	/*
    	* recopila los datos para la vista de facturas pagadas
    	*/
    	$herramientas  = new HerramientasController();
    	
		//$facturas_por_pagar = DB::select("select cuentas_por_pagars.concepto_descripcion, cuentas_por_pagars.id as idcxp, facturas_por_pagars.id, cuentas_por_pagars.codigo_relacion_pago,facturas_por_pagars.modo_pago, facturas_por_pagars.empresa_rif, facturas_por_pagars.proveedor_nombre, facturas_por_pagars.proveedor_rif, facturas_por_pagars.documento, cuentas_por_pagars.creditos, cuentas_por_pagars.fecha_pago as fecha_real_pago FROM cuentas_por_pagars, facturas_por_pagars WHERE cuentas_por_pagars.concepto = 'CAN' AND cuentas_por_pagars.empresa_rif =:empresa AND facturas_por_pagars.id = cuentas_por_pagars.factura_id order by id desc limit 100",['empresa'=>session('empresaRif')]);
		
    	return view('cuentasPorPagar.facturasPagadas',[
    		'empresas'=>$herramientas->listarEmpresas(),
    		
    		'empresaRif'=>session('empresaRif'),
    		'proveedores'=>Proveedor::all(),
    	]);
    }

    public function buscarCuentasPagadas(Request $request){
    	// metodo que filtra las facturas guardadas en el sistema de cuentas por pagar 
    	$herramientas = new HerramientasController();
		$proveedor = $request->proveedor;
    	$nFactura = $request->n_factura;
    	$fechaDesde 	  = $request->get('fecha_desde');
    	$fechaHasta	      = $request->get('fecha_hasta');    	
    	$empresa       = $request->get('empresa_rif');    	   	
    	$empresa = explode("|", $empresa);
    	$empresaRif = $empresa[0];
    	$empresaNombre = $empresa[1];

		$condicion = array();
		$condicion[]="cuentas_por_pagars.concepto = 'CAN'";
		$condicion[]="cuentas_por_pagars.empresa_rif ='".$empresaRif."'";
		$condicion[]="facturas_por_pagars.id = cuentas_por_pagars.factura_id";
		if(!empty($nFactura)){ 
			$condicion[]= "facturas_por_pagars.documento in(".$nFactura.")";
			$condicion[]= "cuentas_por_pagars.documento in(".$nFactura.")";
		}
		if(!empty($fechaDesde)){ $condicion[]="cuentas_por_pagars.fecha_pago >='".$fechaDesde."'"; }
		if(!empty($fechaHasta)){ $condicion[]="cuentas_por_pagars.fecha_pago <='".$fechaHasta."'"; }
		if(!empty($proveedor)){ $condicion[]="facturas_por_pagars.proveedor_nombre like '%".$proveedor."%'";}
		$whereClause = implode(" AND ", $condicion); //se convierte el array en un string añadiendole el AND

		
		$cuentas_por_pagar = DB::select("select cuentas_por_pagars.concepto_descripcion,cuentas_por_pagars.id, facturas_por_pagars.id, cuentas_por_pagars.codigo_relacion_pago,facturas_por_pagars.modo_pago, facturas_por_pagars.empresa_rif, facturas_por_pagars.proveedor_nombre, facturas_por_pagars.proveedor_rif, facturas_por_pagars.documento, cuentas_por_pagars.creditos, cuentas_por_pagars.fecha_pago as fecha_real_pago FROM cuentas_por_pagars, facturas_por_pagars WHERE ". $whereClause." order by facturas_por_pagars.id desc ");
		$empresaDatos = Empresa::select('nombre','rif')->where('rif',$empresaRif)->first();//datos de la empresa seleccionada para la vista
    	
    	return view('cuentasPorPagar.facturasPagadas',[
    		'cuentas'=>$cuentas_por_pagar,
    		'empresas'=>$herramientas->listarEmpresas(),
    		'datosEmpresa'=>$empresa,
    		'fechaDesde' =>$fechaDesde,
    		'fechaHasta' =>$fechaHasta,    		
			'empresaDatos' =>$empresaDatos,
			'empresaRif'=>$empresaRif,
			'nFactura' =>$nFactura    		
    	]);
    	   	
    }

    public function optenerFacturasPorPagar(Request $request){
		
    	//Metodo que Importamos las facturas del libro de compra al sistema cuentas por pagar
		if(empty(session('empresaRif')) or empty(session('modoPago'))){

    		return self::seleccionarEmpresa('cuentasporpagar.facturasPorPagar');
    	}
		
    	$porcentajeRetencionIva='';
    	$mensaje = array();
    	$fechaActual = date('Y-m-d');
    	$nFactura = $request->get('nfactura');
    	$fechaIni  = $request->get('fecha_cierre_ini');
    	$fechaFin  = $request->get('fecha_cierre_fin');
    	$empresa   = $request->get('empresa');
		$observacion = $request->get('observacion');
    	$diasCredito = $request->dias_credito;
    	$porcDescuento= $request->porcentaje_descuento;
    	$modoPago = session('modoPago');
    	$empresa = explode('|', $empresa);
    	$rifEmpresa = session('empresaRif');
		$proveedorRifSinCaracteres ='';
		$cxpProveedorRif ='';
    	$codigoUnico = uniqid();//genera un codigo unico en php
    	

    	$herramientas = new HerramientasController();
    	$conexionSQL = $herramientas->conexionDinamicaBD($empresa[1]);    	
    	$proveedorRif = trim($request->get('proveedorRif'));
		$proveedorRifSinCaracteres = str_replace('-','',$proveedorRif);
		$anioAnterior =date('Y')-1;
    	//buscamos todas las facturas cerradas de la tabla CXP de la empresa seleccionada
    	
    	if($fechaIni=='' or $fechaFin=='' and empty($proveedorRif)){
    		$registros = $conexionSQL->select("SELECT * from cxp where codorigen=2000 and documento=:nfactura and year(fecha)>=:anioAnterior order by keycodigo",[$nFactura,$anioAnterior]);
    	}

    	if(!empty($fechaIni) or !empty($fechaFin)){	
    		$registros = $conexionSQL->select("SELECT * FROM cxp WHERE codorigen=2000 and cierre>=:fechaIni and cierre<=:fechaFin and year(fecha)>=:anioAnterior",[$fechaIni,$fechaFin,$anioAnterior]);
    	}

    	if(empty($fechaIni) and empty($fechaFin) and !empty($proveedorRif) and !empty($nFactura)){
    		$registros = $conexionSQL->select("SELECT * from cxp where codorigen=2000 and documento=:nfactura and REPLACE(rif, '-', '') =:rifProveedor and year(fecha)>=:anioAnterior order by keycodigo",
    			[$nFactura,$proveedorRifSinCaracteres,$anioAnterior]);
    	}
    	
    	if(!empty($registros)){
    		$anioAnterior =date('Y')-1;
    		//si no hay resultado de busqueda no importar registros

	    	foreach ($registros as $registro) {
	    		$retencionIva=0;
	    		$retencionIslr=0;
	    		$credito=0;
				$cxpProveedorRif =str_replace('-','',trim($registro->rif));//de los registro traidos del libro de compras, buscamos el rif del proveedor de la base de datos vealo
				$proveedor = Proveedor::whereRaw("REPLACE(rif, '-', '') = '$cxpProveedorRif'")->select('rif')->first();
				
				if(isset($proveedor->rif)){
					$proveedorRif = $proveedor->rif;
					
				}else{
					$mensaje['texto']="el proveedor ".$registro->rif." ".$registro->nomprov." del libro de compras que esta importando no coincide con los proveedores registrados en el vealo, posiblemente no esten registrado o el rif es distinto";
	    			$mensaje['tipo']='alert-warning';
					return self::facturasPorPagar($mensaje);
				}
				
	    		if(isset($registro->DOCUMENTO)){
	    			$documento = $registro->DOCUMENTO;
	    		}
	    		if(isset($registro->documento)){
	    			$documento = $registro->documento;
	    		}
	    		if(isset($registro->CONCEPTO)){
		    			$concepto = $registro->CONCEPTO;
		    		}
	    		if(isset($registro->concepto)){
	    			$concepto=$registro->concepto;
	    		}
	    		
	    		//comparamos si el ya existe para no volve a cargarlo 
				
	    		if(DB::table('facturas_por_pagars')
		    		->where('documento',$documento)
		    		->where('proveedor_rif',$proveedorRif)
		    		->where('concepto',$concepto)		    		
		    		->whereYear('fecha_factura','>=',$anioAnterior)
		    		->whereYear('fecha_factura','<=',date('Y'))
		    		->doesntExist())
	    		{
	    			//si no exite duplicado guardamos el registro
	    			//buscamos el porcentaje de retencion del iva
					$caracteres=array('-',' ','.','*',',','/');
					$rifProveedorFactura=str_replace($caracteres,'',trim($proveedorRif));	    			
					$proveedor = DB::select("SELECT rif,porcentaje_retener,descontar_nota_credito,agregar_igtf,dias_credito,agregar_islr FROM (SELECT REPLACE(rif,'-','')AS rif,porcentaje_retener,descontar_nota_credito,agregar_igtf,dias_credito,agregar_islr FROM proveedors) AS prov WHERE rif=:facRif",['facRif'=>$rifProveedorFactura]);
	    			$descontarNotaCredito=0;
					$agregarIgtf =0;
					$diasCreditoProveedor = 0;
					$agregarIslr =0;
					foreach($proveedor as $datos){
						$porcentajeRetencionIva=$datos->porcentaje_retener;
						$descontarNotaCredito = $datos->descontar_nota_credito;
						$agregarIgtf = $datos->agregar_igtf;
						$agregarIslr = $datos->agregar_islr;
						$diasCreditoProveedor = $datos->dias_credito;
					}//fin buscar porcentaje de retencion	
					
					//verificar si el proveedor esta registrado en vealo
					/* if(empty($porcentajeRetencionIva)){
						dd($proveedorRif,'proveedor no se encuentra registrado, registrelo para continuar');
					} */

		    		$facturaPorPagar = new FacturasPorPagar;
		    		$facturaPorPagar->empresa_rif = $empresa[0];
		    		$facturaPorPagar->n_control = $registro->ncontrol;
		    		$facturaPorPagar->cierre = $registro->cierre;
		    		$facturaPorPagar->proveedor_rif = $proveedorRif;
		    		$facturaPorPagar->proveedor_nombre = $registro->nomprov;
		    		$facturaPorPagar->porcentaje_retencion_iva = $porcentajeRetencionIva;	
		    		$facturaPorPagar->documento = $documento;
		    		$facturaPorPagar->concepto = $concepto;		    		
		    		$facturaPorPagar->debitos = $registro->debitos;
		    		$facturaPorPagar->creditos = $registro->creditos;
		    		$facturaPorPagar->poriva = $registro->poriva;
		    		$facturaPorPagar->montoiva = $registro->montoiva;
		    		$facturaPorPagar->gravado = $registro->gravado;
		    		$facturaPorPagar->excento = $registro->exento;
					$facturaPorPagar->observacion = $observacion;
					$facturaPorPagar->origen = 'siace';
					$facturaPorPagar->is_igtf = $agregarIgtf;
		    		//$facturaPorPagar->codigo_relacion_pago = $codigoUnico;
		    		if(isset($registro->fecha)){
		    			$fechaFactura = $registro->fecha;
		    		}
		    		if(isset($registro->FECHA)){
		    			$fechaFactura = $registro->FECHA;
		    		}	
					$facturaPorPagar->fecha_factura = $fechaFactura;
					//si no se indica el valor de la tasa al momento de cargar la factura, se toma el valor de la tasa de historial dolar o la que este definida como historico
					if(empty($request->valor_tasa)){
						$monedaSecundaria = HerramientasController::valorDolarPorFecha($fechaFactura);
					}else{
						$monedaSecundaria = $request->valor_tasa;
					}
							    		
		    		if(!empty($diasCredito)){
		    			//si hay modificacion en los dis de credito
		    			$facturaPorPagar->dias_credito = $diasCredito;
		    		}else{
						//si no hay modificaciones en los dias de creditos le colocamos los dias de credito del proveedor
						$facturaPorPagar->dias_credito = $diasCreditoProveedor;
					}
		    		if(!empty($porcDescuento)){
		    			//si hay modificacion del porcentaje de descuento 
		    			$facturaPorPagar->porcentaje_descuento = $porcDescuento;
		    		}
		    		if(!empty($modoPago)){
		    			$facturaPorPagar->modo_pago = $modoPago;
		    		}

		    		$facturaPorPagar->moneda_secundaria = $monedaSecundaria;
					$facturaPorPagar->usuario=auth()->user()->name;		    		
	    			//////GUARDAMOS LA FACTURA///////////////////////////
		    		$facturaPorPagar->save();
		    		

		    		///guardamos registro de la factura en cuentas por pagar
		    		$arrayRegistro = array('empresaRif'=>$empresa[0],'ncontrol'=>$registro->ncontrol,'cierre'=>$registro->cierre,'proveedorRif'=>$proveedorRif,'proveedorNombre'=>$registro->nomprov,'cod_concepto'=>1,'concepto'=>$concepto,'documento'=>$documento,'debitos'=>$registro->debitos,'creditos'=>$registro->creditos,'poriva'=>$registro->poriva,'montoiva'=>$registro->montoiva,'gravado'=>$registro->gravado,'exento'=>$registro->exento,'factura_id'=>$facturaPorPagar->id);
	    			$idCuentasPagar = self::guardarEnCuentasPorPagar($arrayRegistro);
	    			/////////fin guardado cuentas por poagar

	    			///////si el registro de cxp es factura se insertan las retenciones, sino es nota de credito
	    			if($concepto =='FAC'){	    				
	    				
    					////buscamos si la factura tiene retencion de ISLR
					
		    			$retencionIslr = self::calcularRetencionISLR($proveedorRif,$registro->exento); 
		    			//si retencion es mayor a 0.00 activamos bandera	    			
		    			if($retencionIslr > 0.00){		    				
		    				$arrayRegistro['debitos'] = 0;
		    				$arrayRegistro['montoiva'] = 0;
		    				$arrayRegistro['creditos'] = $retencionIslr;
		    				$arrayRegistro['cod_concepto'] = 2;
		    				$arrayRegistro['concepto'] = 'RISLR';
		    				$arrayRegistro['concepto_descripcion']='RETENCION DE ISLR';
		    				$arrayRegistro['factura_id']=$facturaPorPagar->id;
		    				
		    				self::guardarEnCuentasPorPagar($arrayRegistro);
		    				FacturasPorPagar::where('id',$facturaPorPagar->id)->update(['is_retencion_islr'=>1,'retencion_islr'=>$retencionIslr]);
		    			}	    			
		    			////////fin bucar retencion de ISLR

						///buscar retencion de  IVA
			    		//buscar si la empresa es agente retencion de impuestos	
			    		$isAgente = Empresa::where('rif',session('empresaRif'))->first();
	    				if($isAgente->is_agente_retencion==1){
			    			
			    			if($registro->montoiva > 0.00){
			    				$retencionIva = self::buscarRetencionIva($proveedorRif,$registro->montoiva);
				    			if($retencionIva > 0.00){		    						    				
				    				$arrayRegistro['debitos'] = 0;
				    				$arrayRegistro['montoiva'] = 0;
				    				$arrayRegistro['creditos'] = $retencionIva;
				    				$arrayRegistro['cod_concepto'] = 3;
				    				$arrayRegistro['concepto'] = 'RIVA';
				    				$arrayRegistro['concepto_descripcion']='RETENCION IVA';
				    				$arrayRegistro['factura_id']=$facturaPorPagar->id;
				    				
				    				self::guardarEnCuentasPorPagar($arrayRegistro);
				    				FacturasPorPagar::where('id',$facturaPorPagar->id)->update(['is_retencion_iva'=>1,'retencion_iva'=>$retencionIva]);
				    			}			    			
			    			} ////fin buscar retencion de iva
	    				}//fin si es agente retencion de impuestos

		    			//////////si la factura tiene DESCUENTO se agrega en cxp ////////////////////
		    			$monto = 0;
		    			$descuento =0;
		    			if(!empty($porcDescuento)){

		    				$monto = $facturaPorPagar->debitos;

		    				if($porcDescuento > 0.0){
		    					$descuento = (($monto - $retencionIva - $retencionIslr)*$porcDescuento)/100;
		    				}
		    				
							$arrayRegistro['debitos'] = 0;
		    				$arrayRegistro['montoiva'] = 0;
		    				$arrayRegistro['creditos'] = $descuento;
		    				$arrayRegistro['cod_concepto'] = 4;
		    				$arrayRegistro['concepto'] = 'DESC';
		    				$arrayRegistro['concepto_descripcion']='DESCUENTO DEL '.$porcDescuento.'%';
		    				$arrayRegistro['factura_id']=$facturaPorPagar->id;
		    				
		    				self::guardarEnCuentasPorPagar($arrayRegistro);
		    			}///FIN SI LA FACTURA TIENE DESCUENTO

						/////COMPARAMOS SI EL PROVEEDOR TIENE HABILITADO DESCONTAR NOTA DE CREDITO
						if($descontarNotaCredito == 1){
							/////BUSCAR SI LA FACTURA TIENE NOTA DE CREDITO
							$notacreditos = $conexionSQL->select("SELECT SUM(creditos+montoiva)AS credito FROM notacredito WHERE codfact=:keycodigoFac GROUP BY codfact",['keycodigoFac'=>$registro->keycodigo]);
							foreach($notacreditos as $notacredito){
								$credito = floatval($notacredito->credito);
							}
							//si la nota credito es mayor a 0 creamos el asiento
							if($credito > 0.00){
								$arrayRegistro['debitos'] = 0;
								$arrayRegistro['montoiva'] = 0;
								$arrayRegistro['creditos'] = $credito;
								$arrayRegistro['cod_concepto'] = 5;
								$arrayRegistro['concepto'] = 'NCP';
								$arrayRegistro['concepto_descripcion']='NOTA DE CREDITO';
								$arrayRegistro['factura_id']=$facturaPorPagar->id;
								
								self::guardarEnCuentasPorPagar($arrayRegistro);
							}//FIN NOTA CREDITO		
						}////FIN DE COMPARAR SI EL PROVEEDOR TIENE HABILITADO DESCONTAR NOTA DE CREDITO	
	    			}//fin si es factura
	    		
	    			$mensaje['texto']="Factura ".$nFactura." importada con exito";
	    			$mensaje['tipo']='alert-success';
				}else{
					$mensaje['texto']="Error no se pudo traer la factura ".$nFactura." porque ya fue registrada";
					$mensaje['tipo']="alert-danger";
				}//fin si la factura no la han registrado
	    		$fechaActual=$registro->cierre;
	    		
	    	}//fin del foreach
	    } //fi el $mensaje es vacio es porque no encontro nada y modificamos el mensaje

	    if(empty($mensaje)){	    	
	    	$mensaje['texto']="No se encontro la factura ".$nFactura." en el libro de compras, por favor verifique los datos";
	    	$mensaje['tipo']="alert-warning";
	    }	    
    	return self::facturasPorPagar($mensaje);
    }

    public function nuevaFacturaPorPagar2(){
    	//metodo que llama a la interfaz para el registro de una factura manual    	
    	$poriva = Parametro::buscarVariable('poriva');
    	$herramientas = new HerramientasController();
    	return view('cuentasPorPagar.nuevafacturaporpagar',[    		
    		'poriva'=>$poriva,
    		'empresas'=>$herramientas->listarEmpresas(),
    		'proveedores'=>Proveedor::all(),
    	]);
    }

    public function saveNuevaFacturaPorPagar(Request $request){
    	//Metodo que guarda las facturas registradas manualmente
		$facturaCancelada = $request->factura_cancelada;
		$montoIva=0;
    	$anioAnterior =date('Y')-1;
    	$porcDescuento = $request->porcentaje_descuento;		 	
    	$empresa     = $request->get('empresa_rif');
    	$empresa = explode('|', $empresa);
    	$proveedor = explode('|',$request->get('proveedor'));
    	$proveedorRif =$proveedor[0];
    	$proveedorNombre = $proveedor[1];
    	$facturasConIslr = $request->islr;
		$retencionIslr =0;
		$retencionIva = 0;
		$montoIva=$request->montoiva;
    	$debitos = $request->get('debitos');
    	$monedaSecundaria = HerramientasController::valorDolarPorFecha($request->fecha_factura);
    	$herramientas = new HerramientasController();
    	$conexionSQL = $herramientas->conexionDinamicaBD(session('basedata'));
		$codigoUnico = uniqid();//genera un codigo unico en php

		//comparamos si la factura ya ha sido registrada
    	if(DB::table('facturas_por_pagars')
    		->where('documento',$request->get('documento'))
    		->where('proveedor_rif',$proveedorRif)    		
    		->whereYear('fecha_factura','>=',$anioAnterior)
    		->whereYear('fecha_factura','<=',date('Y'))
    		->doesntExist()){    	

	    	$facturaPorPagar = new FacturasPorPagar;
	    	$facturaPorPagar->empresa_rif = session('empresaRif');
	    	$facturaPorPagar->fecha_factura = $request->fecha_factura;
			$facturaPorPagar->n_control = $request->get('n_control');
			$facturaPorPagar->cierre = $request->fecha_factura;
			$facturaPorPagar->proveedor_rif = $proveedorRif;
			$facturaPorPagar->proveedor_nombre = $proveedorNombre;
			$facturaPorPagar->moneda_secundaria = $monedaSecundaria;
			$facturaPorPagar->documento = $request->documento;
			$facturaPorPagar->debitos = $debitos;
			$facturaPorPagar->creditos = 0.00;
			$facturaPorPagar->poriva = $request->poriva;
			$facturaPorPagar->montoiva = $request->montoiva;
			$facturaPorPagar->gravado = $request->gravado_f;
			$facturaPorPagar->excento = $request->excento;		
			$facturaPorPagar->concepto = 'FAC';
			$facturaPorPagar->dias_credito = $request->dias_credito;
			$facturaPorPagar->porcentaje_descuento =$request->porcentaje_descuento;
			$facturaPorPagar->observacion = $request->observacion;
			$facturaPorPagar->modo_pago = session('modoPago');
			$facturaPorPagar->origen = 'local';
			$facturaPorPagar->usuario = auth()->user()->name;
			$facturaPorPagar->save();

            ///guardamos registro de la factura en cuentas por pagar
            $arrayRegistro = array('empresaRif'=>session('empresaRif'),'ncontrol'=>$request->n_control,'cierre'=>$request->fecha_factura,'proveedorRif'=>$proveedorRif,'proveedorNombre'=>$proveedorNombre,'cod_concepto'=>1,'concepto'=>'FAC','documento'=>$request->documento,'debitos'=>$debitos,'poriva'=>$request->poriva,'montoiva'=>$request->montoiva,'gravado'=>$request->gravado_f,'exento'=>$request->excento,'factura_id'=>$facturaPorPagar->id);
			
            $idCuentasPagar = self::guardarEnCuentasPorPagar($arrayRegistro);

            ///////si el registro de cxp es factura se insertan las retenciones, sino es nota de credito
			

			////buscamos si la factura tiene retencion de ISLR(proveedorRif,nFactura,fechaFactura)
			//verificar si no viene vacio seleccion de ISLR
    		if(!empty($facturasConIslr)){
    		
	    		///si la factura tiene retencion de ISLR
	    		//buscamos datos del proveedor
    			$datos_proveedor = Proveedor::where('rif',$proveedorRif)->first();
				$PorcRetencion = $datos_proveedor->ultimo_porcentaje_retener_islr;

				//si el proveedor tiene porcentaje de retencion islr
				if($PorcRetencion > 0){
					if($datos_proveedor->tipo_contribuyente=="Juridico"){					
						$sustraendo=0;
					}else{
						
						$porcenReten = Retencion::where('procent_retencion',$PorcRetencion)->first();
						$sustraendo=$porcenReten->sustraendo;
					}					
					$montoExcento = $request->excento;
					$retencionIslr = ((($montoExcento*$PorcRetencion)/100)-$sustraendo);
				}else{
					$retencionIslr=0;
					$banderaRetencionIslr=2;
				}
	    	}else{
	    		//si el checkbox esta vacio es porque a la  factura ya le generaron retencoin de islr
	    		//si no viene checked buscamos en el sistema de islr
	    		$retencionIslr = self::buscarRetencionISLR($proveedorRif,$request->documento,$request->fecha_factura);    		
	    	}//fin si no viene vacion el checkbox seleccion de islr
			//si retencion es mayor a 0.00 activamos bandera	    			
			if($retencionIslr > 0.00){		    				
				$arrayRegistro['debitos'] = 0;
				$arrayRegistro['montoiva'] = 0;
				$arrayRegistro['creditos'] = $retencionIslr;
				$arrayRegistro['concepto'] = 'RISLR';
				$arrayRegistro['cod_concepto'] = 2;
				$arrayRegistro['concepto_descripcion'] = 'RETENCION DE ISLR';
				$arrayRegistro['factura_id']=$facturaPorPagar->id;
				if(!empty($facturaCancelada)){
					$arrayRegistro['codigo_relacion_pago'] = $codigoUnico;
				}	
				self::guardarEnCuentasPorPagar($arrayRegistro);
				FacturasPorPagar::where('id',$facturaPorPagar->id)->update(['is_retencion_islr'=>1,'retencion_islr'=>$retencionIslr]);
			}	    			
			////////fin bucar retencion de ISLR

			//buscar si la empresa es agente retencion de IVA			
			if(floatval($request->montoiva) > 0.00){
				
				$empresaRif = session('empresaRif');	
				$isAgente = Empresa::where('rif',$empresaRif)->first();					
				if($isAgente->is_agente_retencion==1){					
					$retencionIva = self::buscarRetencionIva($proveedorRif,$request->montoiva);
	    			if($retencionIva > 0.00){
	    				$arrayRegistro['debitos'] = 0;
	    				$arrayRegistro['montoiva'] = 0;
	    				$arrayRegistro['creditos'] = $retencionIva;
	    				$arrayRegistro['concepto'] = 'RIVA';
	    				$arrayRegistro['cod_concepto'] = 3;
	    				$arrayRegistro['concepto_descripcion'] ='RETENCION DE IVA';
	    				$arrayRegistro['factura_id']=$facturaPorPagar->id;
	    				if(!empty($facturaCancelada)){
							$arrayRegistro['codigo_relacion_pago'] = $codigoUnico;
						}
	    				self::guardarEnCuentasPorPagar($arrayRegistro);
	    				FacturasPorPagar::where('id',$facturaPorPagar->id)->update(['is_retencion_iva'=>1,'retencion_iva'=>$retencionIva]);
	    			}			    			
				} ////fin buscar retencion de iva   
			}//fin si es agente retencion de impuestos

			//////////si la factura tiene DESCUENTO se agrega en cxp ////////////////////
			$monto = 0;
			$descuento =0;
			if(!empty($porcDescuento)){

				$monto = $facturaPorPagar->debitos;

				if($porcDescuento > 0.0){
					$descuento = (($monto - $retencionIva - $retencionIslr)*$porcDescuento)/100;
				}
				
				$arrayRegistro['debitos'] = 0;
				$arrayRegistro['montoiva'] = 0;
				$arrayRegistro['creditos'] = $descuento;
				$arrayRegistro['concepto'] = 'DESC';
				$arrayRegistro['cod_concepto']= 4;
				$arrayRegistro['concepto_descripcion']='DESCUENTO DEL '.$porcDescuento.'%';
				$arrayRegistro['factura_id']=$facturaPorPagar->id;
				if(!empty($facturaCancelada)){
					$arrayRegistro['codigo_relacion_pago'] = $codigoUnico;
				}
				self::guardarEnCuentasPorPagar($arrayRegistro);
			}


			
			if(!empty($facturaCancelada)){
				//////////si la factura ya esta cancelada se agrega el registro de cancelacion en cuentas_por_pagars
				$idDecontado='';
				$deudaTotalFactura = CuentasPorPagar::debitosMenosCreditoSoloDeuda($facturaPorPagar->id);
				
				$deudaTotalFacturaDivisa = HerramientasController::valorAlCambioMonedaSecundaria($deudaTotalFactura->resto,$monedaSecundaria);
				
				//buscamos el id de bancos correspondiente a de contado
				$idDecontado = Parametro::buscarVariable('id_banco_decontado');
				
				///guardamos registro de la factura en cuentas por pagar 
				$arrayRegistro = array('empresaRif'=>session('empresaRif'),'banco_id'=>$idDecontado,'codigo_relacion_pago'=>$codigoUnico,'ncontrol'=>$request->n_control,'cierre'=>$request->fecha_factura,'fecha_pago'=>$request->fecha_pago,'proveedorRif'=>$proveedorRif,'proveedorNombre'=>$proveedorNombre,'cod_concepto'=>0,'concepto'=>'CAN','concepto_descripcion'=>'PAGO DE FACTURA','documento'=>$request->documento,'creditos'=>$deudaTotalFactura->resto,'factura_id'=>$facturaPorPagar->id);
			
				$idCuentasPagar = self::guardarEnCuentasPorPagar($arrayRegistro);				
				FacturasPorPagar::where('id',$facturaPorPagar->id)->update(['pago_efectuado'=>1,'fecha_real_pago'=>$request->fecha_pago,'codigo_relacion_pago'=>$codigoUnico,'monto_divisa'=>$deudaTotalFacturaDivisa]);

				return redirect('/cuentasporpagar/verPagarFacturas/'.$codigoUnico); 

			}                   

			return self::facturasPorPagar();
			
		}else{
			
			\Session::flash('message', 'Esta Factura no se puede guardar porque ya fue registrada anteriormente');
			$poriva = Parametro::buscarVariable('poriva');
	    	$herramientas = new HerramientasController();
	    	return view('cuentasPorPagar.nuevafacturaporpagar',[    		
	    		'poriva'=>$poriva,
	    		'empresas'=>$herramientas->listarEmpresas(),
	    		'proveedores'=>Proveedor::all(),
	    	]);
		}	
    }

    public function guardarEnCuentasPorPagar($registro){
    	/*
    	*funcion inserta los datos de las facturas al ser añadidas
    	*en la tabla fasturas por pagar y asi poder trabajarlas mas facil al registrar los pagos o deducciones 
    	*/
    	$cuentasPagar = new CuentasPorPagar();
    	if(isset($registro['empresaRif'])){
    		$cuentasPagar->empresa_rif = $registro['empresaRif'];
    	}
    	if(isset($registro['ncontrol'])){
    		$cuentasPagar->n_control = $registro['ncontrol'];
    	}
		if(isset($registro['cierre'])){
			$cuentasPagar->cierre = $registro['cierre'];
		}
		if(isset($registro['proveedorRif'])){
		$cuentasPagar->proveedor_rif = $registro['proveedorRif'];
			if(isset($registro['proveedorNombre'])){
				$cuentasPagar->proveedor_nombre = $registro['proveedorNombre'];	
			}else{
				$nombreProveedor = DB::select("select nombre from proveedors where rif=:proveedorRif",[$registro['proveedorRif']]);
				foreach($nombreProveedor as $nombre){
					$cuentasPagar->proveedor_nombre = $nombre->nombre;
				}
			}
		}
		
		if(isset($registro['concepto'])){	
		$cuentasPagar->concepto = $registro['concepto'];
		}
		if(isset($registro['concepto_descripcion'])){	
		$cuentasPagar->concepto_descripcion = $registro['concepto_descripcion'];
		}
		if(isset($registro['documento'])){
		$cuentasPagar->documento = $registro['documento'];
		}
		if(isset($registro['monto_divisa'])){
		$cuentasPagar->monto_divisa = $registro['monto_divisa'];
		}
		if(isset($registro['monto_bolivares'])){
		$cuentasPagar->monto_bolivares = $registro['monto_bolivares'];
		}
		if(isset($registro['debitos'])){
		$cuentasPagar->debitos = $registro['debitos'];
		}
		if(isset($registro['creditos'])){			
		$cuentasPagar->creditos = $registro['creditos'];
		}
		if(isset($registro['poriva'])){
		$cuentasPagar->poriva = $registro['poriva'];
		}
		if(isset($registro['montoiva'])){
		$cuentasPagar->montoiva = $registro['montoiva'];
		}
		if(isset($registro['gravado'])){
		$cuentasPagar->gravado = $registro['gravado'];
		}
		if(isset($registro['exento'])){
		$cuentasPagar->excento = $registro['exento'];
		}
		if(isset($registro['monto_divisa'])){
		$cuentasPagar->monto_divisa = $registro['monto_divisa'];
		}
		if(isset($registro['codigo_relacion_pago'])){
			$cuentasPagar->codigo_relacion_pago = $registro['codigo_relacion_pago'];
		}
		if(isset($registro['factura_id'])){
			$cuentasPagar->factura_id = $registro['factura_id'];
		}
		if(isset($registro['tasa'])){
			$cuentasPagar->tasa = $registro['tasa'];
		}
		if(isset($registro['observacion'])){
			$cuentasPagar->observacion = $registro['observacion'];
		}
		if(isset($registro['fecha_pago'])){
			$cuentasPagar->fecha_pago = $registro['fecha_pago'];
		}
		if(isset($registro['banco_id'])){
			$cuentasPagar->banco_id = $registro['banco_id'];
		}
		if(isset($registro['referencia_pago'])){
			$cuentasPagar->referencia_pago = $registro['referencia_pago'];
		}
		if(isset($registro['cod_concepto'])){
			$cuentasPagar->cod_concepto = $registro['cod_concepto'];
		}
		if(isset($registro['concepto_descripcion'])){
			$cuentasPagar->concepto_descripcion = $registro['concepto_descripcion'];
		}
		if(isset($registro['pago_efectuado'])){
			$cuentasPagar->pago_efectuado = $registro['pago_efectuado'];
		}
		
		$cuentasPagar->tipo_moneda=session('modoPago');
		$cuentasPagar->cod_tipo_moneda = intval(session('codTipoMoneda'));
		$cuentasPagar->usuario = auth()->user()->name;//nombre del usuario
		$cuentasPagar->save();
		return $cuentasPagar->id;
    }

    public function vistaPagarFacturas(Request $request){
    	$modoPago = session('modoPago');
    	$bancos = Banco::all();
    	$facturas = $request->facturasPorPagar;    	
    	$codigoUnico = uniqid();//genera un codigo unico en php
    	$todosRegistros = self::prepararPagarFacturas($codigoUnico,$facturas);
		//buscamos parametros de configuracion, si se puede seleccionar el banco cuando esta en modo pago divisas
		$isActivarBanco = Parametro::buscarVariable('select_banco_desde_modo_pago_divisa');     	
    	return view('cuentasPorPagar.pagarFacturas.indexPagar',['bancos'=>$bancos,'modoPagoSelect'=>$modoPago,'cuentas'=>$todosRegistros,'id_facturas'=>$facturas,'codigo_relacion_pago'=>$codigoUnico,'isActivarBanco'=>$isActivarBanco]);

    }

    public function retornarVistaPagarFacturas($facturas='',$codigoUnico=''){
    	$modoPago = session('modoPago');
    	$bancos = Banco::all();    	
    	//$codigoUnico = uniqid();//genera un codigo unico en php
    	$todosRegistros = self::prepararPagarFacturas($codigoUnico,$facturas); 
		//buscamos parametros de configuracion, si se puede seleccionar el banco cuando esta en modo pago divisas
		$isActivarBanco = Parametro::buscarVariable('select_banco_desde_modo_pago_divisa');     	
    	return view('cuentasPorPagar.pagarFacturas.indexPagar',['bancos'=>$bancos,'modoPagoSelect'=>$modoPago,'cuentas'=>$todosRegistros,'id_facturas'=>$facturas,'codigo_relacion_pago'=>$codigoUnico,'isActivarBanco'=>$isActivarBanco]);

    }

    public function verVistaPagarFacturas($codigoUnico=0,$facturaId=0){
    	//abre la vista de pagar facturas ya calculadas con el codigo de relacion
    	$modoPago = session('modoPago');
    	$bancos = Banco::all();    


			$facturas = array();
			$todosRegistros = self::prepararPagarFacturas($codigoUnico,$facturas);

    		//optenemos los id de las facturas para retornar
			foreach($todosRegistros as $registro){
				$facturas[]=$registro['factura_id'];
			}
		if(empty($facturas)){
			
			$mensaje['texto']="ocurrio un erro, vuelva a editar el registro de pagos anterior";
			$mensaje['tipo']="alert-info";
			return self::facturasPorPagar($mensaje);
		}else{
			//buscamos parametros de configuracion, si se puede seleccionar el banco cuando esta en modo pago divisas
			$isActivarBanco = Parametro::buscarVariable('select_banco_desde_modo_pago_divisa');  
    		return view('cuentasPorPagar.pagarFacturas.indexPagar',['bancos'=>$bancos,'modoPagoSelect'=>$modoPago,'cuentas'=>$todosRegistros,'id_facturas'=>$facturas,'codigo_relacion_pago'=>$codigoUnico,'isActivarBanco'=>$isActivarBanco]);
		}
			
		
    	
    	    	
		
    }

    public function prepararPagarFacturas($codigoUnico,$facturasId){
    	//Si facturaId esta vacia y codigo unico no, buscamos los id de las facturas con el codigo unico    	
    	
    	if(empty($facturasId) and !empty($codigoUnico)){
    		
    		$resulatdoRelacion = FacturasPorPagar::where('codigo_relacion_pago',$codigoUnico)->select('id')->get();
    		
    		foreach($resulatdoRelacion as $datosFactura){
    			
    			$facturasId[]=$datosFactura->id;
    		}
    		
    	}
    	
		//buscamos el valor de la tasa del dia
		$tasaDelDia=1;
		$tasaDelDia = HerramientasController::valorDolarPorFecha(date('Y-m-d'));    	
		
    	//buscamos los datos de las facturas
    	$color1='#FDEBD0';
    	$color2='#D4EFDF';
    	$cambioColor=0;
		$montoPagado=0;
		$todosRegistros=array();
    	foreach($facturasId as $facturaId){
    		////cambiamos los colores 
    		if($cambioColor==0){
    			$cambioColor=1;
    			$color = $color1;
    		}else{
    			$cambioColor=0;
    			$color = $color2;
    		}
    		$sumaDebito=0;
    		$sumaCredito=0;
    		$restaTotal=0;
    		$datosFactura = FacturasPorPagar::find($facturaId); 
			$registrosCxp = CuentasPorPagar::leftJoin('bancos','cuentas_por_pagars.banco_id','=','bancos.id')->where('cuentas_por_pagars.factura_id',$datosFactura->id)->select('cuentas_por_pagars.*','bancos.nombre')->get();
			//verificamos si es en divisas monto pagado es divisas si es bolivares tomamos los bolivares
			//si el monto en diviasas es 0 tomamos el monto en bolivares
			if($datosFactura->monto_divisa > 0.00){
				$montoPagado=$datosFactura->monto_divisa;
			}else{
				$montoPagado=$datosFactura->debitos;
			}
			
    		//buscamos y restamos los debitos menos los creditos
    		$verificarSiSeCanceloFactura = CuentasPorPagar::debitosMenosCredito($datosFactura->id);			
    		$restaTotal = $verificarSiSeCanceloFactura->resto;
    		
    		
    		$arrayRegistroCxp['cxp'] = $registrosCxp;
    		$arrayRegistroCxp['factura_id'] = $datosFactura->id;
    		$arrayRegistroCxp['tasa'] = $datosFactura->moneda_secundaria;
    		$arrayRegistroCxp['monto_divisa'] = $montoPagado;
    		$arrayRegistroCxp['color'] = $color;
    		$arrayRegistroCxp['igtf'] = $datosFactura->igtf;
    		$arrayRegistroCxp['proveedor_rif']= $datosFactura->proveedor_rif;
    		$arrayRegistroCxp['documento']= $datosFactura->documento;
    		$arrayRegistroCxp['n_control']= $datosFactura->n_control;
    		$arrayRegistroCxp['sumaDebito']= $sumaDebito;
    		$arrayRegistroCxp['sumaCredito']= $sumaCredito;
    		$arrayRegistroCxp['restaTotal']= $restaTotal;
    		$arrayRegistroCxp['igtf']=$datosFactura->igtf;
    		$arrayRegistroCxp['tasaDelDia']=$tasaDelDia;
    		$arrayRegistroCxp['codigo_relacion_pago']=$datosFactura->codigo_relacion_pago;    		

    		//si los registros no tiene un codigo de relacion se les asiganan uno
    		if(empty($datosFactura->codigo_relacion_pago)){
    			$datosFactura->codigo_relacion_pago = $codigoUnico;
    			$datosFactura->update();
    			CuentasPorPagar::where('documento',$datosFactura->documento)
	    		->where('proveedor_rif',$datosFactura->proveedor_rif)
	    		->where('n_control',$datosFactura->n_control)
	    		->update(['codigo_relacion_pago'=>$codigoUnico]);
    		}//fin if
    		//guardamos todo en un array
    		$todosRegistros[]=$arrayRegistroCxp;
    	}//fin foreach		    	
    	return $todosRegistros;
    }


    public function guardarPagarFacturas(Request $request){
    	//el select tipo tasa esta concatenada con los datos que el formulario necesita para hacer los calculo
    	//por esto el se aplica el explode para que la posicion 0 es el valor y la 1 es tasaActual 
		//$tasaManual = Parametro::buscarVariable('cxp_valor_tasa_is_manual_en_cancelar_factura');//si el valo de esta variable es 1 el valor de la tasa al cancelar las facturas en manual
    	
    	$seleccionTasaFormulario = explode('|',$request->tipo_tasa);
		//si tipoTasa que viene del formulario es tasa manual tomamos el valor del input tasa_manual
		if($seleccionTasaFormulario[1]=='tasaManual'){
			$tipoTasa = $request->tasa_manual;
		}else{
			$tipoTasa = $seleccionTasaFormulario[0];
		}
		
		

		
    
    	$valorCero=0.01;//esto indica si al cancelar la factura los debitos - creditos cuando se considera pago
    	$tipoRegistro = $request->tipo_registro;
    	$modoPago = $request->modo_pago;		
    	$bancoId = $request->banco_id ;
		$idFacturaNotaCreditoDebito = $request->nfactura_nota;
		$bancoNombre='';

		if(!empty($bancoId)){ //buscamos el nombre del banco
			$banco = DB::select('select nombre_corto from bancos where id=:bancoId',['bancoId'=>$bancoId]);
			foreach($banco as $datosBanco){
				$bancoNombre = $datosBanco->nombre_corto;
			}
			
		}
						
    	$referenciaPago = $request->referencia_pago;
    	$fechaPago = $request->fecha_pago;
    	$montoPagoIngresado = floatval($request->get('monto'));
		$copiaMontoPagoIngresado =floatval($request->get('monto'));     	
    	$idFacturasPorPagar = $request->idFacturasPorPagar;
    	$codigoRelacionPago = $request->codigo_relacion_pago;
    	$datosPagoFacturas = $request->datosPagoFactura;
		$cantidadFacturas=count($datosPagoFacturas);
    	//$tipoTasa = $request->tipo_tasa;
		$sumaMontosFacturas =0;
		$cuentasPorPagarNCP['fecha_pago'] = $fechaPago;		    	
		$cuentasPorPagarNCP['codigo_relacion_pago'] = $codigoRelacionPago;		    			    	
		$cuentasPorPagarNCP['factura_id'] = $idFacturaNotaCreditoDebito;
		$cuentasPorPagarNCP['empresaRif'] = session('empresaRif');
		$cuentasPorPagarNCP['cierre'] = $fechaPago;		   	
		$cuentasPorPagarNCP['concepto'] = $tipoRegistro;
		
		//si la moneda usada es extranjera y la sesion de pago es divisas pero el pago de las facturas es en bolivares
		//el monto que viene del formulario es en bolivares y se debe convertir para poder hacer los calculos correctos
		if(session('monedaBase')=='extranjera' and session('modoPago')=='dolares' and $modoPago=='bolivares'){
			$montoPagoIngresado = round($montoPagoIngresado / $tipoTasa,2);
			$tasa = $tipoTasa;
		}
		
		if(session('modoPago')=='bolivares'){
		$cuentasPorPagarNCP['monto_bolivares'] = $montoPagoIngresado;	
		}					
		
    	
    	//tipo de registro a insertar
    	switch ($tipoRegistro) {
    		case 'NCP':
    			// Nota de credito...
    			$conceptoDescripcion='NOTA DE CREDITOS';    			
    			$debeOhaver='creditos';
				$codConcepto=5;				
		    	if($idFacturaNotaCreditoDebito<>0){
					
					$cuentasPorPagarNCP['cod_concepto'] =$codConcepto;
					$cuentasPorPagarNCP['concepto_descripcion'] = $conceptoDescripcion;
					$cuentasPorPagarNCP[$debeOhaver] = round($montoPagoIngresado,2);						
					self::guardarEnCuentasPorPagar($cuentasPorPagarNCP);
				}
		    				
    			break;
			case 'DESC':
				// Nota de credito...
				$conceptoDescripcion='DESCUENTO';    			
				$debeOhaver='creditos';
				$codConcepto=4;				
				if($idFacturaNotaCreditoDebito<>0){
					
					$cuentasPorPagarNCP['cod_concepto'] =$codConcepto;
					$cuentasPorPagarNCP['concepto_descripcion'] = $conceptoDescripcion;
					$cuentasPorPagarNCP[$debeOhaver] = round($montoPagoIngresado,2);
					$cuentasPorPagarNCP['monto_bolivares'] =0;		
					$cuentasPorPagarNCP['tasa'] = $tipoTasa;				
					self::guardarEnCuentasPorPagar($cuentasPorPagarNCP);
				}
							
				break;
			case 'RISLR':
					// Nota de credito...
					$conceptoDescripcion='RETENCION DE ISLR';    			
					$debeOhaver='creditos';
					$codConcepto=2;
					if($idFacturaNotaCreditoDebito<>0){
						
						$cuentasPorPagarNCP['cod_concepto'] =$codConcepto;
						$cuentasPorPagarNCP['concepto_descripcion'] = $conceptoDescripcion;
						$cuentasPorPagarNCP[$debeOhaver] = round($montoPagoIngresado,2);						
						self::guardarEnCuentasPorPagar($cuentasPorPagarNCP);
					}
					break;
			case 'RIVA':
						// Nota de credito...
						$conceptoDescripcion='RETENCION DE IVA';    			
						$debeOhaver='creditos';
						$codConcepto=2;
						if($idFacturaNotaCreditoDebito<>0){
							
							$cuentasPorPagarNCP['cod_concepto'] =$codConcepto;
							$cuentasPorPagarNCP['concepto_descripcion'] = $conceptoDescripcion;
							$cuentasPorPagarNCP[$debeOhaver] = round($montoPagoIngresado,2);						
							self::guardarEnCuentasPorPagar($cuentasPorPagarNCP);
						}
						break;    		
    		case 'NDEB':
    			// Nota de debitos...
    			$conceptoDescripcion='NOTA DE DEBITOS';
    			$debeOhaver='debitos';
				$codConcepto=7;
				if($idFacturaNotaCreditoDebito<>0){
					
					$cuentasPorPagarNCP['cod_concepto'] =$codConcepto;
					$cuentasPorPagarNCP['concepto_descripcion'] = $conceptoDescripcion;
					$cuentasPorPagarNCP[$debeOhaver] = round($montoPagoIngresado,2);						
					self::guardarEnCuentasPorPagar($cuentasPorPagarNCP);
				}
    			break;    		
    		case 'CAN':
    			// Pago de Facturas...
    			$conceptoDescripcion='PAGO DE FACTURA';
    			$debeOhaver='creditos';
				$codConcepto=0;
    			break;	    		
    		default:
    			// Pago de Facturas...
    			$conceptoDescripcion='PAGO DE FACTURA';
    			$debeOhaver='creditos';
    			break;
    	}

    	foreach ($datosPagoFacturas as $index => $datos) {
			
    		$datosPagoFactura = explode("|",$datos);
    		$montoBs = floatval($datosPagoFactura[0]);//el monto de la factura			
			$tasa = $datosPagoFactura[1];//valor de la tasa del dolar en bs    		
    		$montoDivisaFactura = floatval($datosPagoFactura[2]);//monto en divisa de la factura
			
    		$proveedorRif = $datosPagoFactura[3];
    		$documento = $datosPagoFactura[4];
    		$nControl = $datosPagoFactura[5];
    		$igtf = $datosPagoFactura[6];
    		$facturaId = $datosPagoFactura[7];

    		$datosFactura = FacturasPorPagar::find($facturaId);
    		
    		if(empty($datosFactura->fecha_real_pago)){
    			//si la fecha_real_pago esta vacia se asigna una fecha de lo contrario se deja la misma
    			$fechaRealPago = date('Y-m-d');
    		}else{
    			$fechaRealPago = $datosFactura->fecha_real_pago;
    		}

    		if(empty($codigoRelacionPago)){
    			//si viene vacio el codigo de relacion del pago se asigna el de la factura
    			$codigoRelacionPago = $datosFactura->codigo_relacion_pago;
    		}
    		//buscamos la facturasPorPagars verificamos si el pago se concreto
    		//$factura = FacturasPorPagar::find($facturaId);

    		//si el monto es 0 no se guarda asiento de pago porque ya esta cancelada la factura
			//$valorCero = 0.00 se define al inicio del metodo
    		if($montoBs > $valorCero){
				
    			$cuentasPorPagar['banco_id'] = $bancoId;
		    	$cuentasPorPagar['referencia_pago'] = $referenciaPago;
		    	$cuentasPorPagar['fecha_pago'] = $fechaPago;		    	
		    	$cuentasPorPagar['codigo_relacion_pago'] = $datosFactura->codigo_relacion_pago;
		    	$cuentasPorPagar['proveedorRif'] = $proveedorRif;
		    	$cuentasPorPagar['documento'] = $documento;
		    	$cuentasPorPagar['ncontrol'] = $nControl;		    	
		    	$cuentasPorPagar['factura_id'] = $facturaId;
		    	$cuentasPorPagar['empresaRif'] = session('empresaRif');
		    	$cuentasPorPagar['cierre'] = $datosFactura->fecha_factura;
    			    			  					
    			if(session('modoPago')=='dolares'){

					//si MonedaBase es NACIONAL y los pagos son en Divisas

					if(session('monedaBase')=='nacional'){	    		
						//si el monto en divisa de la factura es menor al monto ingresado se descuenta toda la factura
						if($tipoRegistro == 'CAN'){
							//if($montoDivisaFactura < $montoPagoIngresado and $modoPago<>'bolivares'){
							if($montoDivisaFactura <= $montoPagoIngresado and $datosFactura->pago_efectuado==0 and $modoPago<>'bolivares'){
									//restamos el monto en divisas de la factura cancelada
								//caso 1
								
								$cuentasPorPagar['observacion'] = $request->observacion;//'caso 1 fac:'.$montoDivisaFactura.' pag:'.$montoPagoIngresado;
								$cuentasPorPagar['concepto'] = $tipoRegistro;
								$cuentasPorPagar['concepto_descripcion'] = $conceptoDescripcion;
								$cuentasPorPagar[$debeOhaver] = $montoBs;	
								$cuentasPorPagar['tasa'] = $tipoTasa;		    	
								//$cuentasPorPagar['monto_divisa'] = ($montoBs/$tasa);
								$cuentasPorPagar['monto_divisa'] = HerramientasController::valorAlCambioMonedaSecundaria($montoBs,$tasa);			    	
								$montoPagoIngresado = $montoPagoIngresado - $montoDivisaFactura;
								self::guardarEnCuentasPorPagar($cuentasPorPagar);
								//comparamos si la duda se cancelo en la factura y actualizamos la bandera
								//facturas_pagada
								$verificarSiSeCanceloFactura = CuentasPorPagar::debitosMenosCredito($facturaId);		
								if($verificarSiSeCanceloFactura->resto <= $valorCero){
									FacturasPorPagar::where('id',$verificarSiSeCanceloFactura->factura_id)->update(['pago_efectuado'=>1,'fecha_real_pago'=>$fechaRealPago]);
									// aqui se registrara las deducciones de la sra helen 
								}
								continue;
							}			    		

							//si es mayor es porque el monto de la siguiente factura es mayor al que queda en las divisas para pagar
							//if($montoDivisaFactura > $montoPagoIngresado and $montoPagoIngresado>0.00 and $modoPago<>'bolivares'){
							if($montoDivisaFactura > $montoPagoIngresado and $montoPagoIngresado>0.00 and $datosFactura->pago_efectuado==0 and $modoPago<>'bolivares'){
								//el monto registrado es el abonado del saldo que quedo para pagar
								//caso 3
								
								$montoBs = ($montoPagoIngresado) * $tasa;
								$cuentasPorPagar['observacion'] =  $request->observacion;//'caso 3 fac:'.$montoDivisaFactura.' pag:'.$montoPagoIngresado;
								$cuentasPorPagar['concepto'] = $tipoRegistro;
								$cuentasPorPagar['concepto_descripcion'] = $conceptoDescripcion;
								$cuentasPorPagar[$debeOhaver] = $montoBs;					
								//$cuentasPorPagar['monto_divisa'] = ($montoBs/$tasa);
								$cuentasPorPagar['monto_divisa'] = HerramientasController::valorAlCambioMonedaSecundaria($montoBs,$tasa);
								$montoPagoIngresado=0;
								$cuentasPorPagar['tasa'] = $tipoTasa;
								
								self::guardarEnCuentasPorPagar($cuentasPorPagar);
								//comparamos si la duda se cancelo en la factura y actualizamos la bandera
								//facturas_pagada
								$verificarSiSeCanceloFactura = CuentasPorPagar::debitosMenosCredito($facturaId);		
								if($verificarSiSeCanceloFactura->resto <= $valorCero){
									FacturasPorPagar::where('id',$verificarSiSeCanceloFactura->factura_id)->update(['pago_efectuado'=>1,'fecha_real_pago'=>$fechaRealPago]);
									// aqui se registrara las deducciones de la sra helen 
								}
								continue;    			
							} 							

							//validamos si el modo pago es divisa y un pago se hace en bs y la tasa es la del dia
							if(session('modoPago')=='dolares' and $modoPago=='bolivares'){							
								//caso 4
								
								//si el monto de la tasa es mayor al de la factura
								if($index == count($datosPagoFacturas)-1){
									$notaDebito = $montoPagoIngresado-$montoBs;
									if($notaDebito > $valorCero){
																
										$cuentasPorPagar['debitos'] = $notaDebito;
										$cuentasPorPagar['concepto']='NDEB';
										$cuentasPorPagar['creditos']=0;
										$cuentasPorPagar['cod_concepto']= 7;
										$cuentasPorPagar['monto_divisa'] =0;
										$cuentasPorPagar['monto_bolivares'] =0;
										$cuentasPorPagar['concepto_descripcion']='NOTA DE DEBITOS - POR AUMENTO DE TASA';
										self::guardarEnCuentasPorPagar($cuentasPorPagar);
									}
									if($notaDebito < $valorCero){
										
										$cuentasPorPagar['creditos'] =$notaDebito;	
										$cuentasPorPagar['debitos']=0;
										$cuentasPorPagar['cod_concepto']= 8;		    		
										$cuentasPorPagar['concepto']='NCP';
										$cuentasPorPagar['monto_divisa'] =0;
										$cuentasPorPagar['monto_bolivares'] =0;
										$cuentasPorPagar['concepto_descripcion']='NOTA DE CREDITOS - POR DISMINUCION DE TASA';
										self::guardarEnCuentasPorPagar($cuentasPorPagar);
									}
								} 		    		
								//despues de registrar el debito o credito para que cuadre la factura registramos la cancelacion
								$montoGuardar=0;
								switch($montoPagoIngresado){
									case $montoPagoIngresado >= $montoBs:
										if($index < count($datosPagoFacturas)-1){
											$montoGuardar = $montoBs;
											$montoPagoIngresado = $montoPagoIngresado - $montoBs;
										}else{
											$montoGuardar = $montoPagoIngresado;
										}
										break;								
									case $montoPagoIngresado < $montoBs:
										$montoGuardar = $montoPagoIngresado;
										$montoPagoIngresado = $montoPagoIngresado - $montoBs;
										break;
								}
								$cuentasPorPagar['observacion'] =  $request->observacion;//'caso 4';
								$cuentasPorPagar['concepto'] = $tipoRegistro;
								$cuentasPorPagar['concepto_descripcion'] = 'PAGO DE FACTURA Bs.';
								$cuentasPorPagar['creditos'] = $montoGuardar;
								$cuentasPorPagar['debitos']=0;
								$cuentasPorPagar['tasa'] = $tipoTasa;
								$cuentasPorPagar['monto_bolivares'] = $montoGuardar;	
								//$cuentasPorPagar['monto_divisa'] = ($montoGuardar/$tasa);
								$cuentasPorPagar['monto_divisa'] = HerramientasController::valorAlCambioMonedaSecundaria($montoGuardar,$tasa);					
								self::guardarEnCuentasPorPagar($cuentasPorPagar);
								//comparamos si la duda se cancelo en la factura y actualizamos la bandera
								//facturas_pagada
								$verificarSiSeCanceloFactura = CuentasPorPagar::debitosMenosCredito($facturaId);		
								if($verificarSiSeCanceloFactura->resto <= $valorCero){
									FacturasPorPagar::where('id',$verificarSiSeCanceloFactura->factura_id)->update(['pago_efectuado'=>1,'fecha_real_pago'=>$fechaRealPago]);
									// aqui se registrara las deducciones de la sra helen 
								}
								
								
							}//fin sesion dolares modo pago Bs y tasa actual 				    	

						}else{///Fin registro 'CAN'	
						
						//validamos si se inserta una nota de debito o credito va en bolivares	
						//caso 6
							if($idFacturaNotaCreditoDebito==0 and $montoPagoIngresado > $valorCero){
								//si no seleccionaron ninguna factura pero seleccionaron todas las facturas
								//se reparte el monto de la nota de credito o debito entre las facturas
								$montoGuardar=0;
								switch($montoPagoIngresado){
									case $montoPagoIngresado >= $montoBs:
										$montoGuardar = $montoBs;
										$montoPagoIngresado = $montoPagoIngresado - $montoBs;
										break;								
									case $montoPagoIngresado < $montoBs:
										$montoGuardar = $montoPagoIngresado;
										$montoPagoIngresado = $montoPagoIngresado - $montoBs;
										break;
								}
								$cuentasPorPagar['observacion'] =  $request->observacion;//'caso 6';    	
								$cuentasPorPagar['concepto'] = $tipoRegistro;
								$cuentasPorPagar['concepto_descripcion'] = $conceptoDescripcion;
								$cuentasPorPagar[$debeOhaver] = round($montoGuardar,2);		
								$cuentasPorPagar['tasa'] = $tipoTasa;			
								//$cuentasPorPagar['monto_bolivares'] = $montoGuardar;						
								self::guardarEnCuentasPorPagar($cuentasPorPagar);
								//comparamos si la duda se cancelo en la factura y actualizamos la bandera
								//facturas_pagada
								$verificarSiSeCanceloFactura = CuentasPorPagar::debitosMenosCredito($facturaId);
																
								if($verificarSiSeCanceloFactura->resto <= $valorCero){								
									FacturasPorPagar::where('id',$verificarSiSeCanceloFactura->factura_id)->update(['pago_efectuado'=>1,'fecha_real_pago'=>$fechaRealPago]);
									// aqui se registrara las deducciones de la sra helen 
								}
								continue;
							}	
						}
						//FIN MONEDA NACIONAL CON PAGO EN DIVISAS
					}else{
						//si la monedaBase es $$$$ EXTRANJERA $$$ Y SE PAGAN EN DIVISAS
						//si el monto en divisa de la factura es menor al monto ingresado se descuenta toda la factura
						if($tipoRegistro == 'CAN'){
							
							if($montoBs <= $montoPagoIngresado and $datosFactura->pago_efectuado==0 and $modoPago<>'bolivares'){
									//restamos el monto en divisas de la factura cancelada
								//caso 1
								
								$cuentasPorPagar['observacion'] =  $request->observacion;//'caso 1.1 fac:'.$montoBs.' extranje pag:'.$montoPagoIngresado;
								$cuentasPorPagar['concepto'] = $tipoRegistro;
								$cuentasPorPagar['concepto_descripcion'] = $conceptoDescripcion;
								$cuentasPorPagar[$debeOhaver] = $montoBs;	
								$cuentasPorPagar['tasa'] = $tipoTasa;		    	
								//$cuentasPorPagar['monto_divisa'] = ($montoBs/$tasa);
								$cuentasPorPagar['monto_divisa'] = HerramientasController::valorAlCambioMonedaSecundaria($montoPagoIngresado,$tasa);			    	
								$montoPagoIngresado = $montoPagoIngresado - $montoBs;
								self::guardarEnCuentasPorPagar($cuentasPorPagar);
								//comparamos si la duda se cancelo en la factura y actualizamos la bandera
								//facturas_pagada
								$verificarSiSeCanceloFactura = CuentasPorPagar::debitosMenosCredito($facturaId);		
								if($verificarSiSeCanceloFactura->resto <= $valorCero){
									FacturasPorPagar::where('id',$verificarSiSeCanceloFactura->factura_id)->update(['pago_efectuado'=>1,'fecha_real_pago'=>$fechaRealPago]);
									// aqui se registrara las deducciones de la sra helen 
								}
								continue;
							}
							
							//si el monto de la facturas en menor al monto ingresado es en sesion divisas y se cancela en bolivares
							if($montoBs <= $montoPagoIngresado and $datosFactura->pago_efectuado==0 and $modoPago=='bolivares'){
								
								//restamos el monto en divisas de la factura cancelada
								//caso 1.2
								
								$cuentasPorPagar['observacion'] =  $request->observacion;//'caso 1.2 fac:'.$montoBs.' extranje pag:'.$montoPagoIngresado;
								$cuentasPorPagar['concepto'] = $tipoRegistro;
								$cuentasPorPagar['concepto_descripcion'] = $conceptoDescripcion;
								$cuentasPorPagar[$debeOhaver] = $montoBs;			    	
								//$cuentasPorPagar['monto_divisa'] = ($montoBs/$tasa);
								$cuentasPorPagar['monto_divisa'] = HerramientasController::valorAlCambioMonedaSecundaria($montoPagoIngresado,$tasa);
								$cuentasPorPagar['tasa'] = $tipoTasa;			    	
								$montoPagoIngresado = $montoPagoIngresado - $montoBs;
								self::guardarEnCuentasPorPagar($cuentasPorPagar);
								//comparamos si la duda se cancelo en la factura y actualizamos la bandera
								//facturas_pagada
								$verificarSiSeCanceloFactura = CuentasPorPagar::debitosMenosCredito($facturaId);		
								if($verificarSiSeCanceloFactura->resto <= $valorCero){
									FacturasPorPagar::where('id',$verificarSiSeCanceloFactura->factura_id)->update(['pago_efectuado'=>1,'fecha_real_pago'=>$fechaRealPago]);
									// aqui se registrara las deducciones de la sra helen 
								}
								continue;
							}

							//si es mayor es porque el monto de la siguiente factura es mayor al que queda en las divisas para pagar
							
							if($montoBs > $montoPagoIngresado and $montoPagoIngresado>0.00 and $datosFactura->pago_efectuado==0 and $modoPago<>'bolivares'){
								//el monto registrado es el abonado del saldo que quedo para pagar
								//caso 3
								
								//$montoBs = ($montoPagoIngresado) * $tasa;
								$cuentasPorPagar['observacion'] =  $request->observacion;//'caso 3 fac:'.$montoBs.' extranje pag:'.$montoPagoIngresado;
								$cuentasPorPagar['concepto'] = $tipoRegistro;
								$cuentasPorPagar['concepto_descripcion'] = $conceptoDescripcion;
								$cuentasPorPagar[$debeOhaver] = $montoPagoIngresado;					
								//$cuentasPorPagar['monto_divisa'] = ($montoBs/$tasa);
								$cuentasPorPagar['monto_divisa'] = HerramientasController::valorAlCambioMonedaSecundaria($montoPagoIngresado,$tasa); 
								$montoPagoIngresado=0;
								$cuentasPorPagar['tasa'] = $tipoTasa;
								
								self::guardarEnCuentasPorPagar($cuentasPorPagar);
								//comparamos si la duda se cancelo en la factura y actualizamos la bandera
								//facturas_pagada
								$verificarSiSeCanceloFactura = CuentasPorPagar::debitosMenosCredito($facturaId);		
								if($verificarSiSeCanceloFactura->resto <= $valorCero){
									FacturasPorPagar::where('id',$verificarSiSeCanceloFactura->factura_id)->update(['pago_efectuado'=>1,'fecha_real_pago'=>$fechaRealPago]);
									// aqui se registrara las deducciones de la sra helen 
								}
								continue;    			
							} 	
							
							//si es mayor es porque el monto de la siguiente factura es mayor al que queda en las divisas para pagar esto es con modo de pago bolivares
							
							if($montoBs > $montoPagoIngresado and $montoPagoIngresado>0.00 and $datosFactura->pago_efectuado==0 and $modoPago=='bolivares'){
								//el monto registrado es el abonado del saldo que quedo para pagar
								//caso 3.2
								dd('linea 1459');
								//$montoBs = ($montoPagoIngresado) * $tasa;
								$cuentasPorPagar['observacion'] =  $request->observacion;//'caso 3.2 fac:'.$montoBs.' extranje pag:'.$montoPagoIngresado;
								$cuentasPorPagar['concepto'] = $tipoRegistro;
								$cuentasPorPagar['concepto_descripcion'] = $conceptoDescripcion;
								$cuentasPorPagar[$debeOhaver] = $montoPagoIngresado;					
								//$cuentasPorPagar['monto_divisa'] = ($montoBs/$tasa);
								$cuentasPorPagar['monto_divisa'] = HerramientasController::valorAlCambioMonedaSecundaria($montoPagoIngresado,$tasa); 
								$montoPagoIngresado=0;
								$cuentasPorPagar['tasa'] = $tipoTasa;
								
								self::guardarEnCuentasPorPagar($cuentasPorPagar);
								//comparamos si la duda se cancelo en la factura y actualizamos la bandera
								//facturas_pagada
								$verificarSiSeCanceloFactura = CuentasPorPagar::debitosMenosCredito($facturaId);		
								if($verificarSiSeCanceloFactura->resto <= $valorCero){
									FacturasPorPagar::where('id',$verificarSiSeCanceloFactura->factura_id)->update(['pago_efectuado'=>1,'fecha_real_pago'=>$fechaRealPago]);
									// aqui se registrara las deducciones de la sra helen 
								}
								continue;    			
							}

							//validamos si el modo pago es divisa y un pago se hace en bs y la tasa es la del dia
							 if(session('modoPago')=='dolares' and $modoPago=='bolivares'){							
								//caso 4

								//$montoPagoIngresado = ($montoPagoIngresado/$tasa);
								//si el monto de la tasa es mayor al de la factura
								if($index == count($datosPagoFacturas)-1){
									
									$montoNotaDebitoCredito = $montoPagoIngresado-$montoBs;
									
									if($montoNotaDebitoCredito > $valorCero){
																
										$cuentasPorPagar['debitos'] = $montoNotaDebitoCredito;
										$cuentasPorPagar['concepto']='NDEB';
										$cuentasPorPagar['creditos']=0;
										$cuentasPorPagar['cod_concepto']= 7;
										$cuentasPorPagar['monto_divisa'] =0;
										$cuentasPorPagar['monto_bolivares'] =0;
										$cuentasPorPagar['concepto_descripcion']='NOTA DE DEBITOS - POR DIFERENCIAL DE TASA #'.$tipoTasa;
										self::guardarEnCuentasPorPagar($cuentasPorPagar);
									}
									if($montoNotaDebitoCredito < $valorCero){
										
										$cuentasPorPagar['creditos'] =$montoNotaDebitoCredito;	
										$cuentasPorPagar['debitos']=0;
										$cuentasPorPagar['cod_concepto']= 8;		    		
										$cuentasPorPagar['concepto']='NCP';
										$cuentasPorPagar['monto_divisa'] =0;
										$cuentasPorPagar['monto_bolivares'] =0;
										$cuentasPorPagar['concepto_descripcion']='NOTA DE CREDITOS - POR DISMINUCION DE TASA';
										self::guardarEnCuentasPorPagar($cuentasPorPagar);
									}
								} 		    		
								//despues de registrar el debito o credito para que cuadre la factura registramos la cancelacion
								$montoGuardar=0;
								switch($montoPagoIngresado){
									case $montoPagoIngresado >= $montoBs:
										if($index < count($datosPagoFacturas)-1){
											$montoGuardar = $montoBs;
											$montoPagoIngresado = $montoPagoIngresado - $montoBs;
										}else{
											$montoGuardar = $montoPagoIngresado;
										}
										break;								
									case $montoPagoIngresado < $montoBs:
										$montoGuardar = $montoPagoIngresado;
										$montoPagoIngresado = $montoPagoIngresado - $montoBs;
										break;
								}
								$cuentasPorPagar['observacion'] = //'caso 4 extranje';
								$cuentasPorPagar['concepto'] = $tipoRegistro;
								$cuentasPorPagar['concepto_descripcion'] = 'PAGO DE FACTURA Bs.';
								$cuentasPorPagar['creditos'] = $montoPagoIngresado;
								$cuentasPorPagar['debitos']=0;
								$cuentasPorPagar['tasa'] = $tasa;
								$cuentasPorPagar['monto_bolivares'] = $montoGuardar;	
								//$cuentasPorPagar['monto_divisa'] = ($montoGuardar/$tasa);
								$cuentasPorPagar['monto_divisa'] = $montoGuardar;					
								self::guardarEnCuentasPorPagar($cuentasPorPagar);
								//comparamos si la duda se cancelo en la factura y actualizamos la bandera
								//facturas_pagada
								$verificarSiSeCanceloFactura = CuentasPorPagar::debitosMenosCredito($facturaId);		
								if($verificarSiSeCanceloFactura->resto <= $valorCero){
									FacturasPorPagar::where('id',$verificarSiSeCanceloFactura->factura_id)->update(['pago_efectuado'=>1,'fecha_real_pago'=>$fechaRealPago]);
									// aqui se registrara las deducciones de la sra helen 
								}
																
							} //fin sesion dolares modo pago Bs y tasa actual 				    	

						}else{///Fin registro 'CAN'	
						
						//validamos si se inserta una nota de debito o credito va en la monedaBase	
						//caso 6
							if($idFacturaNotaCreditoDebito==0 and $montoPagoIngresado > $valorCero){
								//dd('estoy linea 1497',$tipoTasa);
								//si no seleccionaron ninguna factura pero seleccionaron todas las facturas
								//se reparte el monto de la nota de credito o debito entre las facturas
								$montoGuardar=0;
								switch($montoPagoIngresado){
									case $montoPagoIngresado >= $montoBs:
										$montoGuardar = $montoBs;
										$montoPagoIngresado = $montoPagoIngresado - $montoBs;
										break;								
									case $montoPagoIngresado < $montoBs:
										$montoGuardar = $montoPagoIngresado;
										$montoPagoIngresado = $montoPagoIngresado - $montoBs;
										break;
								}
								$cuentasPorPagar['observacion'] =  $request->observacion;//'caso 6';    	
								$cuentasPorPagar['concepto'] = $tipoRegistro;
								$cuentasPorPagar['concepto_descripcion'] = $conceptoDescripcion;
								$cuentasPorPagar[$debeOhaver] = round($montoGuardar,2);	
								$cuentasPorPagar['tasa'] = $tipoTasa;				
								//$cuentasPorPagar['monto_bolivares'] = $montoGuardar;						
								self::guardarEnCuentasPorPagar($cuentasPorPagar);
								//comparamos si la duda se cancelo en la factura y actualizamos la bandera
								//facturas_pagada
								$verificarSiSeCanceloFactura = CuentasPorPagar::debitosMenosCredito($facturaId);
																
								if($verificarSiSeCanceloFactura->resto <= $valorCero){								
									FacturasPorPagar::where('id',$verificarSiSeCanceloFactura->factura_id)->update(['pago_efectuado'=>1,'fecha_real_pago'=>$fechaRealPago]);
									// aqui se registrara las deducciones de la sra helen 
								}
								continue;
							}	
						}
					}	
    			}//fin session(modoPago)== dolares
    			/////////////////////////////////////////////BOLIVALES//////////////////////////////////////	
				if(session('modoPago')=='bolivares'){
						//si el monto en bolivares de la factura es menor al monto ingresado se descuenta toda la factura
		    		if($tipoRegistro == 'CAN'){
		    			
			    		if($montoBs < $montoPagoIngresado and $modoPago=='bolivares'){
							//restamos el monto en divisas de la factura cancelada
							//caso 1
							$notaDebito=0;
							if($montoPagoIngresado > $request->total_facturas){

				    			/* si el monto de pago ingresado es mayor al de las facturas resto la diferencia entre ambos y creo una nota de debito, para despues tomar ese monto y se suma al contador de $montoPagoIngresado, de esta forma al realizar el pago 7 mata 7*/

					    		$notaDebito = $montoPagoIngresado - $request->total_facturas;
					    		$montoPagoIngresado = $montoPagoIngresado + $notaDebito ;
					    		$cuentasPorPagar['debitos'] = $notaDebito;
					    		$cuentasPorPagar['concepto']='NDEB';
					    		$cuentasPorPagar['creditos']=0;
					    		$cuentasPorPagar['concepto_descripcion']='NOTA DE DEBITOS - POR AUMENTO DE TASA';
								$cuentasPorPagar['observacion'] =  $request->observacion;//'caso 1 Ndeb:'.$montoBs.' pag:'.$montoPagoIngresado;
					    		self::guardarEnCuentasPorPagar($cuentasPorPagar);

				    		}
				    		$cuentasPorPagar['debitos'] =0;						    
						    $cuentasPorPagar['observacion'] = $request->observacion; //'caso 1 fac:'.$montoBs.' pag:'.$montoPagoIngresado;
						    $cuentasPorPagar['concepto'] = $tipoRegistro;
							$cuentasPorPagar['concepto_descripcion'] = $conceptoDescripcion;
							$cuentasPorPagar[$debeOhaver] = $montoBs+$notaDebito;			    	
					    	$cuentasPorPagar['monto_divisa'] = 0;				    	
					    	$montoPagoIngresado = $montoPagoIngresado - ($montoBs+$notaDebito);
					    	self::guardarEnCuentasPorPagar($cuentasPorPagar);

					    	//comparamos si la duda se cancelo en la factura y actualizamos la bandera
	    					//facturas_pagada
	    					$verificarSiSeCanceloFactura = CuentasPorPagar::debitosMenosCredito($facturaId);									
				    		if($verificarSiSeCanceloFactura->resto <= $valorCero){
								
				    			FacturasPorPagar::where('id',$verificarSiSeCanceloFactura->factura_id)->update(['pago_efectuado'=>1,'fecha_real_pago'=>$fechaRealPago]);
				    			// aqui se registrara las deducciones de la sra helen 
				    		}
					    	continue;
			    		}

			    		if($montoBs == $montoPagoIngresado and $modoPago=='bolivares'){
							//restamos el monto en divisas de la factura cancelada
							//caso 2
						    
						    $cuentasPorPagar['observacion'] = $request->observacion; //'caso 2';
						    $cuentasPorPagar['concepto'] = $tipoRegistro;
							$cuentasPorPagar['concepto_descripcion'] = $conceptoDescripcion;
							$cuentasPorPagar[$debeOhaver] = $montoBs;								    	
					    	$cuentasPorPagar['monto_divisa'] = 0;
					    	$montoPagoIngresado = $montoPagoIngresado - $montoBs;
					    	self::guardarEnCuentasPorPagar($cuentasPorPagar);
					    	//comparamos si la duda se cancelo en la factura y actualizamos la bandera
	    					//facturas_pagada
	    					$verificarSiSeCanceloFactura = CuentasPorPagar::debitosMenosCredito($facturaId);							
				    		if($verificarSiSeCanceloFactura->resto <= $valorCero){
				    			FacturasPorPagar::where('id',$verificarSiSeCanceloFactura->factura_id)->update(['pago_efectuado'=>1,'fecha_real_pago'=>$fechaRealPago]);
				    			// aqui se registrara las deducciones de la sra helen 
				    		}
					    	continue;
			    		}

			    		//si es mayor es porque el monto de la siguiente factura es mayor al que queda en las divisas para pagar
						if($montoBs > $montoPagoIngresado and $montoPagoIngresado > $valorCero and $modoPago=='bolivares'){
			    			//el monto registrado es el abonado del saldo que quedo para pagar
			    			//caso 3			
			    				    			
			    			$cuentasPorPagar['observacion'] = $request->observacion; //'caso 3 fac:'.$montoBs.' pag:'.$montoPagoIngresado;
			    			$cuentasPorPagar['concepto'] = $tipoRegistro;
							$cuentasPorPagar['concepto_descripcion'] = $conceptoDescripcion;
							$cuentasPorPagar[$debeOhaver] = $montoPagoIngresado;					
					    	$cuentasPorPagar['monto_divisa'] = 0;
					    	$montoPagoIngresado=0;
					    	self::guardarEnCuentasPorPagar($cuentasPorPagar);
							//comparamos si la duda se cancelo en la factura y actualizamos la bandera
							//facturas_pagada
							$verificarSiSeCanceloFactura = CuentasPorPagar::debitosMenosCredito($facturaId);								
							if($verificarSiSeCanceloFactura->resto <= $valorCero){
								FacturasPorPagar::where('id',$verificarSiSeCanceloFactura->factura_id)->update(['pago_efectuado'=>1,'fecha_real_pago'=>$fechaRealPago]);
								// aqui se registrara las deducciones de la sra helen 
							}				    	
					    	continue;    			
			    		} 					    	
				    
				    }else{///Fin registro 'CAN'	
				    
			    	//validamos si se inserta una nota de debito o credito va en bolivares	
			    	//caso 6
						if($idFacturaNotaCreditoDebito==0 and $montoPagoIngresado > $valorCero){
							//si no seleccionaron ninguna factura pero seleccionaron todas las facturas
							//se reparte el monto de la nota de credito o debito entre las facturas
							$montoGuardar=0;
							switch($montoPagoIngresado){
								case $montoPagoIngresado > $montoBs:
									$montoGuardar = $montoBs;
									$montoPagoIngresado = $montoPagoIngresado - $montoBs;
									break;
								case $montoPagoIngresado == $montoBs:
									$montoGuardar = $montoBs;
									$montoPagoIngresado = $montoPagoIngresado - $montoBs;
									break;
								case $montoPagoIngresado < $montoBs:
									$montoGuardar = $montoPagoIngresado;
									$montoPagoIngresado = $montoPagoIngresado - $montoBs;
									break;
							}
							$cuentasPorPagar['observacion'] = $request->observacion; //'caso 6';    	
							$cuentasPorPagar['concepto'] = $tipoRegistro;
							$cuentasPorPagar['concepto_descripcion'] = $conceptoDescripcion;
							$cuentasPorPagar[$debeOhaver] = round($montoGuardar,2);					
							$cuentasPorPagar['monto_bolivares'] = $montoGuardar;						
							self::guardarEnCuentasPorPagar($cuentasPorPagar);
							//comparamos si la duda se cancelo en la factura y actualizamos la bandera
							//facturas_pagada
							$verificarSiSeCanceloFactura = CuentasPorPagar::debitosMenosCredito($facturaId);
															
							if($verificarSiSeCanceloFactura->resto <= $valorCero){								
								FacturasPorPagar::where('id',$verificarSiSeCanceloFactura->factura_id)->update(['pago_efectuado'=>1,'fecha_real_pago'=>$fechaRealPago]);
								// aqui se registrara las deducciones de la sra helen 
							}
							continue;
						}
						
			    	}
				}//fin modo pago Bolivares		    	 
		    			    	
    		}//fin $montoBs > 0.00    			
    		$sumaMontosFacturas +=  $montoPagoIngresado;
			
    	}//fin foreach
		    	
    	return self::retornarVistaPagarFacturas($idFacturasPorPagar,$codigoRelacionPago);
    }

    private function datosEmpresa($rif){
    	return DB::select('select * from empresas where rif=:rif',[$rif]);
    }

    /////////////////////INGRESAR OTOS PAGOS///////////////////////////////
    /*Registro de servicios y nominas especiales este metodo muestra el formulario
     de registro, que a su ver guardara en cuentas_por_pagars como credito*/
    public function addOtrosPagos(){
    	$herramientas = new HerramientasController();
     	return view('cuentasPorPagar.otrospagos',['empresas'=>$herramientas->listarEmpresas(),'bancos' => Banco::all()]);
    }

    public function saveOtrosPagos(Request $request){
    	$codigoUnico = uniqid();//genera un codigo unico en php    	
    	$monto= HerramientasController::convertirMonto($request->creditos);
    	$monedaSecundaria = HerramientasController::valorDolarPorFecha($request->fecha_pago);
    	$modoPago = session('modoPago');
    	//$empresa = explode('|',$request->get('empresa'));
    	$facturaPorPagar = new FacturasPorPagar;
		$facturaPorPagar->empresa_rif = session('empresaRif');
		$facturaPorPagar->fecha_factura = $request->fecha_pago;			
		$facturaPorPagar->proveedor_rif = $request->proveedor_rif;
		$facturaPorPagar->proveedor_nombre = $request->beneficiario;
		$facturaPorPagar->moneda_secundaria = $monedaSecundaria;			
		$facturaPorPagar->debitos = $monto;
		$facturaPorPagar->creditos = 0.00;
		$facturaPorPagar->poriva = 0.00;
		$facturaPorPagar->montoiva = 0.00;
		$facturaPorPagar->gravado = 0.00;
		$facturaPorPagar->excento = 0.00;		
		$facturaPorPagar->concepto = 'FAC';
		$facturaPorPagar->origen = 'local';
		$facturaPorPagar->dias_credito = $request->dias_credito;
		$facturaPorPagar->porcentaje_descuento =$request->porcentaje_descuento;
		$facturaPorPagar->modo_pago = $modoPago;
		$facturaPorPagar->codigo_relacion_pago = $codigoUnico;
		$facturaPorPagar->pago_efectuado = 1;
		$facturaPorPagar->fecha_real_pago=date('Y-m-d');        
		$facturaPorPagar->save();

		///guardamos registro de la factura en cuentas por pagar
		$codTipoMoneda=intval(session('codTipoMoneda'));
		$arrayRegistro = array('empresaRif'=>session('empresaRif'),'concepto_descripcion'=>'GASTOS','ncontrol'=>'000','cierre'=>$request->fecha_pago,'fecha_pago'=>$request->fecha_pago,'proveedorRif'=>$request->proveedor_rif,'proveedorNombre'=>$request->beneficiario,'concepto'=>'FAC','documento'=>'000','debitos'=>$monto,'poriva'=>'0.00','montoiva'=>'0.00','gravado'=>'0.00','exento'=>'0.00','codigo_relacion_pago'=>$codigoUnico,'factura_id'=>$facturaPorPagar->id,'usuario'=>auth()->user()->name);
		$idCuentasPagar = self::guardarEnCuentasPorPagar($arrayRegistro);            
		

    	//crear asiento del debito

		// crear el asiento del credito
		$arrayRegistro['concepto']='CAN';
		$arrayRegistro['debitos']=0;
		$arrayRegistro['creditos']=$monto;
		$arrayRegistro['pago_efectuado']=1;		
		$arrayRegistro['referencia_pago']=$request->referencia_pago;
		$arrayRegistro['banco_id']=$request->banco_id;
		$arrayRegistro['concepto_descripcion']=$request->get('concepto_descripcion');

		self::guardarEnCuentasPorPagar($arrayRegistro);    	
    	return self::facturasPorPagar();
    }


    //////ELIMINAR TODAS LAS FACTURAS VINCULADAS/////////////////////////
    //--------ESTO ELIMINA TODOS LOS REGISTROS QUE CONTENGA EN CODIGO DE RELACION --//
    public function eliminarTodasPorPagar($codRealcion){

    	//eliminamos la relacion de las facturas
    	DB::select("update cuentas_por_pagars set codigo_relacion_pago=null,pago_efectuado=0 
    		where codigo_relacion_pago=:codigo",[$codRealcion]);
    	DB::select("update facturas_por_pagars set codigo_relacion_pago=null,pago_efectuado=0 
    		where codigo_relacion_pago=:codigo",[$codRealcion]);
    	//eliminamos los asientos registrados como retenciones y pagos
    	DB::select("delete from cuentas_por_pagars where 
    		codigo_relacion_pago=:codigo and concepto<>'FAC'",[$codRealcion]);
    	return self::facturasPorPagar();
    }

    public function desvincularAsientoCuentasPorPagar($id,$codigoRelacion){
    	FacturasPorPagar::where('id','=',$id)
    	->update(['codigo_relacion_pago'=>'']);
    	CuentasPorPagar::where('factura_id','=',$id)->update(['codigo_relacion_pago'=>'']);
    	CuentasPorPagar::where('factura_id','=',$id)->where('concepto','CAN')->delete();
    	return self::listadoFacturasCalculadas();
    }

    public function desvincularAsientoCuentasPorPagarBolivares($id,$codigoRelacion){
    	FacturasPorPagar::where('id','=',$id)
    	->update(['codigo_relacion_pago'=>'']);
    	CuentasPorPagar::where('factura_id','=',$id)->update(['codigo_relacion_pago'=>'']);
    	CuentasPorPagar::where('factura_id','=',$id)->where('concepto','CAN')->delete();
    	return self::facturasPorPagar();
    }
	
	public function editarFacturasPorPagar($id,$urlRetorno='cuentasporpagar.facturasPorPagar'){
		//eliminar registro de factura en facturas por pagar y cuentas por pagar
    	//ya que al insertar un factura tambien se inserta un asiento de cuentas por pagar
		
    	$factura = FacturasPorPagar::findOrFail($id);
		$factura->is_factura_revisada=true;//marcamos que la factura fue revisada por el relacionador de facturas
		$factura->update(); 
		   	    	   	
    	return view('cuentasPorPagar.editarFacturasPorPagar',['factura'=>$factura,'urlRetorno'=>$urlRetorno]);
	}

	public function updateFacturasPorPagar(Request $request,$id){
		$urlRetorno = $request->url_de_retorno;
		$factura = facturasPorPagar::find($id);
		$factura->dias_credito = $request->dias_credito;
		$factura->porcentaje_descuento = $request->porcentaje_descuento;
		$factura->moneda_secundaria = $request->valor_tasa;
		$factura->update();

		//eliminamos de cuentas por pagar el registro de porcentaje de descuento previo si lo hay
		CuentasPorPagar::where('factura_id',$id)->where('cod_concepto',4)->delete();

		//si tiene porcfentaje de descuento se agrega en cxp
		if($request->porcentaje_descuento > 0){
			
			$porcDescuento = $request->porcentaje_descuento;
			$monto = $factura->debitos;

			//verificamos que la factura tenca iva
			$retencionIva= self::buscarRetencionIva($factura->proveedor_rif,$factura->montoiva);			
			
			//verificamos si el proveedor tiene retencion de islr
			$retencionIslr = self:: calcularRetencionISLR($factura->proveedor_rif,$factura->excento);
			
			$descuento = (($monto - $retencionIva - $retencionIslr)*$porcDescuento)/100;            			
			
			$arrayRegistro['empresaRif'] = session('empresaRif');
			$arrayRegistro['factura_id'] = $factura->id;
			$arrayRegistro['ncontrol'] = $factura->n_control;
			$arrayRegistro['cierre'] = $factura->cierre;
			$arrayRegistro['proveedorRif'] = $factura->proveedor_rif;	
			$arrayRegistro['proveedorNombre'] = $factura->proveedor_nombre;	
			$arrayRegistro['documento'] = $factura->documento;	
			$arrayRegistro['poriva'] = $factura->poriva;				
			$arrayRegistro['gravado'] = $factura->gravado;
			$arrayRegistro['exento'] = $factura->excento;
			$arrayRegistro['debitos'] = 0;
			$arrayRegistro['montoiva'] = 0;
			$arrayRegistro['creditos'] = $descuento;
			$arrayRegistro['concepto'] = 'DESC';
			$arrayRegistro['cod_concepto']= 4;
			$arrayRegistro['concepto_descripcion']='DESCUENTO DEL '.$porcDescuento.'%';
			
			
			self::guardarEnCuentasPorPagar($arrayRegistro);
			//fin guardar en cuentas por pagar
		}
		return redirect()->route($urlRetorno);
	}

    public function eliminarFacturasPorPagar($id,$urlRetorno='cuentasporpagar.facturasPorPagar'){
    	//eliminar registro de factura en facturas por pagar y cuentas por pagar
    	//ya que al insertar un factura tambien se inserta un asiento de cuentas por pagar

    	$factura = FacturasPorPagar::findOrFail($id);
    	$eliminarFactura = FacturasPorPagar::where('empresa_rif',$factura->empresa_rif)
    	->where('documento',$factura->documento)
    	->where('n_control',$factura->n_control)    	
    	->where('proveedor_rif',$factura->proveedor_rif)
    	->delete();
    	CuentasPorPagar::where('empresa_rif',$factura->empresa_rif)
    	->where('documento',$factura->documento)
    	->where('n_control',$factura->n_control)    	
    	->where('proveedor_rif',$factura->proveedor_rif)
    	->delete();    	   	
    	return redirect()->route($urlRetorno);    	
    }

    public function elimarAsientoCuentasPorPagar($id=0,$codigoRelacion=0){

    	//eliminar los registros de cuentas_por_pagar, por pagar esto es desde la vista indexPagar    	
    	$eliminarAsiento = CuentasPorPagar::findOrFail($id);
		
		if($eliminarAsiento->cod_concepto==4){
			//si el registro a leiminar es descuento(cod_concepto==4) buscamos en facturas por pagar y editamos el campo de % desceunto a 0
			facturasPorPagar::where('id',$eliminarAsiento->factura_id)->update(['porcentaje_descuento'=>0.00]);
		}		   	
    	$eliminarAsiento->delete();//eliminamos en cuentas por pagar
		
		$verificarSiSeCanceloFactura = CuentasPorPagar::debitosMenosCredito($eliminarAsiento->factura_id); //validamos que la factura este cancelada o no
											
		if(floatval($verificarSiSeCanceloFactura->resto) > 0.00){								
			FacturasPorPagar::where('id',$verificarSiSeCanceloFactura->factura_id)->update(['pago_efectuado'=>0]);
			
			// aqui se registrara las deducciones de la sra helen 
		}     	
    	return self::verVistaPagarFacturas($codigoRelacion,$id);
    }


    /////////////Recibo Pago Facturas////////////
    public function reciboPagoFacturas($codigoRelacion){
    	$facturas = array();		
    	$datosDelPago = self::prepararPagarFacturas($codigoRelacion,$facturas);
		$empresa = Empresa::where('rif',session('empresaRif'))->first();
    	return view('cuentasPorPagar.pagarFacturas.reciboPago',['datosDelPago'=>$datosDelPago,'empresa'=>$empresa]);
    }
 
    ///////////////RELACION PAGO FACTURAS EN DIVISAS //////////
    public function relacionPagoFacturasIndex(){
    	if(empty(session('empresaRif')) or empty(session('modoPago'))){
    		return self::seleccionarEmpresa('relacionPagoFacturasIndex');
    	}
    	$listadoFacturasPorPagar = self::prepararFacturasPorPagar('dolares');
    	return view('cuentasPorPagar.relacionPagoFacturas.index',['cuentas'=>$listadoFacturasPorPagar]);
    }

    public function calculoDeDeudasPorFacturas(Request $request){
    	$facturasPorPagar = $request->facturasPorPagar;
    	$facturasConIslr = $request->islr;
    	$igtfs = $request->igtf;
    	$montoGravado=0;
    	$montoExcento=0;
    	$ivaRetener=0;
    	$islrRetener=0;
    	$banderaRetencionIslr = 0;
    	$banderaRetencionIva = 0;
    	$banderaIgtf =0;

    	//recorremos el arreglo de facturas seleccionadas
    	foreach ($facturasPorPagar as $idFactura) {
			$montoIgtf=0;
			$islrRetener=0;
			$banderaRetencionIslr=0;
    		//buscamos los datos de la factura
    		$factura = FacturasPorPagar::find($idFactura);
			$monedaSecundaria = $factura->moneda_secundaria;			
    		//buscamos datos del proveedor
    		$datos_proveedor = Proveedor::where('rif',$factura->proveedor_rif)->first(); 

    		//verificar si no viene vacio seleccion de ISLR
    		if(!empty($facturasConIslr)){

	    		//comparamos si la factura lleva retencion de islr
	    		foreach($facturasConIslr as $idFacIslr){
	    			if($idFactura==$idFacIslr){
	    				$PorcRetencion = $datos_proveedor->ultimo_porcentaje_retener_islr;

						//si el proveedor tiene porcentaje de retencion islr
						if($PorcRetencion > 0){
							if($datos_proveedor->tipo_contribuyente=="Juridico"){							
								$sustraendo=0;
							}else{								
								$porcenReten = Retencion::where('procent_retencion',$PorcRetencion)->first();
								$sustraendo=$porcenReten->sustraendo;
							}
							
							$montoExcento = $factura->excento;			
							$islrRetener = ((($montoExcento*$PorcRetencion)/100)-$sustraendo);
							$banderaRetencionIslr=1;
						}else{
							$islrRetener=0;
							$banderaRetencionIslr=0;
						}
	    			}
	    		}
				$factura->is_retencion_islr = $banderaRetencionIslr;
	    		$factura->retencion_islr    = $islrRetener;	    		
	    	}

	    	//realizamos el calculo del pago de la factura para luego guardar los datos
	    	$facturasPorPagar = new FacturasPorPagar();//monto de la factura menos nota de credito

	    	$debitoMenosCredito = $facturasPorPagar->buscarFacturaConNotaCredito(
	    		$factura->proveedor_rif,
	    		$factura->documento,
	    		$factura->fecha_factura
	    	); 
	    	
	    	$montoTotalBs= 0;
			$porceDescuento = 0;
			$descuento = 0;
			$motoTotalDivisas = 0;			

			//calculos matematicos de los datos de la factura
			$porceDescuento = $factura->porcentaje_descuento;								
			$subTotalBs = floatval($debitoMenosCredito->resto)-($ivaRetener+$islrRetener);
			$descuento = ($subTotalBs*$porceDescuento)/100;
			$montoTotalBs = $subTotalBs - $descuento;
			if($monedaSecundaria==0){
				$monedaSecundaria = HerramientasController::valorDolarPorFecha($factura->fecha_factura);
				
			}
			if($monedaSecundaria==0){
				dd('moneda secundaria en facturas_por_pagars no tiene monto valido, si esta usando como referencia la tasa del siace verifique la tabla tipo_moneda en el campo presio_compra_moneda_nacional, si esta usando como referencia la tasa de historial_dolar debe actualizar el valor');
			}
			//$motoTotalDivisas = $montoTotalBs/$monedaSecundaria;
			$motoTotalDivisas = HerramientasController::valorAlCambioMonedaSecundaria($montoTotalBs,$monedaSecundaria);
			//si no hay seleccion de IGTF porcentajeIgtf=0 para que al multiplicar de lo mismo y no incremente el %
			//verificamos los checkbox seleccionados de IGTF
			if(!empty($igtfs)){
				foreach($igtfs as $idFacturaIgtf){
					if($idFacturaIgtf == $idFactura){
						$porcentajeIgtf = Parametro::buscarVariable('igtf');				
						$montoIgtf = ($motoTotalDivisas*floatval($porcentajeIgtf))/100;     
						$igtf_bs = $montoIgtf*$monedaSecundaria;
						if($porcentajeIgtf==""){

							dd("la variable igtf no se encuentra creada en la tabla parametros, agregue ese registro e indiquele en numeros el porcentaje a calcular Ej: si es 3% se agrega 3.00");}

						//creamos el asiento del igtf en cuentas por pagar pero en bs
						$datos = array(
							'empresaRif'=>$factura->empresa_rif,
							'ncontrol'=>$factura->n_control,
							'proveedorRif'=>$factura->proveedor_rif,
							'cod_concepto'=>6,
							'concepto'=>'NDEB',
							'concepto_descripcion'=>'IMPUESTO IGTF',
							'documento'=>$factura->documento,
							'debitos'=>$igtf_bs,
							'creditos'=>0,
							'factura_id'=>$factura->id,
							'codigo_relacion_pago'=>$factura->codigo_relacion_pago,
							'observacion'=>'en facturas calculadas',
						);
						self::guardarEnCuentasPorPagar($datos);
					}
				}
			}else{
				$montoIgtf=0;
			}
	    	//actualizamos banderas y montos de retencion islr igtf y montos	    	
			
	    	$factura->monto = $montoTotalBs;
	    	$factura->monto_divisa = $motoTotalDivisas;	    	
	    	$factura->igtf = $montoIgtf;
	    	$factura->is_apartada_pago  = 1;
	    	$factura->fecha_real_pago = $request->fecha_real_pago;
	    	$factura->update();
	    	//limpiamos variables
    		$banderaRetencionIslr= 0;
    		$banderaRetencionIva = 0;
    		$montoGravado        = 0;
			$montoExcento        = 0;
			$ivaRetener          = 0;
			$islrRetener         = 0;		

    	}//fin del foreach
    	//retornamos a una vista
    	return self::listadoFacturasCalculadas();	
    }//fin Metodo calculoDeDeudasPorFacturas

    public function seleccionarRangoFechaFacturasCalculadas(Request $request){
    	/* $fechas = explode(' - ',$request->get('semana_relacion_facturas')); */
    	$fechaini = $request->fechaini;
    	$fechafin = $request->fechafin;
    	session(['fecha_ini_relacion_pago'=>$fechaini,'fecha_fin_relacion_pago'=>$fechafin]);
    	return self::listadoFacturasCalculadas();
    }

    public function listadoFacturasCalculadas(){
    	if(empty(session('empresaRif')) or empty(session('modoPago'))){
    		return self::seleccionarEmpresa('listadoFacturasCalculadas');
    	}
    	$fechaini='';
    	$fechafin='';
		$banderaFacturaSiaceEncontrada = 0;
		$montoOrigenFactura = 0;
    	$fechaini = session('fecha_ini_relacion_pago');
    	$fechafin = session('fecha_fin_relacion_pago');
    	$arrayFechas=[];
    	$arrayFacturas=[];
		$herramientas  = new HerramientasController();

		//comprobar si esta activa la opcion de verificar facturas en el siace
		//si lo esta la buscamos en la tabla cxp del siace en caso de no encontrarla enviar una bandera par resaltar el error
		$verificarFacturaSiace = Parametro::buscarVariable('verificar_facturas_en_siace');
		if($verificarFacturaSiace==''){
			dd('No esta definido en la configuracion si se puede o no verificar facturas en el siace');
		}

		//buscamos en cuentas por pagars el total a cancelar
    	//$totalCancelar = CuentasPorPagar::debitosMenosCredito($facturaPorPagar->id);
    	if(session('fecha_ini_relacion_pago')<> null){
    		$facturasCalculadas = new FacturasPorPagar();
	    	$fechasPagos = $facturasCalculadas->fechasPagoFacturasApartadas($fechaini,$fechafin);
	    	
	    	foreach ($fechasPagos as $fechaPago) {
	    		$facturas = $facturasCalculadas->listarFacturasPagoCalculado($fechaPago->fecha_real_pago,$fechaPago->proveedor_rif);
	    		$listadoFacturasPorPagar=array();
	    		foreach ($facturas as $facturaPorPagar){
					$montoOrigenFactura=0;
	    			//buscamos en cuentas por pagars el total a cancelar
    				$totalCancelar = CuentasPorPagar::debitosMenosCreditoSoloDeuda($facturaPorPagar->id);
					$notasDebitoAumentoTasa = CuentasPorPagar::sumaDebitosPorAumentoTasa($facturaPorPagar->id);
					$totalFactutrasPorProveedor = $facturasCalculadas->contarFacturasDelProveedorPorRangoDeFechaCalculada($facturaPorPagar->fecha_real_pago,$facturaPorPagar->proveedor_rif);
					
					//verificar si hay que verificar la factura y buscar en el siace
					//esto es por si elimina del siace una factura por relacionar o ya relacionada
					//ya que si las elimnan no se les debe pagar all proveedor bien sea por devolucion
					if($verificarFacturaSiace==1 and $facturaPorPagar->origen=='siace'){
						$conexionSQL = $herramientas->conexionDinamicaBD(session('basedata'));    				
						$registros = $conexionSQL->select("SELECT keycodigo,debitos from cxp where codorigen=2000 and documento=:nfactura and rif=:rifProveedor order by keycodigo",['nfactura'=>$facturaPorPagar->documento,'rifProveedor'=>$facturaPorPagar->proveedor_rif]);
						foreach($registros as $registro){
							if($registro->keycodigo > 0){
								$banderaFacturaSiaceEncontrada=1;
								$montoOrigenFactura = $registro->debitos;
							}
						}
					}else{
						//si no esta habilitada la configuracion de veriifcar las facturas en el siace, la bandela la dejamos en 1 
						//para que asuma que la encontron y no tilde de rojo la factura ya que la verificacion esta desactivada
						$banderaFacturaSiaceEncontrada=1;
					}

					$factura= array(
    				  'id'=>$facturaPorPagar->id,
		              'empresa_rif'=>$facturaPorPagar->empresa_rif,		              
		              'pago_efectuado'=>$facturaPorPagar->pago_efectuado,		              
		              'fecha_factura'=>$facturaPorPagar->fecha_factura,
		              'n_control'=>$facturaPorPagar->n_control,
		              'cierre'=>$facturaPorPagar->cierre,
		              'proveedor_rif'=>$facturaPorPagar->proveedor_rif,
		              'proveedor_nombre'=>$facturaPorPagar->proveedor_nombre,
		              'is_apartada_pago'=>$facturaPorPagar->is_apartada_pago,
		              'documento'=>$facturaPorPagar->documento,
		              'debitos'=>$facturaPorPagar->debitos,
		              'creditos'=>$facturaPorPagar->creditos,  
		              'resto'=>$totalCancelar->resto,
					  'ndebAumentoTasa'=>$notasDebitoAumentoTasa,
		              'concepto'=>$facturaPorPagar->concepto,
		              'codigo_relacion_pago'=>$facturaPorPagar->codigo_relacion_pago,
		              'poriva'=>$facturaPorPagar->poriva,
		              'montoiva'=>$facturaPorPagar->montoiva,
		              'gravado'=>$facturaPorPagar->gravado,
		              'excento'=>$facturaPorPagar->excento,
		              'dias_credito'=>$facturaPorPagar->dias_credito,
		              'fecha_pago'=>$fechaPago,
		              'fecha_real_pago'=>$facturaPorPagar->fecha_real_pago,		              
		              'porcentaje_descuento'=>$facturaPorPagar->porcentaje_descuento,
		              'modo_pago'=>$facturaPorPagar->modo_pago,
		              'moneda_secundaria'=>$facturaPorPagar->moneda_secundaria,
		              'cod_modo_pago'=>$facturaPorPagar->cod_modo_pago,
		              'is_apartada_pago'=>$facturaPorPagar->is_apartada_pago,
					  'desapartada_pago'=>$facturaPorPagar->desapartada_pago,
		              'is_retencion_islr'=>$facturaPorPagar->is_retencion_islr,
		              'retencion_islr'=>$facturaPorPagar->retencion_islr,
		              'retencion_iva'=>$facturaPorPagar->retencion_iva,
		              'observacion'=>$facturaPorPagar->observacion,
		              'igtf'=>$facturaPorPagar->igtf,
		              'usuario'=>$facturaPorPagar->usuario,	
					  'totalFactutrasPorProveedor'=>$totalFactutrasPorProveedor,
					  'montoOrigenFactura'=>$montoOrigenFactura,	
					  'banderaFacturaSiaceEncontrada'=>$banderaFacturaSiaceEncontrada,              
    				);
    				$listadoFacturasPorPagar[]=(object)$factura;
					
	    		}
	    		$facturas='';
				
	    		//dd($listadoFacturasPorPagar);
	    		$arrayFacturas['fechaPagoAcordado']=$fechaPago->fecha_real_pago;
	    		$arrayFacturas['montoPagar']=$fechaPago->monto;
	    		$arrayFacturas['facturas'] = $listadoFacturasPorPagar;
	    		$arrayFechas[]=(object)$arrayFacturas;
	    		
	    	}
			
    	}    	
    	return view('cuentasPorPagar.relacionPagoFacturas.listadoFacturasCalculadas',
    		['fechaFacturas'=>$arrayFechas,'fechaini'=>$fechaini,'fechafin'=>$fechafin]);
    }
    

    public function eliminaFacturaCalculada($id){

    	//quitar la factura de la lista de facturas calculadas`para el pago
    	//solo desactivamos la vandera de is_apartada_pago
    	FacturasPorPagar::where('id','=',$id)
    	->update([
    		'is_apartada_pago'=>0,
    		'is_retencion_islr'=>0,
    		'is_retencion_iva'=>0,
    		'igtf'=>0,
    		'fecha_real_pago'=>'',
			'desapartada_pago'=>1
    	]);
    	//eliminamos el asiento del igtf en cuentas:por_pagars
    	CuentasPorPagar::where('factura_id',$id)->where('cod_concepto',6)->delete();
    	return self::listadoFacturasCalculadas();

    }

    public function reportePagoBolivaresDeDolares(){
		if(empty(session('empresaRif'))){
    		return self::seleccionarEmpresa('reportePagoBolivares');
    	}
    	return view('cuentasPorPagar.reportes.reporteBolivaresEntregados');
    }

    public function resulReportePagoBolivaresDeDolares(Request $request){
    	$pagosBs = CuentasPorPagar::where('cierre' ,'>=', $request->fechaIni)
    	->where('cierre' ,'<=', $request->fechaFin)
    	->where('concepto','=','CAN')
    	->where('monto_bolivares','>',0.00)
    	->get();
    	return view('cuentasPorPagar.reportes.reporteBolivaresEntregados',['pagosBs'=>$pagosBs]); 
    }
}
