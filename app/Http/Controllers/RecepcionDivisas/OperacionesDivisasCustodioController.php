<?php

namespace App\Http\Controllers\RecepcionDivisas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Herramientas\HerramientasController;
class OperacionesDivisasCustodioController extends Controller
{
    public function index(){
		$herramientas = new HerramientasController();
		return view('divisasCustodio.listarOperacionesDivisas',['empresas'=>$herramientas->listarEmpresas()]);
	}


	public function buscarOperacionDivisa(Request $request){
		$fecha 	   = $request->get('fecha');
    	$conexion  = $request->get('conexion');
    	$herramientas = new HerramientasController();
    	$conexionSQL = $herramientas->conexionDinamicaBD($conexion);

		$empresa = self::datosEmpresa($conexion);
		$registros = $conexionSQL->table('operaciones_en_divisas_custodio')->where('fecha','=',$request->get('fecha'))->orderBy('codusua','asc')->get();
		//dd($registros);
		return view('divisasCustodio.listarOperacionesDivisas',['registrosDiario'=>$registros,'fecha'=>$fecha,'datosEmpresa'=>$empresa,'empresas'=>$herramientas->listarEmpresas()]);

	}

	public function buscarOperacionDivisaAsesor(Request $request,$conexion,$fecha){
		$empresa = self::datosEmpresa($conexion);
		$asesor = $request->get('busqueda');
		$herramientas = new HerramientasController();
    	$conexionSQL = $herramientas->conexionDinamicaBD($conexion);
		$registros = $conexionSQL
			->table('operaciones_en_divisas_custodio')
			->where([
				['fecha','=',$fecha],
				['usuario','like','%'.$asesor.'%']
				
			])
			->orderBy('keycodigo','asc')
			->get();
		//dd($registros);
		return view('divisasCustodio.listarOperacionesDivisas',['registrosDiario'=>$registros,'fecha'=>$fecha,'datosEmpresa'=>$empresa,'empresas'=>$herramientas->listarEmpresas()]);
	}

    public function create(){
    	$herramientas = new HerramientasController();
    	$conexionSQL = $herramientas->conexionDinamicaBD('hptal');
    	//para que funcione el goup by de mysql en laravel hay que cambiar en el archivo de configuracion de larave config/database.php en mysql buscar 'strict' => true, y cambiarlo a false
    	$users = $conexionSQL->select("SELECT facturas.codusua,facturas.usuario FROM facturas WHERE fecha=curdate() GROUP BY facturas.usuario");
    	$cotizacionDivisa = $conexionSQL->select("SELECT precio_venta_moneda_nacional FROM tipo_moneda where keycodigo=:id",['id'=>'3']);    	

    	return view('divisasCustodio.create',['asesores'=>$users,'cotizacionDivisa'=>$cotizacionDivisa[0]]);
    }


    public function saveOperacionDivisa(Request $request){
    	//separamos el codigo y nombre del asesor ya que vienen juntos en el select
    	$asesores=explode('-',$request->get('asesor'));
    	$conexion=$request->get('conexion');
    	$herramientas = new HerramientasController();
    	$conexionSQL = $herramientas->conexionDinamicaBD($conexion);

    	//convertimos el monto en formato valido para mysql
    	$cotizacion=self::strToDecimal($request->get('cotizacion'));
    	$cambioPagoMovil=self::strToDecimal($request->get('montoCambioEnPagoMovil'));

    	$conexionSQL->table('operaciones_en_divisas_custodio')->insert([
            'cotizacion' => $cotizacion,
            'codusua' => $asesores[0],
            'usuario' =>$asesores[1],
            'divisa_a_consumir'=>$request->get('divisasAconsumir'),
            'divisa_a_recibir'=>$request->get('divisasRecibidas'),
            'monto_divisa_a_consumir_en_bolivares'=>$request->get('montoAconsumirEnBolivares'),
            'divisa_para_cambio_en_efectivo'=>$request->get('divisasCambioEfectivo'),
            'monto_para_cambio_en_pago_movil'=>$cambioPagoMovil,
            'numero_factura'=>$request->get('numFactura'),
            'entidad'=>$request->get('entidad'),
            'referencia_del_pago_movil'=>$request->get('referenciaPagoMovil'),
            'fecha'=>date('Y-m-d'),
            'registrado'=>NOW(),
           
        ]);

        $users = $conexionSQL->select("SELECT facturas.codusua,facturas.usuario FROM facturas WHERE fecha=CURDATE() GROUP BY facturas.usuario");
    	$cotizacionDivisa = $conexionSQL->select("SELECT precio_venta_moneda_nacional FROM tipo_moneda where keycodigo=:id",['id'=>'3']);
    	//buscamos por fecha
    	//$registros = self::buscarPorFecha(date('Y-m-d'));
    	$asesores="";
    	return view('divisasCustodio.create',['asesores'=>$users,'cotizacionDivisa'=>$cotizacionDivisa[0]]);
    }
 

