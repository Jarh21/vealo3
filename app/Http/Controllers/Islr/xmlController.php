<?php

namespace App\Http\Controllers\Islr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\Islr;
use App\Http\Controllers\islrController;
use App\Models\ReporteXml;
use App\Models\EncabezadoXml;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\Herramientas\HerramientasController;
use Illuminate\Support\Facades\DB;
use XMLWriter; /*para usar la clase nativa de xml en php*/

class xmlController extends Controller
{
    public function buscarFecha($fechaini,$fechafin,$empresa){
		$islr = DB::select("
			select
				i.id,
				i.id as islr_y_rrhh_id,
				'islr' as islr_o_rrhh, 
				i.nControl,
				i_d.nFactura,
				i_d.nControl as nControlFactura,
				e.nombre as empresa,
				e.direccion as e_direccion,
				e.nom_corto,
				i.fecha,
				e.rif as e_rif,
				p.nombre proveedor,
				p.id as p_id,
				p.rif as p_rif,
				p.direccion as p_direccion,
				p.tipo_contribuyente,
				i.proveedor_codfiscal,
				i_d.concepto,
				i.n_egreso_cheque,
				i_d.monto,
				i_d.porcentaje_retencion,
				i_d.total_retener as total_retener_factura,
				i.serie,
				i.sustraendo,
				i.total_retener,
				i.total_letras

			from 
				islrs i,
				proveedors p,
				empresas e,
				islr_detalles i_d
			where
				i.id = i_d.islr_id and 
				i.empresa_rif=e.rif and 
				i.proveedor_rif=p.rif and 
				(i.fecha>=:fechaini and i.fecha<=:fechafin) and 
				e.rif=:empresaRif order by p.rif",
				['fechaini'=>$fechaini,'fechafin'=>$fechafin,'empresaRif'=>$empresa]);
		return $islr;
	}
    	////////////////////////////////////// XML  /////////////
	//////////////////////////////////////////////////////
	public function xml(){
		//vista principal	
		$herramientas = new HerramientasController();		
		//$empresas = Empresa::all();
		return view('islr.xml.xmlcrear',['empresas'=>$herramientas->listarEmpresas()]);
			
	}


