<?php

namespace App\Http\Controllers\RetencionIva;

use App\Http\Controllers\Controller;
use DateTime;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use App\Models\Proveedor;
use App\Models\RetencionIvaDetalle;
use App\Models\RetencionIva;
use App\Models\Parametro;
use App\Models\Empresa;
use App\Http\Controllers\Herramientas\HerramientasController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class RetencionIvaController extends Controller
{
    public function index(){
		//metodo donde se agregan los documentos respectivos a la retencion como facturas y notas de debito o credito
		$herramientas = new HerramientasController();
    	//$conexionSQL = $herramientas->conexionDinamicaBD(session('basedata')); 
		/* $registros = DB::select("SELECT * FROM retencion_iva_detalles WHERE estatus='N'"); */
		$registros = RetencionIvaDetalle::where('comprobante','0')->where('rif_agente',session('empresaRif'))->get();
        $proveedores = Proveedor::select('rif','porcentaje_retener','nombre')->get();		
		$iva = Parametro::buscarVariable('poriva');
		$tipoOperacion = Parametro::buscarVariable('reten_iva_modo_operacion');
        return view('retencionIva.registroDocumentos',['proveedores'=>$proveedores,'registros'=>$registros,'iva'=>$iva,'empresas'=>$herramientas->listarEmpresas(),'tipoOperacion'=>$tipoOperacion]);
    }

	public function buscarFactura($keycodigo){
		return RetencionIvaDetalle::find($keycodigo);
	}

	public function editarFactura($keycodigo){
		$iva = Parametro::buscarVariable('poriva');
		$documento = self::buscarFactura($keycodigo);
		$proveedores = Proveedor::select('rif','porcentaje_retener','nombre')->get();	
		//$tipoOperacion = Parametro::buscarVariable('reten_iva_modo_operacion');
		return view("retencionIva.editarDocumentoIva",['documento'=>$documento,'proveedores'=>$proveedores,'iva'=>$iva]);
	}

	

	public function updateFactura(Request $arrayDatos){
		//actualizamos los datos de la factura enviados esto es en el registro de documentos
		$proveedor = $arrayDatos->proveedorRif;
		$detalleProveedor = explode("|",$proveedor);
		$retencionIvaDetalles = RetencionIvaDetalle::find($arrayDatos->keycodigo);
		$retencionIvaDetalles->rif_retenido  = $detalleProveedor[0];
		$retencionIvaDetalles->nom_retenido  = $detalleProveedor[2];
		$retencionIvaDetalles->fecha_docu    = $arrayDatos->fecha_docu;
		$retencionIvaDetalles->tipo_docu     = $arrayDatos->tipo_docu;
		$retencionIvaDetalles->serie         = $arrayDatos->serie;
		$retencionIvaDetalles->documento     = $arrayDatos->nfactura;		
		$retencionIvaDetalles->control_fact  = $arrayDatos->control_fact;
		$retencionIvaDetalles->tipo_trans    = $arrayDatos->tipo_trans;
		$retencionIvaDetalles->fact_afectada = $arrayDatos->fact_afectada;//cuando son notas de credito se guarda el numero de factural al que pertenece la nota
		$retencionIvaDetalles->comprasmasiva = $arrayDatos->comprasmasiva;
		$retencionIvaDetalles->sincredito    = $arrayDatos->sincredito; //exento
		$retencionIvaDetalles->base_impon    = $arrayDatos->base_impon;
		$retencionIvaDetalles->porc_alic     = $arrayDatos->porc_alic; //alicuota el porcentaje del iva
		$retencionIvaDetalles->iva           = $arrayDatos->iva;//monto iva d el afactura
		$retencionIvaDetalles->iva_retenido  = $arrayDatos->iva_retenido;//es afectado por el porcentaje del proveedor
		$retencionIvaDetalles->porc_reten    = $arrayDatos->porc_reten;//porcentaje del proveedor
		$retencionIvaDetalles->estatus		 = $arrayDatos->compra_venta;//editar ipo de operacion compra o venta
		$retencionIvaDetalles->update();
		//esto es para cerrar la ventana popup y recargar la pagina principal
	?>
		<script>
			window.opener.location.reload()
			window.close();
		</script>
	<?php	
		
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
						'estatus'=>$request->compra_venta,
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
			'estatus'=>$request->compra_venta,
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
		$retencionIvaDetalles->cod_usua      = $arrayDatos['cod_usua'];
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
		$anioMes = date('Ym', strtotime($fecha));
		$comprobante = $anioMes.$request->comprobante;
		$rif_retenido='';
		$nom_retenido='';
		$sumaIvaRetenido = 0;
		$datosFacturas = RetencionIvaDetalle::whereIn('keycodigo',$request->facturas_id)->get();//buscamos los datos de las facturas
		foreach($datosFacturas as $datoFactura){
			$rif_retenido = $datoFactura->rif_retenido;
			$nom_retenido = $datoFactura->nom_retenido;
			$sumaIvaRetenido = $sumaIvaRetenido + floatval($datoFactura->iva_retenido);
			RetencionIvaDetalle::where('keycodigo',$datoFactura->keycodigo)->update(['comprobante'=>$comprobante]);
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
		return self::mostrarComprobanteRetencionIva($comprobante,$request->firma_digital);
	}

	public function mostrarComprobanteRetencionIva($comprobante,$firma=''){
		//Generamos el comprobante de retencion en PDF
		
		$retencionIva = RetencionIva::where('comprobante',$comprobante)->first();
		$datosFacturas = RetencionIvaDetalle::where('comprobante',$comprobante)->get();//buscamos los datos de las facturas
		$datosEmpresa = Empresa::select('direccion','logo','firma')->where('rif',$retencionIva->rif_agente)->first();
		$datosModificados=array();
		
		//modificamos datos necesarios para el formato de retencion de iva 
		
		$anio = substr($retencionIva->periodo, 0, 4);
		$mes = substr($retencionIva->periodo,4,2);
		

		//nombre del mes
		setlocale(LC_TIME, 'es-ES');		
		$dateObj   = DateTime::createFromFormat('!m', $mes);
		$monthName = strftime('%B', $dateObj->getTimestamp());
		

		$datosModificados['anio']=$anio;
		$datosModificados['mes']=ucfirst($monthName);

		$pdf = new Dompdf();
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
		$pdf->setPaper('legal','landscape'); // Establecer la orientación a horizontal
        $pdf->setOptions($options);
        $html = view('retencionIva.comprobanteRetencionIva', ['retencionIva'=>$retencionIva,'datosFacturas'=>$datosFacturas,'datosEmpresa'=>$datosEmpresa,'datosModificados'=>$datosModificados,'firma'=>$firma])->render(); // Reemplaza 'pdf.example' con el nombre de tu vista

        $pdf->loadHtml($html);
        $pdf->render();

        return $pdf->stream($retencionIva->nom_agente.'-'.$retencionIva->nom_retenido.'-'.$retencionIva->comprobante.'.pdf');
			

	}

	public function listarRetencionesIva(){
		$herramientas = new HerramientasController();
		$retenciones = DB::select( "select * from retenciones_dat where comprobante<>'0' and rif_agente=:rifAgente order by keycodigo desc limit 20",['rifAgente'=>session('empresaRif')]);
		return view('retencionIva.listadoRetenciones',['retenciones'=>$retenciones,'empresas'=>$herramientas->listarEmpresas()]);
	}

	public function buscarRetencionIva(Request $request){
		$comprobante = $request->comprobante;
		$proveedor = $request->proveedor;
		$fechaDesde = $request->fecha_desde;
		$fechaHasta = $request->fecha_hasta;
		$documento = $request->documento;

		$condicion = array();
		$condicion[]="estatus='C'";
		$condicion[]="rif_agente='".session('empresaRif')."'";
		if(!empty($comprobante)){$condicion[]="comprobante =".$comprobante;}
		if(!empty($proveedor)){ $condicion[]="nom_retenido like '%".$proveedor."%'";}
		if(!empty($fechaDesde)){ $condicion[]=" fecha_docu >='".$fechaDesde."'"; }
		if(!empty($fechaHasta)){ $condicion[]=" fecha_docu <='".$fechaHasta."'";}
		if(!empty($documento)){ $condicion[] = " documento in(".$documento.")";}
		$whereClause = implode(" AND ", $condicion); //se convierte el array en un string añadiendole el AND

		$herramientas = new HerramientasController();
		$retenciones = DB::select( "select * from retenciones_dat where  ". $whereClause." order by keycodigo desc ");
		return view('retencionIva.listadoRetenciones',['retenciones'=>$retenciones,'empresas'=>$herramientas->listarEmpresas()]);
	}

	public function seleccionSucursal($rifEmpresa,$vista='retencion.iva.listar'){
		//este solo aplica para el listado de las retenciones de iva ya registradas
		//debido a que si le dan opcion buscar
        $empresa = Empresa::where('rif','=',$rifEmpresa)->first();		
        session(['empresaNombre'=>$empresa->nombre,'empresaRif'=>$empresa->rif,'codTipoMoneda'=>3,'modoPago'=>'dolares','basedata'=>$empresa->basedata]);
        return redirect()->route($vista);        
    }

	public function editarRetencionIva($comprobante){
		//abre la vista para editar la retencion y se le pasan los parametros 
		$retencionIva = self::consultarRetencionIva($comprobante);
		
		$proveedores = Proveedor::select('rif','porcentaje_retener','nombre')->get();	
		return view('retencionIva.editarRetencion',['retencionIva'=>$retencionIva,'proveedores'=>$proveedores]);
	}

	public function actualizarRetencionIva(Request $request){
		
		$proveedoRequest = explode('|',$request->proveedorRif);
		$proveedorRif = $proveedoRequest[0];
		
		$retencionIva = RetencionIva::where('comprobante',$request->comprobante)->first();
		
		//verificamos que se ha modificado el dato del proveedor para modificarlo tambien el los detallados de las facturas.
		if($retencionIva->rif_retenido <> $proveedorRif){
			$proveedor= Proveedor::where('rif',$proveedorRif)->first();
			$retencionIva->rif_retenido = $proveedor->rif;
			$retencionIva->nom_retenido = $proveedor->nombre;
			//actualizamos en detalle de la retencion
			RetencionIvaDetalle::where('comprobante',$request->comprobante)->update(['rif_retenido'=>$proveedor->rif,'nom_retenido'=>$proveedor->nombre]);
		}
		
		$retencionIva->fecha = $request->fecha;
		$retencionIva->cheque = $request->cheque;

		$retencionIva->update();
		return redirect()->route('retencion.iva.listar');
	}

	public function consultarRetencionIva($comprobante){
		return RetencionIva::where('comprobante',$comprobante)->first();
	}

	public function consultarDetallesRetencionIva($comprobante){
		return RetencionIvaDetalle::where('comprobante',$comprobante)->get();//buscamos los datos de las facturas

	}

	public function updateDetalleRetencionIva(Request $request){
		//buscamos la factura a modificar
		$retencionDetalle = RetencionIvaDetalle::where('keycodigo',$request->keycodigo)->update([
			'fecha_docu'=>$request->fecha_docu,			
			'serie'=>$request->serie,
			'documento'=>$request->nfactura,			
			'control_fact'=>$request->control_fact,
			'tipo_trans'=>$request->tipo_trans,
			'fact_afectada'=>$request->fact_afectada,
			'comprasmasiva'=>$request->comprasmasiva,
			'sincredito'=>$request->sincredito,
			'base_impon'=>$request->base_impon,
			'porc_alic'=>$request->porc_alic,
			'iva'=>$request->iva,
			'iva_retenido'=>$request->iva_retenido,
			'porc_reten'=>$request->porc_reten,
			'estatus' => $request->compra_venta]
			);
			 
		self::actualizarTotalRetener($request->comprobante);			
		
	}

	public function actualizarTotalRetener($comprobante){
		DB::update("UPDATE retenciones r JOIN retenciones_dat r_dat ON r.comprobante = r_dat.comprobante SET r.total = (SELECT SUM(iva_retenido) FROM retenciones_dat WHERE comprobante = ?)WHERE r.comprobante=? ",[$comprobante,$comprobante]);
	}

	public function vistaGenerarTxt(){
		$herramientas = new HerramientasController();
		return view('retencionIva.generarTxt',['empresas'=>$herramientas->listarEmpresas()]);
	}

	public function buscarRegistrosParaElTxt(Request $request){
		$herramientas = new HerramientasController();
		$fechaini = $request->fechaini;
		$fechafin = $request->fechafin;
		$detalleTxt = DB::select("SELECT r.rif_agente,d.comprobante,r.periodo,r.fecha AS fecha_retencion,d.fecha_docu,d.estatus,d.rif_retenido,d.nom_retenido,d.tipo_docu,d.documento,d.control_fact,d.comprasmasiva,d.base_impon,d.iva,d.iva_retenido,d.fact_afectada,d.sincredito,d.porc_alic FROM retenciones r, retenciones_dat d WHERE r.comprobante = d.comprobante AND r.estatus ='N' AND r.rif_agente =:empresaRif AND r.fecha >=:fechaini AND r.fecha <=:fechafin",["empresaRif"=>session('empresaRif'),'fechaini'=>$fechaini,'fechafin'=>$fechafin]); 
		$contenido ='';
		$tipoDocumento ='';

		foreach($detalleTxt as $registro){
			if($registro->tipo_docu=='FA'){
				$tipoDocumento='01';
			}
			if($registro->tipo_docu=='ND'){
				$tipoDocumento='02';
			}
			if($registro->tipo_docu=='NC'){
				$tipoDocumento='03';
			}
			$contenido.= $registro->rif_agente . "\t" . $registro->periodo . "\t" . $registro->fecha_retencion ."\t". $registro->estatus ."\t". $tipoDocumento ."\t". $registro->rif_retenido ."\t". $registro->documento ."\t". $registro->control_fact ."\t". $registro->comprasmasiva ."\t". $registro->base_impon ."\t". $registro->iva_retenido ."\t". $registro->fact_afectada ."\t". $registro->fact_afectada ."\t". $registro->comprobante ."\t". $registro->sincredito ."\t". $registro->porc_alic ."\t". '0' ."\n";
		}
		Storage::disk('local')->put('archivo.txt', $contenido);
		return view('retencionIva.generarTxt',['empresas'=>$herramientas->listarEmpresas(),'detalleTxt'=>$detalleTxt]);
	}

	public function descargarTxt(){
		$archivo = 'archivo.txt';
    	$rutaArchivo = storage_path('app/' . $archivo);

		if (Storage::disk('local')->exists($archivo)) {
			return response()->download($rutaArchivo, $archivo, ['Content-Type' => 'text/plain']);
		} else {
			return response()->json(['error' => 'El archivo no existe'], 404);
		}
	}
	

}
