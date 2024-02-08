<?php

namespace App\Http\Controllers\RetencionIva;

use App\Http\Controllers\Controller;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use App\Models\Proveedor;
use App\Models\RetencionIvaDetalle;
use App\Models\RetencionIva;
use App\Models\Parametro;
use App\Models\Empresa;
use App\Http\Controllers\Herramientas\HerramientasController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class RetencionIvaController extends Controller
{
    public function index(){
		$herramientas = new HerramientasController();
    	//$conexionSQL = $herramientas->conexionDinamicaBD(session('basedata')); 
		/* $registros = DB::select("SELECT * FROM retencion_iva_detalles WHERE estatus='N'"); */
		$registros = RetencionIvaDetalle::where('estatus','S')->where('rif_agente',session('empresaRif'))->get();
        $proveedores = Proveedor::all();
		$iva = Parametro::buscarVariable('poriva');
        return view('retencionIva.registroDocumentos',['proveedores'=>$proveedores,'registros'=>$registros,'iva'=>$iva,'empresas'=>$herramientas->listarEmpresas()]);
    }

    public function guardarFacturaRetencionIva(Request $request){
		
		if($request->factura_import_manual=='on'){
			self::registrarFacturasManual($request);
			
		}else{
			self::importarFacturasDelSiace($request);
		}
		
        return redirect()->route('retencion.iva.index');
    }

	public function importarFacturasDelSiace($request){
		//importamos las facturas del sistema siace o se guarda la factura manual
		
		$proveedorRif='';
        $nfactura = $request->nfactura;
        $proveedor = explode('|',$request->proveedorRif);
		$proveedorRif = $proveedor[0];
		$proveedorRifSinCaracteres = str_replace('-','',$proveedorRif);
        $fechaIni = $request->fecha_cierre_ini;
        $fechaFin = $request->fecha_cierre_fin;
		$montoIva = 0;

        //hacemos la conexion con la base de datos del siace
        $herramientas = new HerramientasController();
    	$conexionSQL = $herramientas->conexionDinamicaBD(session('basedata')); 
    	$anioAnterior =date('Y')-1;
    	//buscamos todas las facturas cerradas de la tabla CXP de la empresa seleccionada
    	//buscar solo con el numero de factura
    	if($fechaIni=='' or $fechaFin=='' and empty($proveedorRif) and !empty($nfactura)){
    		$registros = $conexionSQL->select("SELECT keycodigo, rif AS rif_retenido,nomprov AS nom_retenido,fecha AS fecha_docu,'FA' AS tipo_docu,documento,ncontrol AS control_fact,debitos AS comprasmasiva,exento AS sincredito,baseimp AS base_impon,poriva AS porc_alic,montoiva AS iva from (SELECT keycodigo, REPLACE(rif, '-', '') AS rif, nomprov, fecha, 'FA' AS tipo_docu, documento, ncontrol, debitos, exento, baseimp, poriva, montoiva,codorigen FROM cxp)facturas where codorigen=2000 and documento=:nfactura and year(fecha)>=:anioAnterior order by keycodigo",[$nfactura,$anioAnterior]);
			
		}
		//buscar solo por rango de fechas
    	if(!empty($fechaIni) or !empty($fechaFin)){	
    		$registros = $conexionSQL->select("SELECT keycodigo, rif AS rif_retenido,nomprov AS nom_retenido,fecha AS fecha_docu,'FA' AS tipo_docu,documento,ncontrol AS control_fact,debitos AS comprasmasiva,exento AS sincredito,baseimp AS base_impon,poriva AS porc_alic,montoiva AS iva FROM (SELECT keycodigo, REPLACE(rif, '-', '') AS rif, nomprov, fecha, 'FA' AS tipo_docu, documento, ncontrol, debitos, exento, baseimp, poriva, montoiva,codorigen FROM cxp)facturas WHERE codorigen=2000 and cierre>=:fechaIni and cierre<=:fechaFin and year(fecha)>=:anioAnterior",[$fechaIni,$fechaFin,$anioAnterior]);
			
		}
		//buscar con codigo de factura y rif del proveedor
    	if(empty($fechaIni) and empty($fechaFin) and !empty($proveedorRif) and !empty($nfactura)){
    		$registros = $conexionSQL->select("SELECT keycodigo, rif AS rif_retenido,nomprov AS nom_retenido,fecha AS fecha_docu,'FA' AS tipo_docu,documento,ncontrol AS control_fact,debitos AS comprasmasiva,exento AS sincredito,baseimp AS base_impon,poriva AS porc_alic,montoiva AS iva from (SELECT keycodigo, REPLACE(rif, '-', '') AS rif, nomprov, fecha, 'FA' AS tipo_docu, documento, ncontrol, debitos, exento, baseimp, poriva, montoiva,codorigen FROM cxp)facturas where codorigen=2000 and documento=:nfactura and rif=:rifProveedor and year(fecha)>=:anioAnterior order by keycodigo",
    			[$nfactura,$proveedorRifSinCaracteres,$anioAnterior]);
				
    	}
		//verificamos si el tipo de moneda base es Nacional o extranjera, esto debido a que si la moneda es extranjera 
		//se debe hacer el cambio a bolivares ya que la retencion es en bolivares.


		//Recorremos todos los registros encontrados para verificar varias opciones como 
		$bandera =''; 
		foreach($registros as $registro){
			
			//validar si encontro la factura del siace
			
			$bandera ='encontrado';
			$proveedorRifSinCaracteres = trim($registro->rif_retenido);
			//montoiva de la factura para poder restar el de las notas de credito
			//$montoIva = floatval($registro->iva);
			
			//buscamos al proveedor para optener los datos del % de retencion 100 o 75
			$proveedorDatos = DB::select("SELECT * FROM (SELECT REPLACE(rif, '-', '')rif,rif as rif_original,nombre,direccion,porcentaje_retener FROM proveedors)AS prove WHERE rif=:proveedorRifSinCaracteres",[$proveedorRifSinCaracteres]);
			
			
			foreach($proveedorDatos as $proveedorDato){
				//validamos que la factura no se haya registrado previamente con el rif que optenemos de la busqueda en proveedorDatos
				$validarDuplicado = RetencionIvaDetalle::where('rif_retenido',$proveedorDato->rif_original)->where('documento',$registro->documento)->first();
				
				if(!isset($validarDuplicado->keycodigo)){

					//verificamos si el tipo de moneda base es Nacional o extranjera, esto debido a que si la moneda es extranjera 
					//se debe hacer el cambio a bolivares ya que la retencion es en bolivares.

					if($herramientas->consultarMonedaBase()=='extranjera'){
						$tasa = valorDolarPorFecha($registro->fecha_docu);
						$iva = ($registro->iva*$tasa);
						$comprasmasiva = ($registro->comprasmasiva*$tasa);
						$sincredito = ($registro->sincredito*$tasa);
						$base_impon = ($registro->base_impon*$tasa);
					}else{
						$iva = $registro->iva;
						$comprasmasiva = $registro->comprasmasiva;
						$sincredito = $registro->sincredito;
						$base_impon = $registro->base_impon;
					}
					

					$ivaRetenido = ($iva*$proveedorDato->porcentaje_retener)/100;
					$datos = array(
						'rif_original'=>$proveedorDato->rif_original,
						'nombre'=>$proveedorDato->nombre,
						'fecha_docu'=>$registro->fecha_docu,
						'tipo_docu'=>'FA',
						'serie'=>'',
						'documento'=>$registro->documento,
						'estatus'=>'S',
						'control_fact'=>$registro->control_fact,
						'tipo_trans'=>'no aplica',
						'fact_afectada'=>'NO',
						'comprasmasiva'=>$comprasmasiva,
						'sincredito'=>$sincredito,
						'base_impon'=>$base_impon,
						'porc_alic'=>$registro->porc_alic,
						'iva'=>$iva,
						'iva_retenido'=>$ivaRetenido,
						'porc_reten'=>$proveedorDato->porcentaje_retener,
						'rif_agente'=>session('empresaRif'),
						'nom_agente'=>session('empresaNombre'),
						'fecha' => date("Y-m-d H:i:s"),
						'cod_usua' =>Auth::user()->id,
						'usuario' => Auth::user()->name,
					);
					self::guardarRegistroRetencionIvaDetalle($datos);
					$bandera ='guardado';
				}else{//si el registro de la factura ya existe
					$bandera ='duplicado';
					
					
				}//fin validar si ya se registro la factura y no es duplicado
			}//fin foreach proveedorDatos
						
		}//fin foreach registros
		
		switch($bandera){
			case'guardado':
				\Session::flash('message', 'Factura registrada con exito');
				\Session::flash('alert','alert-success');
			break;
			case'duplicado'	:
				\Session::flash('message', 'La Factura  no se puede guardar porque ya fue registrada anteriormente');
				\Session::flash('alert','alert-danger');
			break;
			case'':
				\Session::flash('message', 'Factura no encontrada');
				\Session::flash('alert','alert-warning');
			break;	
		}
		
	}// fin del metodo importarFacturasDelSiace

	public function registrarFacturasManual($request){
		//rif del proveedor y porcentaje de retencion
		$proveedor = explode('|',$request->proveedorRif);
		$proveedorRif = $proveedor[0];

		$proveedorDato = Proveedor::where('rif',$proveedorRif)->first();
		
		//registro de facturas Manualmente
		$datos = array(
			'rif_original'=>$proveedorDato->rif,
			'nombre'=>$proveedorDato->nombre,
			'fecha_docu'=>$request->fecha_docu,
			'tipo_docu'=>$request->tipo_docu,
			'serie'=>$request->serie,
			'documento'=>$request->nfactura,
			'estatus'=>'S',
			'control_fact'=>$request->control_fact,
			'tipo_trans'=>$request->tipo_trans,
			'fact_afectada'=>$request->fact_afectada,
			'comprasmasiva'=>$request->comprasmasiva,
			'sincredito'=>$request->sincredito,
			'base_impon'=>$request->base_impon,
			'porc_alic'=>$request->porc_alic,
			'iva'=>$request->iva,
			'iva_retenido'=>$request->iva_retenido,
			'porc_reten'=>$proveedorDato->porcentaje_retener,
			'rif_agente'=>session('empresaRif'),
			'nom_agente'=>session('empresaNombre'),
			'fecha' => date("Y-m-d H:i:s"),
			'cod_usua' =>Auth::user()->id,
			'usuario' => Auth::user()->name,
		);
		self::guardarRegistroRetencionIvaDetalle($datos);
		return $mensaje=array('tipo'=>'alert-success','texto'=>'Factura registrada con exito');
	}

	public function guardarRegistroRetencionIvaDetalle($arrayDatos){
		if($arrayDatos['sincredito']==''){
			$sincredito =0;
		}else{
			$sincredito = $arrayDatos['sincredito'];
		}
		if($arrayDatos['fact_afectada'] ==''){
			$fact_afectada ='';
		}else{
			$fact_afectada = $arrayDatos['fact_afectada'];
		}
		$retencionIvaDetalles =	new RetencionIvaDetalle();
		$retencionIvaDetalles->rif_retenido  = $arrayDatos['rif_original'];
		$retencionIvaDetalles->nom_retenido  = $arrayDatos['nombre'];
		$retencionIvaDetalles->fecha_docu    = $arrayDatos['fecha_docu'];
		$retencionIvaDetalles->tipo_docu     = $arrayDatos['tipo_docu'];
		$retencionIvaDetalles->serie         = $arrayDatos['serie'];
		$retencionIvaDetalles->documento     = $arrayDatos['documento'];
		$retencionIvaDetalles->estatus       = $arrayDatos['estatus'];
		$retencionIvaDetalles->control_fact  = $arrayDatos['control_fact'];
		$retencionIvaDetalles->tipo_trans    = $arrayDatos['tipo_trans'];
		$retencionIvaDetalles->fact_afectada = $fact_afectada;//cuando son notas de credito se guarda el numero de factural al que pertenece la nota
		$retencionIvaDetalles->comprasmasiva = $arrayDatos['comprasmasiva'];
		$retencionIvaDetalles->sincredito    = $sincredito; //exento
		$retencionIvaDetalles->base_impon    = $arrayDatos['base_impon'];
		$retencionIvaDetalles->porc_alic     = $arrayDatos['porc_alic']; //alicuota el porcentaje del iva
		$retencionIvaDetalles->iva           = $arrayDatos['iva'];//monto iva d el afactura
		$retencionIvaDetalles->iva_retenido  = $arrayDatos['iva_retenido'];//es afectado por el porcentaje del proveedor
		$retencionIvaDetalles->porc_reten    = $arrayDatos['porc_reten'];//porcentaje del proveedor
		$retencionIvaDetalles->rif_agente    = $arrayDatos['rif_agente'];//rif empresa
		$retencionIvaDetalles->nom_agente    = $arrayDatos['nom_agente']; //nombre empresa
		$retencionIvaDetalles->fecha         = $arrayDatos['fecha'];
		$retencionIvaDetalles->cod_usua       = $arrayDatos['cod_usua'];
		$retencionIvaDetalles->usuario       = $arrayDatos['usuario'];
		$retencionIvaDetalles->save();

	}

	public function eliminarFactura($id){
		$factura = RetencionIvaDetalle::where('keycodigo',$id);
		$factura->delete();
		return redirect()->route('retencion.iva.index');
	}

	public function generarRetencionIva(Request $request){
		//este metodo toma las facturas seleccionadas y genera la retencion de iva
		$idFacturasPorRetener = $request->facturasPorRetener;
		$proveedorRif ='';
		$proveedorNombre ='';
		$rifProveedorComparar=array();
		$valor =0;		

		//buscamos todas las facturas seleccionadas
		$datosFacturas = RetencionIvaDetalle::whereIn('keycodigo',$idFacturasPorRetener)->get();
		//recorremos los datos de las facturas para optener los datos del porveedor
		foreach($datosFacturas as $datos){
			$proveedorRif = $datos->rif_retenido;
			$proveedorNombre = $datos->nom_retenido;
			$rifProveedorComparar[] = $proveedorRif;
		}

		//verificamos que las facturas seleccionadas pertenezcan al mismo vendedo
		$verificarProveedores = array_unique($rifProveedorComparar);
		if(count($verificarProveedores)== 1){
			$variable = 'contador_reten_iva_'.session('empresaRif');
			$valor = Parametro::buscarVariable($variable);		
			$contador=str_pad($valor, 8, "0", STR_PAD_LEFT);
			
			//BUSCAMOS EL ULTIMO COMPROBANTE DE RETENCION
			$ultimoComprobante = DB::select("SELECT comprobante FROM retenciones WHERE rif_agente=:empresaRif ORDER BY keycodigo DESC LIMIT 1",['empresaRif'=>session('empresaRif')]);
			return view('retencionIva.registroRetencion',['datosFacturas'=>$datosFacturas,'contador'=>$contador,'rif_agente'=>$proveedorRif,'nom_agente'=>$proveedorNombre,'ultimoComprobante'=>$ultimoComprobante]);
			
		}else{
			\Session::flash('message', 'La retencion no procede porque las facturas seleccionadas son de distintos proveedores, estas deben ser del mismo proveedor para continuar');
			\Session::flash('alert','alert-warning');
			return redirect()->route('retencion.iva.index');
		}

		
	}

	public function guardarComprobanteRetencionIva(Request $request){
		//una vez ya seleccionada la factura nos aparece un formulario donde seleccionamos la fecha la transferencia y guardamos eso datos en este metodo 
		//esto genera guarda el numero del comprobanate de retencion y actualiza los estatus de los registros
		$fecha = $request->fecha;
		$anioMes = $fecha = date('Ym', strtotime($fecha));
		$comprobante = $anioMes.$request->comprobante;
		$rif_retenido='';
		$nom_retenido='';
		$sumaIvaRetenido = 0;
		$datosFacturas = RetencionIvaDetalle::whereIn('keycodigo',$request->facturas_id)->get();//buscamos los datos de las facturas
		foreach($datosFacturas as $datoFactura){
			$rif_retenido = $datoFactura->rif_retenido;
			$nom_retenido = $datoFactura->nom_retenido;
			$sumaIvaRetenido = $sumaIvaRetenido + floatval($datoFactura->iva_retenido);
			RetencionIvaDetalle::where('keycodigo',$datoFactura->keycodigo)->update(['comprobante'=>$comprobante,'estatus'=>'C']);
		}
		$retencionIva = new RetencionIva();
		$retencionIva->periodo = $anioMes;
		$retencionIva->comprobante = $comprobante;
		$retencionIva->fecha = $fecha;
		$retencionIva->rif_agente = session('empresaRif');
		$retencionIva->nom_agente = session('empresaNombre');
		$retencionIva->rif_retenido = $rif_retenido;
		$retencionIva->nom_retenido = $nom_retenido;
		$retencionIva->cheque = $request->cheque;
		$retencionIva->total = $sumaIvaRetenido;
		$retencionIva->estatus='N';
		$retencionIva->cod_usua = Auth::user()->id;
		$retencionIva->usuario = Auth::user()->name;
		$retencionIva->save();
		$variable = 'contador_reten_iva_'.session('empresaRif');
		$valor = Parametro::buscarVariable($variable);
		Parametro::actualizarVariable($variable,$valor+1);
		return self::mostrarComprobanteRetencionIva($comprobante);
	}

	public function mostrarComprobanteRetencionIva($comprobante){
		//Generamos el comprobante de retencion en PDF
		
		$retencionIva = RetencionIva::where('comprobante',$comprobante)->first();
		$datosFacturas = RetencionIvaDetalle::where('comprobante',$comprobante)->get();//buscamos los datos de las facturas
		$datosEmpresa = Empresa::select('direccion','logo')->where('rif',$retencionIva->rif_agente)->first();
		$pdf = new Dompdf();
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
		$pdf->setPaper('letter', 'landscape'); // Establecer la orientación a horizontal
        $pdf->setOptions($options);
        $html = view('retencionIva.comprobanteRetencionIva', ['retencionIva'=>$retencionIva,'datosFacturas'=>$datosFacturas,'datosEmpresa'=>$datosEmpresa])->render(); // Reemplaza 'pdf.example' con el nombre de tu vista

        $pdf->loadHtml($html);
        $pdf->render();

        return $pdf->stream('documento.pdf');
			

	}

	public function listarRetencionesIva(){
		$retenciones = DB::select( "select * from retenciones_dat where estatus='C'");
		return view('retencionIva.listadoRetenciones',['retenciones'=>$retenciones]);
	}


}