	public function buscarEmpleados($empresaRif,$ultimaFechaMes){ //////////////////////////<------------
		//funcion que obtiene los empreados segun la empresa para el xml

		$rrhh = DB::select("SELECT
			  rrhhs.id,
			  rrhhs.id AS islr_y_rrhh_id,
			  'rrhh' AS islr_o_rrhh,
			  :ultimaFechaMes AS fecha,
			  rrhhs.rif AS p_rif,
			  rrhhs.sueldo_base AS monto,
			  0 AS total_retener_factura,
			  rrhhs.nombres AS proveedor,			  
			  '0'AS nFactura,
			  '0'AS nControlFactura,
			  rrhhs.empresa_rif AS e_rif,
			  empresas.nombre AS empresa,  
			  '001'AS proveedor_codfiscal,
			  0 AS porcentaje_retencion
			FROM
			  empresas,
			  rrhhs			  
			WHERE rrhhs.empresa_rif=empresas.rif AND rrhhs.empresa_rif=:empresaRif AND rrhhs.activo=1

			UNION all

			SELECT
			  empleado_declarantes.id,
			  empleado_declarantes.id as islr_y_rrhh_id,
			  'gerente' as islr_o_rrhh,
			  :ultimaFechaMes2 as fecha,
			  empleado_declarantes.rif AS p_rif,
			  empleado_declarantes.sueldo_base AS monto,
			  (empleado_declarantes.sueldo_base*porcentaje_retencion)/100 AS total_retener_factura,
			  empleado_declarantes.nombre AS proveedor,
			  0 AS nFactura,
			  0 AS nControlFactura,
			  empleado_declarantes.e_rif,
			  empleado_declarantes.empresa,
			  codigo AS proveedor_codfiscal,
			  porcentaje_retencion
			FROM
			  empleado_declarantes,
			  contribuyentes
			WHERE empleado_declarantes.contribuyente_id = contribuyentes.id AND e_rif=:eRif
			",['ultimaFechaMes'=>$ultimaFechaMes,'empresaRif'=>$empresaRif,'ultimaFechaMes2'=>$ultimaFechaMes,'eRif'=>$empresaRif]);
		return $rrhh;
	}


	public function xmlCrear(Request $request){
		//vista secundaria donde recibe los dato como espresa y fecha
		//obtenemos los meses de las dos fecha y si hay diferencias envia un error
		$empresaRif = session('empresaRif');
		$fecha1 = date('m',strtotime($request->get('fechaIni')));
		$fecha2 = date('m',strtotime($request->get('fechaFin')));
		$month    = date('Y-m',strtotime($request->get('fechaFin')));
		$aux      = date('Y-m-d', strtotime("{$month} + 1 month"));
		$ultimaFechaMes = date('Y-m-d', strtotime("{$aux} - 1 day"));
		$fechaIniFin=$request->get('fechaIni').'a'.$request->get('fechaFin');

		//verificamos si el XML se genero con aterioridad
		DB::select("UPDATE encabezado_xmls SET activo=0,observacion='Anulado por modificaciones' WHERE periodo_fiscal=:periodo AND rif_empresa=:rife AND activo=1",[$ultimaFechaMes,$request->get('empresaRif')]);
		//self::xmlDeletePorPeriodo($ultimaFechaMes,$request->get('empresaRif')); //eliminar xml+encabezado xml

		if(($fecha1==$fecha2) and ($request->get('fechaIni')<$request->get('fechaFin')) ){

			//****esta funcion obtiene los resultados de las FACTURAS que van para el xml ****
			$xmlFacturas=self::buscarFecha($request->get('fechaIni'),$request->get('fechaFin'),$empresaRif);
			
			//****esta funcion obtiene los resultados de los EMPLEADOS que van para el xml ****
			$ultimaFechaMes = self::ultimaFechaMes($request->get('fechaFin'));
			$xmlEmpleados=self::buscarEmpleados($empresaRif,$ultimaFechaMes);

			//unimos los dos arreglos en uno solo
			$xml=array_merge($xmlFacturas,$xmlEmpleados);

			//si no hay registros retornamos a la vista sin resultados
			if(empty($xml)){
				return view('islr.xml.xmlcrear',['errorFecha'=>'No se encontraron registros en el rango de fecha seleccionado','empresas'=>$empresas]);
			}		

			//eliminamos el caracter '-' del rif del agente de retencion ya que lo requiere asi el xml
			$rifAgente = str_replace("-","",($xml[0]->e_rif));

			//registro del encabezado del archivo XML
			//generamos un array con los datos del encabezado
			$datosEncabezado = array(
				'rif_empresa' =>$xml[0]->e_rif,
				'nombre_empresa'=>$xml[0]->empresa,
				'periodo_fiscal'=>$ultimaFechaMes,
				'fechas_periodo_fiscal'=>$fechaIniFin,
				'observacion'=> $request->get('observacion'),
			);
			//pasamos los datos a la funcion que los guarda y nos retorna el id de lo que guardamos
			$encabezadoId = self::guardarEncabezadoXml($datosEncabezado);

			//llamado a la funcion que guarda los datos en la tabla reporte_xmls
			self::guardarXml($xml,$ultimaFechaMes,$encabezadoId);

			//consultamos el reporte xml
			$reporteXml=self::consultarReporteXml($ultimaFechaMes,$xml[0]->e_rif,$encabezadoId);
			$datosEncabezado = EncabezadoXml::findOrFail($encabezadoId);
			//sumar el monto de los registros en sueldos y facturas para la vista
			$total =0;
			$total_retener=0;
			foreach($reporteXml as $valores){
				$total = $total + $valores->monto_operacion;
				$total_retener = $total_retener + $valores->total_retener;
			}
			$total = number_format($total,2,',','.');
			$total_retener = number_format($total_retener,2,',','.');
			
			return view('islr.xml.xmlver',[
				'xml'=>$reporteXml,
				'ultimoDia'=>$ultimaFechaMes,
				'total'=>$total,
				'total_retener'=>$total_retener,
				'encabezadoId'=>$encabezadoId,
				'fechaIniFin'=>$fechaIniFin,
				'datosEncabezado'=>$datosEncabezado
			]);
		}else{
			return view('islr.xml.xmlver',['errorFecha'=>'Este error se debe a que el rango de fechas no concuerdan porque deben pertenecer al mismo mes ó la fecha de inicio es mayor a la fecha final']);
		}	
	}
	public function xmlNew($id){
		//este metodo genera un nuevo xml por get teniendo solo el encabezado id
		//esto se utiliza cuando desde el mismo xml se vuelve a generar otro xml
		$encabezadoId=self::xmlEncabezadoConsultar($id);
		//inhabilitamos el registro anteior
		DB::select("UPDATE encabezado_xmls SET activo=0,observacion='Anulado por modificaciones' WHERE periodo_fiscal=:periodo AND rif_empresa=:rife AND activo=1",[$encabezadoId->periodo_fiscal,$encabezadoId->rif_empresa]);

		return self::xmlCrearGet($encabezadoId->fechas_periodo_fiscal,$encabezadoId->rif_empresa,"Registros generados nuevamente"); 
	}

	public function xmlCrearGet($fechaIniFin,$empresaRif,$aviso=''){
		//vista secundaria donde recibe los dato como espresa y fecha
		//obtenemos los meses de las dos fecha y si hay diferencias envia un error

		$fechas = explode('a', $fechaIniFin);
		$fechaIni=$fechas[0];
		$fechaFin=$fechas[1];
		$mes1 = date('m',strtotime($fechaIniFin[0]));
		$mes2 = date('m',strtotime($fechaIniFin[1]));		

			

	//	if(($fechaIni==$fechaFin) and ($fechaIni<$fechaFin) ){

			//****esta funcion obtiene los resultados de las FACTURAS que van para el xml ****
			$xmlFacturas=self::buscarFecha($fechaIni,$fechaFin,$empresaRif);

			//****esta funcion obtiene los resultados de los EMPLEADOS que van para el xml ****
			$ultimaFechaMes = self::ultimaFechaMes($fechaFin);
			$xmlEmpleados=self::buscarEmpleados($empresaRif,$ultimaFechaMes);

			//unimos los dos arreglos en uno solo
			$xml=array_merge($xmlFacturas,$xmlEmpleados);

			//si no hay registros retornamos a la vista sin resultados
			if(empty($xml)){

				return view('islr.xml.xmlcrear',['errorFecha'=>'No se encontraron registros en el rango de fecha seleccionado','empresas'=>Empresa::all()]);
			}		

			//eliminamos el caracter '-' del rif del agente de retencion ya que lo requiere asi el xml
			$rifAgente = str_replace("-","",($xml[0]->e_rif));

			//registro del encabezado del archivo XML
			//generamos un array con los datos del encabezado
			$datosEncabezado = array(
				'rif_empresa' =>$xml[0]->e_rif,
				'nombre_empresa'=>$xml[0]->empresa,
				'periodo_fiscal'=>self::ultimaFechaMes($fechaFin),
				'fechas_periodo_fiscal'=>$fechaIniFin,
				'observacion'=> '',
			);
			//pasamos los datos a la funcion que los guarda y nos retorna el id de lo que guardamos
			$encabezadoId = self::guardarEncabezadoXml($datosEncabezado);

			//llamado a la funcion que guarda los datos en la tabla reporte_xmls
			self::guardarXml($xml,$ultimaFechaMes,$encabezadoId);

			//consultamos el reporte xml
			$reporteXml=self::consultarReporteXml($ultimaFechaMes,$xml[0]->e_rif,$encabezadoId);
			
			//sumar el monto de los registros en sueldos y facturas para la vista
			$total =0;
			$total_retener=0;
			foreach($reporteXml as $valores){
				$total = $total + $valores->monto_operacion;
				$total_retener = $total_retener + $valores->total_retener;
			}
			$total = number_format($total,2,',','.');
			$total_retener = number_format($total_retener,2,',','.');
			$datosEncabezado = EncabezadoXml::findOrFail($encabezadoId);
			
			return view('islr.xml.xmlver',[
				'xml'=>$reporteXml,
				'ultimoDia'=>$ultimaFechaMes,
				'total'=>$total,
				'total_retener'=>$total_retener,
				'encabezadoId'=>$encabezadoId,
				'fechaIniFin'=>$fechaIniFin,
				'aviso'=>$aviso,
				'datosEncabezado'=>$datosEncabezado
			]);
	/*	}else{
			return view('xml.xmlver',['errorFecha'=>'Este error se debe a que el rango de fechas no concuerdan porque deben pertenecer al mismo mes ó la fecha de inicio es mayor a la fecha final']);
		}*/	
	}


	public function xmlListar(){
		
		$listadoxml = EncabezadoXml::where('rif_empresa',session('empresaRif'))->orderBy('id', 'desc')->get();
		$herramientas = new HerramientasController();
		return view('islr.xml.xmlLista',['listadoxml'=>$listadoxml,'empresas' =>$herramientas->listarEmpresas()]);
	}


	
	public function xmlver($ultimaFechaMes,$rifEmpresa,$encabezadoId,$fechaIniFin){
		//funcion que muestra los registros del archivo xml una vez le den 
		//clik al boton ver de la lista de archivos xml

		//consultamos el reporte xml
			$reporteXml=self::consultarReporteXml($ultimaFechaMes,$rifEmpresa,$encabezadoId);
			$total =0;
			$total_retener=0;
			foreach($reporteXml as $valores){
				$total = $total + $valores->monto_operacion;
				$total_retener = $total_retener + $valores->total_retener;
			}
			$total = number_format($total,2,',','.');
			$total_retener = number_format($total_retener,2,',','.');
			$datosEncabezado = EncabezadoXml::findOrFail($encabezadoId);		
		return view('islr.xml.xmlver',[
			'xml'=>$reporteXml,
			'total'=>$total,
			'total_retener'=>$total_retener,
			'encabezadoId'=>$encabezadoId,
			'fechaIniFin'=>$ultimaFechaMes,
			'datosEncabezado'=>$datosEncabezado
		]);
	}


	public function consultarReporteXml($ultimaFechaMes,$rifEmpresa,$encabezadoId){
		// este Metodo llamamos la funcion que crea el archivo xml y retornamos los registros a la vista
		 	

		$resultado = DB::select('
			select * from reporte_xmls 
			where rif_empresa=:rifEmpresa			
			and encabezado_id=:encabezadoId',
			['rifEmpresa'=>$rifEmpresa,			
			'encabezadoId'=>$encabezadoId]
		);

			//llamado a la funcion que genera el xml 'crearXml y le pasamos el arreglo xml con todos los datos'	
			self::crearArchivoXml($resultado,$ultimaFechaMes,$rifEmpresa);
		return $resultado;
	}


	public function guardarEncabezadoXml($datos){
		//funcion que guarda los datos de los encabezados del XML y retorna el id guardado 
		$encabezado = new EncabezadoXml();
		$encabezado->rif_empresa = $datos['rif_empresa'];
		$encabezado->nombre_empresa = $datos['nombre_empresa'];
		$encabezado->id_usuario = 0;
		$encabezado->nombre_usuario='';
		$encabezado->periodo_fiscal = $datos['periodo_fiscal'];
		$encabezado->fechas_periodo_fiscal = $datos['fechas_periodo_fiscal'];
		$encabezado->observacion = $datos['observacion'];
		$encabezado->save();
		return $encabezado->id;
	}

	

	public function guardarXml($xml,$ultimaFechaMes,$encabezadoId){
		//guardamos los registros del archivo XML en el atabla reporte_xmls
		//de esta forma el administrador puede hacer los respectivos ajustes antes de renerar 
		//el archivo xml
		
		foreach ($xml as $valueXml) {

			//verificamos si el numero de factura y de control viene morocho para separarlo
			//ya que los proveedores natural solo se le aplica un solo sustraendo, el ingresador suma 
			//los montos y amorocha los numero de factura para efectos internos pero para el seniat
			//debe ir un solo numero de factura.
			$nFactura = str_replace('-','', $valueXml->nFactura);
			$nFactura = str_replace(' ','',$nFactura);
			$nFactura = explode('/', $nFactura);
		
			$nControlFactura = str_replace('-','', $valueXml->nControlFactura);	
			$nControlFactura = str_replace(' ','', $nControlFactura);		
			$nControlFactura = explode('/', $nControlFactura);

			$reporteXml = new ReporteXml();
			$reporteXml->rif_empresa = $valueXml->e_rif;
			$reporteXml->encabezado_id = $encabezadoId;
			$reporteXml->nombre_empresa = $valueXml->empresa;
			$reporteXml->rif_retenido = $valueXml->p_rif;
			$reporteXml->nombre_retenido = $valueXml->proveedor;
			$reporteXml->num_factura=$nFactura[0];
			$reporteXml->num_control=$nControlFactura[0];
			$reporteXml->fecha=$valueXml->fecha;			
			$reporteXml->codigo_servicio=$valueXml->proveedor_codfiscal;			
			$reporteXml->monto_operacion=$valueXml->monto;
			$reporteXml->porcentaje_retencion=$valueXml->porcentaje_retencion;
			$reporteXml->total_retener = $valueXml->total_retener_factura;
			$reporteXml->islr_o_rrhh = $valueXml->islr_o_rrhh;
			$reporteXml->islr_y_rrhh_id = $valueXml->islr_y_rrhh_id;
			$reporteXml->save();
		}
		
	}

	public function xmlUpdate(Request $request,$id,$empresa,$fecha,$encabezadoId){
		//esta funcion actualiza los registros segun el id de la tabla reporte_xmls
		//convertimos el monto separado por miles en monto para mysql
		$monto = self::convertirMonto($request->get('monto_operacion'));

		$xml = ReporteXml::findOrFail($id);
		$xml->rif_retenido = $request->get('rif_retenido');
		$xml->num_factura = $request->get('num_factura');
		$xml->num_control = $request->get('num_control');
		$xml->codigo_servicio = $request->get('codigo_servicio');
		$xml->monto_operacion = $monto;
		$xml->porcentaje_retencion = $request->get('porcentaje_retencion');
		$xml->total_retener = $request->get('total_retener');
		$xml->update();

		//obtenemos los datos de la empresa
		//$empresas = Empresa::all();
		//consultamos el reporte xml
		$reporteXml=self::consultarReporteXml($fecha,$empresa,$encabezadoId);
		$total =0;
			$total_retener=0;
			foreach($reporteXml as $valores){
				$total = $total + $valores->monto_operacion;
				$total_retener = $total_retener + $valores->total_retener;
			}
			$total = number_format($total,2,',','.');
			$total_retener = number_format($total_retener,2,',','.');
			$datosEncabezado = EncabezadoXml::findOrFail($encabezadoId);
		return view('islr.xml.xmlver',[
			'xml'=>$reporteXml,
			'total'=>$total,
			'total_retener'=>$total_retener,
			'encabezadoId'=>$encabezadoId,
			'datosEncabezado'=>$datosEncabezado
		]);
	}


	public function xmlDelete($id){
		$encabezadoxml= EncabezadoXml::findOrFail($id);		
		if($encabezadoxml->delete()){
			DB::delete('delete from reporte_xmls where encabezado_id=?',[$id]);
			return redirect('/regisretenciones/xml-listar');
		}else{
			return redirect('/regisretenciones/xml-listar');
		}
	}

	public function xmlEncabezadoConsultar($id){
		return $encabezadoxml= EncabezadoXml::findOrFail($id);
	}

	public function xmlDeletePorPeriodo($periodo,$rifEmpresa){
		$encabezadoxml = EncabezadoXml::where('periodo_fiscal','=',$periodo)->where('rif_empresa','=',$rifEmpresa);	
		if($encabezadoxml->delete()){
			DB::delete('delete from reporte_xmls where encabezado_id=?',[$encabezadoxml->id]);
			return redirect('/regisretenciones/xml-listar');
		}else{
			return redirect('/regisretenciones/xml-listar');
		}
	}


	public function crearArchivoXml($xml,$ultimaFechaMes,$rifAgente){
		// Vamos a crear un XML con XMLWriter a partir de la matriz anterior. 
		//Lo vamos a crear usando programación orientada a objetos. 
		//Por lo tanto, empezamos creando un objeto de la clase XMLWriter.

		//convertir la fecha 01-05-2020 a 202005 ya que lo requiere asi el xml
		$periodo = date('Ym',strtotime($ultimaFechaMes));
		$rifAgente = str_replace("-", "", $rifAgente);
		$objetoXML = new XMLWriter();

		// Estructura básica del XML
		$rutaArchivo=public_path("xml/detalleRetencion".$rifAgente."-".$periodo.".xml"); //($rutaArchivo);
		$objetoXML->openURI($rutaArchivo);
		$objetoXML->setIndent(true);
		$objetoXML->setIndentString("\t");
		$objetoXML->startDocument('1.0', 'ISO-8859-1');

		// Inicio del nodo raíz	
		// Se inicia un elemento para cada registro.	
		$objetoXML->startElement("RelacionRetencionesISLR"); 
		// Atributo de la fecha de final del elemento obra
		$objetoXML->writeAttribute("RifAgente", $rifAgente);
		// Atributo de la fecha de inicio del elemento obra
		$objetoXML->writeAttribute("Periodo", $periodo);
			
		
		foreach ($xml as $detalleRetencion){

			//cambiar el formato de la fecha a dd-mm-aaaa
			$fecha = date('d/m/Y',strtotime($detalleRetencion->fecha));

			//CONFIGURACIONES PREVIAS
			//si no hay numero de factura colocar 0 esto aplica a los empleados de la farmacia
			if(isset($detalleRetencion->num_factura)){
				$nFactura = $detalleRetencion->num_factura;
				$nFactura = str_replace("-", "", $nFactura);
			}else{
				$nFactura =0;
			}

			//si no hay numero de control colocar 0 esto aplica a los empleados de la farmacia
			if(isset($detalleRetencion->num_control)){
				$nControlFactura = $detalleRetencion->num_control;
				$nControlFactura = str_replace("-", "", $nControlFactura);
			}else{
				$nControlFactura = 0;
			}

			//si no hay porcentaje de Retencion colocar 0
			if(isset($detalleRetencion->porcentaje_retencion)){
				$porcentaje_retencion = $detalleRetencion->porcentaje_retencion;
			}else{
				$porcentaje_retencion = 0;
			}
		

			//reemplazar el "-"
			$p_rif=str_replace("-","",$detalleRetencion->rif_retenido);
			//dd($detalleRetencion->rif_retenido,$p_rif);
			// FIN CONFIGURACION PREVIAS

			// Inicio del elemento que cubre todos el contenido Papá
			$objetoXML->startElement("DetalleRetencion");
						
				$objetoXML->startElement('RifRetenido');//inicio elemento hijo				
				$objetoXML->text($p_rif); //contenido del elemento creado
				$objetoXML->endElement();// Finaliza cada elelmento hijo.

				$objetoXML->startElement('NumeroFactura');
				$objetoXML->text($nFactura);
				$objetoXML->endElement();

				$objetoXML->startElement('NumeroControl');
				$objetoXML->text($nControlFactura);
				$objetoXML->endElement();

				$objetoXML->startElement('FechaOperacion');
				$objetoXML->text($fecha);
				$objetoXML->endElement();
					
				$objetoXML->startElement('CodigoConcepto');
				$objetoXML->text($detalleRetencion->codigo_servicio);
				$objetoXML->endElement();

				$objetoXML->startElement('MontoOperacion');
				$objetoXML->text($detalleRetencion->monto_operacion);
				$objetoXML->endElement();

				$objetoXML->startElement('PorcentajeRetencion');
				$objetoXML->text($porcentaje_retencion);
				$objetoXML->endElement();
			
			$objetoXML->endElement(); // Final del elemento que cubre todos el contenido
		}
			
		$objetoXML->fullEndElement (); // Final del elemento "RelacionRetencionesISLR" que cubre cada obra de la matriz.	
		$objetoXML->endDocument(); // Final del documento
		//return $rutaArchivo;
	}

	public function descargarXml($rif,$periodo){
		
		$pathtoFile = public_path("xml/detalleRetencion".$rif."-".$periodo.".xml");
      	return response()->download($pathtoFile);
	}

	public function ultimaFechaMes($fecha){
		//optener el ultimo dia del mes		
		$month    = date('Y-m',strtotime($fecha));
		$aux      = date('Y-m-d', strtotime("{$month} + 1 month"));
		$ultimaFechaMes = date('Y-m-d', strtotime("{$aux} - 1 day"));
		return $ultimaFechaMes;
	}
}