    public function strToDecimal($valor){
    	//este metodo cambia los montos 1.000.000,00 en validos para mysql 1000000.00
    	$decimal = str_replace(',', '.', str_replace('.', '', $valor));
    	return $decimal;
	}


	public function edit($id){
		return view();
	}


	public function reporteGeneral(){
		$herramientas = new HerramientasController();
		//mostrar la vista del formulario reporte para la administradora
		return view('divisasCustodio.reporteGeneral',['empresas'=>$herramientas->listarEmpresas()]);
	}


	public function resultadoReporteGeneral(Request $request){
		//mostrar el reporte general de la administradora
		
		$fecha = $request->get('fecha');
		$conexion = $request->get('conexion');
		$herramientas = new HerramientasController();
		$conexionSQL = $herramientas->conexionDinamicaBD($conexion);
		$empresa =self::datosEmpresa($conexion);
		
		try{		
			$registros = $conexionSQL->select("SELECT
				  cotizacion,
				  SUM(divisa_a_consumir)AS divisa_a_consumir,
				  SUM(divisa_a_recibir)AS divisa_a_recibir,
				  SUM(monto_divisa_a_consumir_en_bolivares)AS monto_divisa_a_consumir_en_bolivares,
				  SUM(divisa_para_cambio_en_efectivo)AS divisa_para_cambio_en_efectivo,
				  SUM(monto_para_cambio_en_pago_movil)AS monto_para_cambio_en_pago_movil,
				  entidad,
				  fecha,
				  registrado
				FROM
				  operaciones_en_divisas_custodio
				WHERE fecha =:actual
				GROUP BY cotizacion",['actual'=>$fecha]);
			return view('divisasCustodio.reporteGeneral',['registrosDiario'=>$registros,'conexion'=>$conexion,'fecha'=>$fecha,'datosEmpresa'=>$empresa,'empresas'=>$herramientas->listarEmpresas()]);
		}catch (Exception $e) {
			//mostrar la vista del formulario reporte para la administradora
			return view('divisasCustodio.reporteGeneral',['empresas'=>$herramientas->listarEmpresas()]);
		}
	}


	public function reporteDetalladoGerencia($conexion,$tasa,$fecha){
		//reporte detallado de la administradora
		$herramientas = new HerramientasController();
		$conexionSQL = $herramientas->conexionDinamicaBD($conexion);
		$empresa =self::datosEmpresa($conexion);
		$registros = $conexionSQL->select("SELECT
			  *
			FROM
			  operaciones_en_divisas_custodio
			WHERE fecha =:fechaD
			AND cotizacion =:tasa
			",['fechaD'=>$fecha,'tasa'=>$tasa]);
		
		return view('divisasCustodio.reporteDetalleGerencia',[
			'registros'=>$registros,
			'datosEmpresa'=>$empresa,
			'empresas'=>$herramientas->listarEmpresas()
		]);
	}


	public function reporteRecaudo(){	
		//vista al formulario de reporte de recaudo
		$herramientas = new HerramientasController();					
		return view('divisasCustodio.reporteRecaudo',['empresas'=>$herramientas->listarEmpresas()]);
	}


	public function buscarReporteRecaudo(Request $request){
		//mostrar reporte de recaudo
		$fecha = $request->get('fecha');
		$conexion = $request->get('conexion');
		$herramientas = new HerramientasController();	
		$empresa =self::datosEmpresa($conexion);
		$general = self::ejecutarReporteRecaudo($conexion,$fecha);				
		return view('divisasCustodio.reporteRecaudo',['recaudos'=>$general,'datosEmpresa'=>$empresa,'fecha'=>$fecha,'empresas'=>$herramientas->listarEmpresas()]);
	}


	private function ejecutarReporteRecaudo($conexion,$fecha){

		//reporte para Recaudo $$$$ del dia actual
		$totalRecaudo=self::totalRecaudoAsesor($conexion,$fecha);
		$general=array();
		foreach ($totalRecaudo as $recaudo){
			//inicializar los contadores
			$divisa_consumida=0;
			$monto_en_bs_divisa_consumida=0;
			$divisa_recibida=0;
			$pagomovil=0;
			$divisa_para_cambio_en_efectivo=0;

			//buscar total recaudo por cotizacion
			$porCotizacion=self::totalRecaudoCotizacionAsesor($conexion,$fecha,$recaudo->codusua);
			//buscar los detallados de la tabla mov_pagos por asesor
			$porMovPagos = self::reporteMov_pagosPorAsesor($conexion,$fecha,$recaudo->codusua);

			//sumamos todos los totales de las cotizaciones
			foreach ($porCotizacion as $value) {
				$divisa_consumida = $divisa_consumida + $value->divisa_consumida;
				$monto_en_bs_divisa_consumida = $monto_en_bs_divisa_consumida+$value->monto_en_bs_divisa_consumida;
				$divisa_recibida = $divisa_recibida + $value->divisa_recibida;
				$pagomovil = $pagomovil + $value->pagomovil;
				$divisa_para_cambio_en_efectivo = $divisa_para_cambio_en_efectivo +$value->divisa_para_cambio_en_efectivo;
			}
			//llenamos el arreglo que contiene todos los datos de los asesores de venta
			$recaudoGeneral=array(
				'id'=>$recaudo->keycodigo,
				'codusua'=>$recaudo->codusua,
				'usuario'=>$recaudo->usuario,
				'cotizacion'=>$porCotizacion,//detallado de la cotizacion
				'fecha'=>$recaudo->fecha,
				'mov_pagos'=>$porMovPagos,
				'divisa_consumida'=>$divisa_consumida,
				'monto_en_bs_divisa_consumida'=>$monto_en_bs_divisa_consumida,
				'divisa_recibida'=>$divisa_recibida,
				'pagomovil'=>$pagomovil,
				'divisa_para_cambio_en_efectivo'=>$divisa_para_cambio_en_efectivo
			);
			$general[]=$recaudoGeneral;
		}		
		return $general;
	}


	///////////////////----- inicio Pago Movil ----------/////////////////////////
	public function listarPagoMovil(){	
		$herramientas = new HerramientasController();
		return view('divisasCustodio.listarPagoMovil',['empresas'=>$herramientas->listarEmpresas()]);
	}


	private function buscarPagoMovilPorFecha($conexion,$fecha){
		$herramientas = new HerramientasController();
		$conexionSQL = $herramientas->conexionDinamicaBD($conexion);
    	return $registros = $conexionSQL->select("select * from operaciones_en_divisas_custodio where monto_para_cambio_en_pago_movil<>0 and fecha=:actual",['actual'=>$fecha]);
    }


    private function buscarPagoMovilPorId($conexion,$id){
    	$herramientas = new HerramientasController();
		$conexionSQL = $herramientas->conexionDinamicaBD($conexion);
    	return $registros = $conexionSQL->select("select * from operaciones_en_divisas_custodio where keycodigo=:id",['id'=>$id]);
    }


	public function buscarListarPagoMovil(Request $request){
		$herramientas = new HerramientasController();
		$empresa =self::datosEmpresa($request->get('conexion'));
		$pagosmoviles= self::buscarPagoMovilPorFecha($request->get('conexion'),$request->get('fecha'));
		return view('divisasCustodio.listarPagoMovil',['pagosMoviles'=>$pagosmoviles,'conexion'=>$request->get('conexion'),'fecha'=>$request->get('fecha'),'datosEmpresa'=>$empresa,'empresas'=>$herramientas->listarEmpresas()]);
	}


	public function procesarPagoMovil($conexion,$id){
		$buscarPagoMovil=self::buscarPagoMovilPorId($conexion,$id);
		return view('divisasCustodio.procesarPagoMovil',['buscarPagoMovil'=>$buscarPagoMovil,'id'=>$id,'conexion'=>$conexion]);
	}


	public function savePagoMovil(Request $request,$conexion,$id){
		$referencia=$request->get('referenciaPagoMovil');
		$entidad=$request->get('entidad');
		$tlfCliente=$request->get('tlf');
		$empresa =self::datosEmpresa($conexion);
		$herramientas = new HerramientasController();
		$conexionSQL = $herramientas->conexionDinamicaBD($conexion);
		$conexionSQL->select("UPDATE operaciones_en_divisas_custodio 
			SET referencia_del_pago_movil=:referencia,entidad=:nomentidad,tlf_cliente=:tlfCliente 
			where keycodigo=:keyPago",['referencia'=>$referencia,'nomentidad'=>$entidad,'tlfCliente'=>$tlfCliente,'keyPago'=>$id]);
		$pagosmoviles= self::buscarPagoMovilPorFecha($conexion,date('Y-m-d'));

		return view('divisasCustodio.listarPagoMovil',['pagosMoviles'=>$pagosmoviles,'conexion'=>$conexion,'datosEmpresa'=>$empresa,'empresas'=>$herramientas->listarEmpresas()]);
	}


	public function anularPagoMovil($conexion,$id){
		$herramientas = new HerramientasController();
		$conexionSQL = $herramientas->conexionDinamicaBD($conexion);
		$empresa =self::datosEmpresa($conexion);
		$conexionSQL->select("update operaciones_en_divisas_custodio set monto_para_cambio_en_pago_movil =0 where keycodigo=:id",[$id]);
		$pagosmoviles= self::buscarPagoMovilPorFecha($conexion,date('Y-m-d'));
		return view('divisasCustodio.listarPagoMovil',['pagosMoviles'=>$pagosmoviles,'conexion'=>$conexion,'datosEmpresa'=>$empresa,'empresas'=>$herramientas->listarEmpresas()]);
	}
	/////////////////// -----fin Pago Movil --------///////////////////////////


	public function datosEmpresa($db){
		
		//reotrna el nombre de la empresa con el nombre de la conexion
		$herramientas = new HerramientasController();
		$conexionSQL = $herramientas->conexionDinamicaBD($db);
		 $empresa = $conexionSQL->select("select nombre as nombre_empresa,basedata as conexion from farmacias where basedata=:valor",['valor'=>$db]);
		 return $empresa;
	}


	private function totalRecaudoCotizacionAsesor($conexion,$fecha,$codAsesor){
		///reporte de los asesores por cotizacion
		$herramientas = new HerramientasController();
		$conexionSQL = $herramientas->conexionDinamicaBD($conexion);
		$recaudos=$conexionSQL->select("
			SELECT
			  codusua,
			  usuario,
			  cotizacion,
			  fecha,
			  registrado,
			  SUM(divisa_a_consumir) AS divisa_consumida,
			  SUM(monto_divisa_a_consumir_en_bolivares) AS monto_en_bs_divisa_consumida,
			  SUM(divisa_a_recibir) AS divisa_recibida,
			  SUM(monto_para_cambio_en_pago_movil) AS pagomovil,
			  SUM(divisa_para_cambio_en_efectivo) AS divisa_para_cambio_en_efectivo
			FROM
			  operaciones_en_divisas_custodio
			WHERE usuario NOT LIKE'TUR%'
			AND fecha =:fecharecaudo
			AND codusua=:codasesor
			GROUP BY usuario,
			  cotizacion
			UNION ALL
			SELECT
			  codusua,
			  usuario,
			  cotizacion,
			  fecha,
			  registrado,
			  SUM(divisa_a_consumir) AS divisa_consumida,
			  SUM(monto_divisa_a_consumir_en_bolivares) AS monto_en_bs_divisa_consumida,
			  SUM(divisa_a_recibir) AS divisa_recibida,
			  SUM(monto_para_cambio_en_pago_movil) AS pagomovil,
			  SUM(divisa_para_cambio_en_efectivo) AS divisa_para_cambio_en_efectivo
			FROM
			  operaciones_en_divisas_custodio
			WHERE usuario LIKE'TUR%'
			AND codusua=:codasesor1
			AND (registrado BETWEEN :horatur1ini AND :horatur1fin)			
			GROUP BY usuario,
			  cotizacion
			UNION ALL  
			SELECT
			  codusua,
			  usuario,
			  cotizacion,
			  fecha,
			  registrado,
			  SUM(divisa_a_consumir) AS divisa_consumida,
			  SUM(monto_divisa_a_consumir_en_bolivares) AS monto_en_bs_divisa_consumida,
			  SUM(divisa_a_recibir) AS divisa_recibida,
			  SUM(monto_para_cambio_en_pago_movil) AS pagomovil,
			  SUM(divisa_para_cambio_en_efectivo) AS divisa_para_cambio_en_efectivo
			FROM
			  operaciones_en_divisas_custodio
			WHERE usuario LIKE'TUR%'
			AND codusua=:codasesor2
			AND (registrado BETWEEN :horatur2ini AND :horatur2fin)
			GROUP BY usuario,
			  cotizacion",
			['fecharecaudo'=>$fecha,'codasesor'=>$codAsesor,'codasesor1'=>$codAsesor,'horatur1ini'=>$fecha.' 00:00:01','horatur1fin'=>$fecha.'  08:30:00','codasesor2'=>$codAsesor,'horatur2ini'=>$fecha.' 18:00:01','horatur2fin'=>$fecha.' 23:59:59']);
		

		return $recaudos;
	}
	private function reporteMov_pagosPorAsesor($conexion,$fecha,$codAsesor){
		$herramientas = new HerramientasController();
		$conexionSQL = $herramientas->conexionDinamicaBD($conexion);
		$resultado = $conexionSQL->select('SELECT fecha,recibo,usuario,cliente,monto_moneda,monto FROM mov_pagos WHERE codtipomoneda=3 AND  fecha=:fecha AND codusua=:codAsesor ORDER BY keycodigo',[$fecha,$codAsesor]);
		return $resultado;
	}

	private function totalRecaudoAsesor($conexion,$fecha){
		//consultamos los datos de los asesores		
		$herramientas = new HerramientasController();
		$conexionSQL = $herramientas->conexionDinamicaBD($conexion);
		$recaudos=$conexionSQL->select("SELECT keycodigo,codusua,usuario,fecha FROM operaciones_en_divisas_custodio WHERE fecha=:fecharecaudo GROUP BY usuario",['fecharecaudo'=>$fecha]);
		return $recaudos;
	}

	public function reporteRecaudoMovpago(){
	
		$herramientas = new HerramientasController();					
		return view('divisasCustodio.reporteRecaudoMovpago',['empresas'=>$herramientas->listarEmpresas()]);
	}

	public function buscarReporteRecaudoMovpago(Request $request){
		$fecha = $request->fecha;
		$herramientas = new HerramientasController();
		$empresa = self::datosEmpresa($request->conexion);
		$conexionSQL = $herramientas->conexionDinamicaBD($request->conexion);
		$recaudos=$conexionSQL->select("SELECT FECHA, codusua, usuario, DOLARES, ROUND(tasa,2), ROUND((DOLARES*tasa),2) AS Bolivares, codarq FROM (SELECT mov_pagos.Fecha ,mov_pagos.codusua ,mov_pagos.usuario ,SUM(mov_pagos.monto_moneda) AS DOLARES ,mov_pagos.codarq, tipo_moneda_historial_tasa.`nueva_tasa_de_cambio_en_moneda_nacional` AS tasa FROM mov_pagos, tipo_moneda_historial_tasa,(SELECT keycodigo FROM tipo_moneda WHERE is_moneda_secundaria=1 AND is_activo=1)AS tipoMoneda WHERE mov_pagos.fecha = tipo_moneda_historial_tasa.`fecha` AND mov_pagos.codtipomoneda=tipoMoneda.keycodigo AND mov_pagos.FECHA=:fecharecaudo GROUP BY mov_pagos.usuario,mov_pagos.codarq) AS movpagos",['fecharecaudo'=>$fecha]);
		return view('divisasCustodio.reporteRecaudoMovpago',['recaudos'=>$recaudos,'empresas'=>$herramientas->listarEmpresas(),'datosEmpresa'=>$empresa]);
	}
}
