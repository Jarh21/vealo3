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
use App\Exports\RetencionIvaContableExcel;
use Maatwebsite\Excel\Facades\Excel;

class RetencionIvaController extends Controller
{
	public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::check()) {
                return redirect()->route('inicioSesion');
            }

            return $next($request);
        });
    }
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
						$tasa = HerramientasController::valorDolarPorFecha($registro->fecha_docu);
						$iva = ($registro->iva*$tasa);
						$comprasmasiva = ($registro->comprasmasiva*$tasa);
						$sincredito = ($registro->sincredito*$tasa);
						$base_impon = ($registro->base_impon*$tasa);
						echo"Moneda base Extranjera";
					}else{
						echo"Moneda base Nacional";
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
						'tipo_trans'=>'',
						'fact_afectada'=>'',
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
				if (!\Session::has('message')) {
				\Session::flash('message', 'Factura registrada con exito');
				\Session::flash('alert','alert-success');
				}
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
		session(['documentos_seleccionados_iva'=>$idFacturasPorRetener]);//guardamos en la variable session por si regresamos a la vista anterior queden tildados los registros
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
		return self::mostrarComprobanteRetencionIva($comprobante,session('empresaRif'),$request->firma_digital);
	}

	public function mostrarComprobanteRetencionIva($comprobante,$empresaRif,$firma=''){
		//Generamos el comprobante de retencion en PDF
		
		$retencionIva = RetencionIva::where('comprobante',$comprobante)->where('rif_agente',$empresaRif)->first();
		$datosFacturas = RetencionIvaDetalle::where('comprobante',$comprobante)->where('rif_agente',$empresaRif)->get();//buscamos los datos de las facturas
		$datosEmpresa = Empresa::select('direccion','logo','firma')->where('rif',$retencionIva->rif_agente)->first();
		$datosModificados=array();
		
		//modificamos datos necesarios para el formato de retencion de iva 		
		$anio = substr($retencionIva->periodo, 0, 4);
		$mes = substr($retencionIva->periodo,4,2);
		

		//nombre del mes
		
		if($mes == '01'){
		$monthName="ENERO";				
		}
			
		if($mes == '02'){
		$monthName="FEBRERO";		
		}
					
		if($mes == '03'){
		$monthName="MARZO";				
		}
			
		if($mes == '04'){
		$monthName="ABRIL";				
		}
			
		if($mes == '05'){
		$monthName="MAYO";				
		}
			
		if($mes == '06'){
		$monthName="JUNIO";				
		}
			
		if($mes == '07'){
		$monthName="JULIO";				
		}
			
		if($mes == '08'){
		$monthName="AGOSTO";			
		}
				
		if($mes == '09'){
		$monthName="SEPTIEMBRE";
		}				
			
		if($mes == '10'){
		$monthName="OCTUBRE";		
		}
					
		if($mes == '11'){
		$monthName="NOVIEMBRE";
		}
							
		if($mes == '12'){
		$monthName="DICIEMBRE";
		}
										

		$datosModificados['anio']=$anio;
		$datosModificados['mes']=$monthName;

		$pdf = new Dompdf();
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
		$pdf->setPaper('letter','landscape'); // Establecer la orientación a horizontal
        $pdf->setOptions($options);
        $html = view('retencionIva.comprobanteRetencionIva', ['retencionIva'=>$retencionIva,'datosFacturas'=>$datosFacturas,'datosEmpresa'=>$datosEmpresa,'datosModificados'=>$datosModificados,'firma'=>$firma])->render(); // Reemplaza 'pdf.example' con el nombre de tu vista

        $pdf->loadHtml($html);
        $pdf->render();


		//eliminar los archivos de la carpeta storage
		// Verificar si el directorio existe
		
		$rutaDirectorio='pdf/';
		$archivos = Storage::files($rutaDirectorio);

		if (!empty($archivos)) {
			foreach ($archivos as $archivo) {
				// Eliminar cada archivo individualmente
				Storage::delete($archivo);
			}			
		}

		///codigo de chat GPT3
		//guardamos el documento en storage de laravel
		$nombreArchivo = $retencionIva->nom_agente.'-'.$retencionIva->nom_retenido.'-'.$retencionIva->comprobante.'.pdf';
		$nombreArchivo = str_replace(' ','_',$nombreArchivo);
		$rutaArchivo = 'pdf/' . $nombreArchivo; // Ruta donde se guardará el archivo dentro de la carpeta storage

		Storage::put($rutaArchivo, $pdf->output()); // Guardar el archivo PDF en la carpeta storage
		//retornamos el archivo pdf
		return response()->stream(function () use ($pdf) {
			echo $pdf->output();
		}, 200, [
			'Content-Type' => 'application/pdf',
			'Content-Disposition' => 'inline; filename="' . $nombreArchivo . '"'
		]);//fin codigo GPT3

        //return $pdf->stream($retencionIva->nom_agente.'-'.$retencionIva->nom_retenido.'-'.$retencionIva->documento.'.pdf');
			

	}

	public function listarRetencionesIva(){
		$herramientas = new HerramientasController();
		$cantidad=0;
		$limite = 100;
		if(!empty(session('limite'))){
			$limite = (session('limite'));
		}
		if(empty(session('comprobanteIva')) and empty(session('proveedorIva')) and empty(session('fecha_desdeIva')) and empty(session('fecha_hastaIva')) and empty(session('documentoIva'))){
			$retenciones_dat = DB::select( "select GROUP_CONCAT(d.documento SEPARATOR ',')as documentos,  d.*,r.estatus as estatus_retencion, r.fecha,r.total from retenciones_dat d,retenciones r where  d.comprobante<>'0' and d.rif_agente=:rifAgente and d.comprobante = r.comprobante and d.rif_agente = r.rif_agente group by d.comprobante ORDER BY r.keycodigo DESC limit 100",['rifAgente'=>session('empresaRif')]);
			
		}else{
			
			$condicion = array();
			$condicion[]="retenciones_dat.estatus='C'";
			$condicion[]="retenciones_dat.rif_agente='".session('empresaRif')."'";
			$condicion[]="retenciones_dat.comprobante <> '0.00'";
			$condicion[]="retenciones_dat.comprobante = retenciones.comprobante";
			$condicion[]="retenciones_dat.rif_agente = retenciones.rif_agente";
			if(!empty(session('comprobanteIva'))){$condicion[]="retenciones_dat.comprobante =".session('comprobanteIva');}
			if(!empty(session('proveedorIva'))){ $condicion[]="retenciones_dat.nom_retenido like '%".session('proveedorIva')."%'";}
			if(!empty(session('fecha_desdeIva'))){ $condicion[]=" retenciones_dat.fecha_docu >='".session('fecha_desdeIva')."'"; }
			if(!empty(session('fecha_hastaIva'))){ $condicion[]=" retenciones_dat.fecha_docu <='".session('fecha_hastaIva')."'";}
			if(!empty(session('documentoIva'))){ $condicion[] = " retenciones_dat.documento in(".session('documentoIva').")";}
			$whereClause = implode(" AND ", $condicion); //se convierte el array en un string añadiendole el AND
			
			$retenciones_dat = DB::select( "select GROUP_CONCAT(retenciones_dat.documento SEPARATOR ',')as documentos,  retenciones_dat.*,retenciones.estatus as estatus_retencion, retenciones.fecha,retenciones.total from retenciones_dat,retenciones where  ". $whereClause." group by retenciones_dat.comprobante ORDER BY retenciones.keycodigo DESC limit ".$limite);	
			$cantidad = count($retenciones_dat);
		}	
		
		return view('retencionIva.listadoRetenciones',['retenciones_dat'=>$retenciones_dat,'empresas'=>$herramientas->listarEmpresas(),'cantidad'=>$cantidad]);
	}

	public function buscarRetencionIva(Request $request){
		$comprobante = $request->comprobante;
		$proveedor = $request->proveedor;
		$fechaDesde = $request->fecha_desde;
		$fechaHasta = $request->fecha_hasta;
		$documento = $request->documento;
		$limite = $request->limite;
		 // Guardar los valores en variables de sesión
		if(!empty($comprobante)) { 
			session(['comprobanteIva' => $comprobante]);
		}else{
			session(['comprobanteIva' => '']);
		}
		if(!empty($proveedor)) { 
			session(['proveedorIva' => $proveedor]);
		}else{
			session(['proveedorIva' => '']);
		}
		if(!empty($fechaDesde)) { 
			session(['fecha_desdeIva' => $fechaDesde]);
		}else{
			session(['fecha_desdeIva' => '']);
		}
		if(!empty($fechaHasta)) { 
			session(['fecha_hastaIva' => $fechaHasta]);
		}else{
			session(['fecha_hastaIva' => '']);
		}
		if(!empty($documento)) { 
			session(['documentoIva' => $documento]);
		}else{
			session(['documentoIva' => '']);
		}
		if(!empty($limite)) { 
			session(['limite' => $limite]);
		}else{
			session(['limite' => '']);
		}
		$condicion = array();
		$condicion[]="retenciones_dat.estatus='C'";
		$condicion[]="retenciones_dat.rif_agente='".session('empresaRif')."'";
		$condicion[]="retenciones_dat.comprobante <> '0.00'";
		$condicion[]="retenciones_dat.comprobante = retenciones.comprobante";
		$condicion[]="retenciones_dat.rif_agente = retenciones.rif_agente";
		if(!empty($comprobante)){$condicion[]="retenciones_dat.comprobante =".$comprobante;}
		if(!empty($proveedor)){ $condicion[]="retenciones_dat.nom_retenido like '%".$proveedor."%'";}
		if(!empty($fechaDesde)){ $condicion[]=" retenciones.fecha >='".$fechaDesde."'"; }
		if(!empty($fechaHasta)){ $condicion[]=" retenciones.fecha <='".$fechaHasta."'";}
		if(!empty($documento)){ $condicion[] = " retenciones_dat.documento in(".$documento.")";}
		$whereClause = implode(" AND ", $condicion); //se convierte el array en un string añadiendole el AND

		$herramientas = new HerramientasController();
		$retenciones_dat = DB::select( "select GROUP_CONCAT(retenciones_dat.documento SEPARATOR ',')as documentos,retenciones_dat.*,retenciones.estatus as estatus_retencion,retenciones.fecha,retenciones.total from retenciones_dat,retenciones where  ". $whereClause." group by retenciones_dat.comprobante ORDER BY retenciones.keycodigo DESC limit ".$limite);			
		$cantidad = count($retenciones_dat);
		return view('retencionIva.listadoRetenciones',['retenciones_dat'=>$retenciones_dat,'empresas'=>$herramientas->listarEmpresas(),'cantidad'=>$cantidad]);
	}

	public function seleccionSucursal($rifEmpresa,$vista='retencion.iva.listar'){
		//este solo aplica para el listado de las retenciones de iva ya registradas
		//debido a que si le dan opcion buscar
        $empresa = Empresa::where('rif','=',$rifEmpresa)->first();		
        session(['empresaNombre'=>$empresa->nombre,'empresaRif'=>$empresa->rif,'codTipoMoneda'=>3,'modoPago'=>'dolares','basedata'=>$empresa->basedata]);
        return redirect()->route($vista);        
    }

	public function editarRetencionIva($comprobante,$empresaRif=''){
		if($empresaRif==''){
			$empresaRif= session('empresaRif');
		}
		//abre la vista para editar la retencion y se le pasan los parametros 
		$retencionIva = self::consultarRetencionIva($comprobante,$empresaRif);
		$ultimaRetencion =  RetencionIva::where('rif_agente', $empresaRif)//esto es para comparar si la retencion que estoy editando es la ultima ya que la ultima se puede eliminar
		->orderBy('keycodigo', 'desc')
		->select('comprobante')
		->first();
		
		$proveedores = Proveedor::select('rif','porcentaje_retener','nombre')->get();	
		return view('retencionIva.editarRetencion',['retencionIva'=>$retencionIva,'proveedores'=>$proveedores,'ultimaRetencion'=>$ultimaRetencion]);
	}

	public function actualizarRetencionIva(Request $request){
		
		$proveedoRequest = explode('|',$request->proveedorRif);
		$proveedorRif = $proveedoRequest[0];
		
		$retencionIva = RetencionIva::where('comprobante',$request->comprobante)->where('rif_agente',session('empresaRif'))->first();
		
		//verificamos que se ha modificado el dato del proveedor para modificarlo tambien el los detallados de las facturas.
		if($retencionIva->rif_retenido <> $proveedorRif){
			$proveedor= Proveedor::where('rif',$proveedorRif)->first();
			$retencionIva->rif_retenido = $proveedor->rif;
			$retencionIva->nom_retenido = $proveedor->nombre;
			//actualizamos en detalle de la retencion
			RetencionIvaDetalle::where('comprobante',$request->comprobante)->where('rif_agente',session('empresaRif'))->update(['rif_retenido'=>$proveedor->rif,'nom_retenido'=>$proveedor->nombre]);
		}
		
		$retencionIva->fecha = $request->fecha;
		$retencionIva->cheque = $request->cheque;

		$retencionIva->update();
		return redirect()->route('retencion.iva.listar');
	}

	public function consultarRetencionIva($comprobante,$empresaRif = ''){
		if(empty($empresaRif)){
			$empresaRif = session('empresaRif');
		}
		return RetencionIva::where('comprobante',$comprobante)->where('rif_agente',$empresaRif)->first();
	}

	public function consultarDetallesRetencionIva($comprobante,$empresaRif = ''){
		if(empty($empresaRif)){
			$empresaRif = session('empresaRif');
		}
		return RetencionIvaDetalle::where('comprobante',$comprobante)->where('rif_agente',$empresaRif)->get();//buscamos los datos de las facturas

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
		$empresaRif = session('empresaRif');
		DB::update("UPDATE retenciones r JOIN retenciones_dat r_dat ON r.comprobante = r_dat.comprobante SET r.total = (SELECT SUM(iva_retenido) FROM retenciones_dat WHERE comprobante = ? and rif_agente = ?)WHERE r.comprobante=? and r.rif_agente = ?",[$comprobante,$empresaRif,$comprobante,$empresaRif]);
	}

	public function vistaGenerarTxt(){
		$herramientas = new HerramientasController();
		return view('retencionIva.generarTxt',['empresas'=>$herramientas->listarEmpresas()]);
	}

	public function buscarRegistrosParaElTxt(Request $request){
		//generamos el archivo txt
		$herramientas = new HerramientasController();
		$fechaini = $request->fechaini;
		$fechafin = $request->fechafin;
		$empresaRif =session('empresaRif');
		$nomCortoEmpresa = Empresa::where('rif',$empresaRif)->select('nom_corto')->first();
		$detalleTxt = DB::select("
		SELECT r.rif_agente,r.fecha,d.comprobante,r.periodo,r.estatus as estatus_retencion,d.fecha_docu,d.estatus,d.rif_retenido,d.nom_retenido,d.tipo_docu,d.documento,d.control_fact,d.comprasmasiva,d.base_impon,d.iva,d.iva_retenido,d.fact_afectada,d.sincredito,d.porc_alic,d.porc_reten 
		FROM 
		retenciones_dat d
		INNER JOIN retenciones r ON d.comprobante = r.comprobante
		WHERE
		(
		(d.tipo_docu='FA' AND (r.fecha BETWEEN :fechaini_fa AND :fechafin_fa) AND r.estatus in('N'))
		OR 
		(d.tipo_docu<>'FA' AND (r.fecha BETWEEN :fechaini_nc AND :fechafin_nc) AND r.estatus='N' AND (d.`fact_afectada` IN(SELECT det.documento FROM retenciones_dat det,retenciones ret WHERE ret.comprobante = det.comprobante AND (ret.fecha BETWEEN :fechaini_afec AND :fechafin_afec) AND ret.rif_agente= :empresaRif_afec)))
		)  
		AND d.rif_agente= :empresaRif
		",['fechaini_fa'=>$fechaini,'fechafin_fa'=>$fechafin,'fechaini_nc'=>$fechaini,'fechafin_nc'=>$fechafin,'fechaini_afec'=>$fechaini,'fechafin_afec'=>$fechafin,'empresaRif_afec'=>$empresaRif,'empresaRif'=>$empresaRif]); 
		$contenido ='';
		$contenidoContable ='';
		$tipoDocumento ='';
		$fact_afectada=0.00;
		$rif_retenido='';	
		$rif_agente ='';	
		$periodo ='';
		$newNombre ='';
		$quincena ='';
		$fecha='';
		foreach($detalleTxt as $registro){
			$periodo = $registro->periodo;
			$fecha = date("d/m/Y", strtotime($registro->fecha));
			if($registro->tipo_docu=='FA'){
				$tipoDocumento='01';
			}
			if($registro->tipo_docu=='ND'){
				$tipoDocumento='02';
			}
			if($registro->tipo_docu=='NC'){
				$tipoDocumento='03';
			}
			if($registro->estatus_retencion=='N'){
				if(empty($registro->fact_afectada)){
					$fact_afectada=0.00;
				}else{
					$fact_afectada=$registro->fact_afectada;
				}
				$rif_retenido = str_replace("-","",$registro->rif_retenido);
				$rif_agente = str_replace("-","",$registro->rif_agente);
				$contenido.= $rif_agente . "\t" . $registro->periodo . "\t" . $registro->fecha_docu ."\t". $registro->estatus ."\t". $tipoDocumento ."\t". $rif_retenido ."\t". $registro->documento ."\t". $registro->control_fact ."\t". $registro->comprasmasiva ."\t". $registro->base_impon ."\t". $registro->iva_retenido ."\t". $fact_afectada ."\t". $registro->comprobante ."\t". $registro->sincredito ."\t". $registro->porc_alic ."\t". '0' ."\n";
				$contenidoContable.= $fecha ."\t". $registro->nom_retenido ."\t". $rif_retenido ."\t". $registro->iva_retenido ."\t". $registro->porc_reten ."\t". $registro->comprobante ."\t".$fecha."\n";
			}else{
				$contenido.= $rif_agente . "\t" . $registro->periodo . "\t" . $registro->fecha_docu ."\t". $registro->estatus ."\t". $tipoDocumento ."\t". $rif_retenido ."\t". $registro->documento ."\t". $registro->control_fact ."\t". 0 ."\t". 0 ."\t". 0 ."\t". 0 ."\t". $registro->comprobante ."\t". 0 ."\t". 0 ."\t". '0' ."\n";
			
			}	

		}
		Storage::disk('local')->put('SENIAT_'.$nomCortoEmpresa->nom_corto.'.txt', $contenido);
		Storage::disk('local')->put('CONTABLE_'.$nomCortoEmpresa->nom_corto.'.txt', $contenidoContable);
		//preparar el nombre del archivo
		$anio = substr($periodo, 0, 4);
		$mes = substr($periodo,4,2);
		
		//nombre del mes

			if($mes == '01'){
			$monthName="Ene";				
			}
				
			if($mes == '02'){
			$monthName="Feb";		
			}
						
			if($mes == '03'){
			$monthName="Mar";				
			}
				
			if($mes == '04'){
			$monthName="Abr";				
			}
				
			if($mes == '05'){
			$monthName="May";				
			}
				
			if($mes == '06'){
			$monthName="Jun";				
			}
				
			if($mes == '07'){
			$monthName="Jul";				
			}
				
			if($mes == '08'){
			$monthName="Ago";			
			}
					
			if($mes == '09'){
			$monthName="Sep";
			}				
				
			if($mes == '10'){
			$monthName="Oct";		
			}
						
			if($mes == '11'){
			$monthName="Nov";
			}
								
			if($mes == '12'){
			$monthName="Dic";
			}

			if($fechafin >= $anio.'-'.$mes.'-16'){
				$quincena = '2QC';
			}else{
				$quincena = '1QC';
			}
			$newNombre=$nomCortoEmpresa->nom_corto.'_'.$quincena.'_'.$monthName.$anio.'.txt';

		return view('retencionIva.generarTxt',['empresas'=>$herramientas->listarEmpresas(),'detalleTxt'=>$detalleTxt,'fechaini'=>$fechaini,'fechafin'=>$fechafin,'archivo'=>$nomCortoEmpresa->nom_corto.'.txt','newNombre'=>$newNombre]);
	}

	public function descargarTxt($archivo,$newNombre){
		
		
    	$rutaArchivo = storage_path('app/' . $archivo);

		if (Storage::disk('local')->exists($archivo)) {
			return response()->download($rutaArchivo, $newNombre, ['Content-Type' => 'text/plain']);
		} else {
			return response()->json(['error' => 'El archivo no existe'], 404);
		}
	}

	public function anularComprobante($comprobante,$empresaRif=''){
		///anulamos el comprobante de retencion
		if(empty($empresaRif)){
			$empresaRif = session('empresaRif');
		}
		
		RetencionIva::where('comprobante',$comprobante)->where('rif_agente',$empresaRif)->update(['estatus'=>'A','total'=>0]);
		return redirect()->route("retencion.iva.listar");
	}

	public function eliminarComprobante($comprobante,$empresaRif=''){
		//eliminamos el comprobante de rentcion 
		if(empty($empresaRif)){
			$empresaRif = session('empresaRif');
		}
		
		RetencionIva::where('comprobante',$comprobante)->where('rif_agente',$empresaRif)->delete();
		RetencionIvaDetalle::where('comprobante',$comprobante)->where('rif_agente',$empresaRif)->delete();
		$contador = Parametro::buscarVariable('contador_reten_iva_'.$empresaRif);//buscamos el valor del contador
		$contador = $contador - 1; //le restamos 1 ya que se elimino el anterior y pode asignar el mismo contador a la nueva retencion
		Parametro::actualizarVariable('contador_reten_iva_'.$empresaRif,$contador);//asignamos el valor -1 al contador con esto no hay saltos al eliminar
		return redirect()->route("retencion.iva.listar");
	}
	

}
