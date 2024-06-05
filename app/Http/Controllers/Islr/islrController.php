<?php

namespace App\Http\Controllers\Islr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Empresa;
use App\Models\Islr;
use App\Models\Retencion;
use App\Models\IslrDetalle;
use App\Models\ReporteXml;
use App\Models\EncabezadoXml;
use App\Models\Parametro;
use App\Models\NumerosEnLetras;
use App\Http\Controllers\Admin\ProveedorController;
use App\Http\Controllers\Herramientas\HerramientasController;
use App\Http\Controllers\Islr\xmlController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
class islrController extends Controller
{
    public function index(){
		
		
        	$empresas = Empresa::all();
        	$registros=[]; 
        	//$total =0;
			
			$islr = DB::table('islrs')
					->join('empresas','islrs.empresa_rif','=','empresas.rif')
					->join('proveedors','islrs.proveedor_rif','=','proveedors.rif')									
					->select('islrs.id', 'islrs.nControl','empresas.nombre as empresa','empresas.nom_corto','islrs.fecha','proveedors.nombre as proveedor','proveedors.rif','proveedors.direccion','proveedors.tipo_contribuyente','islrs.concepto','islrs.n_egreso_cheque','islrs.serie','islrs.total_retener','islrs.total_letras')
					->orderBy('islrs.id','desc')
					->get();
			foreach ($islr as $valores) {
				//buscar los numeros de facturas.
				$detalleRetencion = self::buscarDetalleRetencion($valores->id);

				//llenamos un arreglo para la vista de index islr
				$registro = array(
								'id' =>$valores->id,
								'nControl' =>$nControl=str_pad(($valores->nControl), 8, "0", STR_PAD_LEFT),
								'empresa' =>$valores->empresa,
								'nom_corto' =>$valores->nom_corto,
								'fecha' =>date('d-m-Y',strtotime($valores->fecha)),
								'proveedor' =>$valores->proveedor,
								'rif' =>$valores->rif,
								'direccion' =>$valores->direccion,
								'tipo_contribuyente' =>$valores->tipo_contribuyente,
								'concepto' =>$valores->concepto,
								'n_egreso_cheque' =>$valores->n_egreso_cheque,
								'serie' =>$valores->serie,
								'total_retener' =>number_format($valores->total_retener,2,',','.'),
								'total_letras' =>$valores->total_letras,
								'detalleRetencion'=> $detalleRetencion
								);
				//$total=$total+$valores->total_retener;
				$registros[]=$registro;				 
			}
			
		return view('islr.islr.index',['islrs'=>$registros,'empresas'=>$empresas,'paginacion'=>$islr,'total'=>'']);
	}

	public function todosRegistrosIslrAjax(){
		
		
		
		$registros=[]; 
		
		
		$islr = DB::table('islrs')
				->join('empresas','islrs.empresa_rif','=','empresas.rif')
				->join('proveedors','islrs.proveedor_rif','=','proveedors.rif')									
				->select('islrs.id', 'islrs.nControl','empresas.nombre as empresa','empresas.nom_corto','islrs.fecha','proveedors.nombre as proveedor','proveedors.rif','proveedors.direccion','proveedors.tipo_contribuyente','islrs.concepto','islrs.n_egreso_cheque','islrs.serie','islrs.total_retener','islrs.total_letras')
				->orderBy('islrs.id','desc')
				->get();
		foreach ($islr as $valores) {
			//buscar los numeros de facturas.
			$detalleRetencion = self::buscarDetalleRetencion($valores->id);

			//llenamos un arreglo para la vista de index islr
			$registro = array(
							'id' =>$valores->id,
							'nControl' =>$nControl=str_pad(($valores->nControl), 8, "0", STR_PAD_LEFT),
							'empresa' =>$valores->empresa,
							'nom_corto' =>$valores->nom_corto,
							'fecha' =>date('d-m-Y',strtotime($valores->fecha)),
							'proveedor' =>$valores->proveedor,
							'rif' =>$valores->rif,
							'direccion' =>$valores->direccion,
							'tipo_contribuyente' =>$valores->tipo_contribuyente,
							'concepto' =>$valores->concepto,
							'n_egreso_cheque' =>$valores->n_egreso_cheque,
							'serie' =>$valores->serie,
							'total_retener' =>number_format($valores->total_retener,2,',','.'),
							'total_letras' =>$valores->total_letras,
							'detalleRetencion'=> $detalleRetencion
							);
			//$total=$total+$valores->total_retener;
			$registros[]=$registro;				 
		}
		return $registros;
	
	}

	public function filtrar(Request $request){
		$proveedor = $request->get('proveedor');	
		$registros=[];	
		$empresas = Empresa::all(); 
			$islr = DB::table('islrs')
					->join('empresas','islrs.empresa_rif','=','empresas.rif')
					->join('proveedors','islrs.proveedor_id','=','proveedors.id')					
					->select('islrs.id', 'islrs.nControl','empresas.nombre as empresa','empresas.nom_corto','islrs.fecha','proveedors.nombre as proveedor','proveedors.rif','proveedors.direccion','proveedors.tipo_contribuyente','islrs.concepto','islrs.n_egreso_cheque','islrs.serie','islrs.total_retener','islrs.total_letras')
					->where('islrs.empresa_rif','=',$request->get('empresa'))
					->where('proveedors.nombre','like','%'.$proveedor.'%')
					->where('islrs.fecha','>=',$request->get('fecha1'))
					->where('islrs.fecha','<=',$request->get('fecha2'))							
					->orderBy('islrs.id','desc')
					->get();
			$total=0;		
			foreach ($islr as $valores) {
				//buscar los numeros de facturas.
				$detalleRetencion = self::buscarDetalleRetencion($valores->id);

				//llenamos un arreglo para la vista de index islr
				$registro = array(
								'id' =>$valores->id,
								'nControl' =>$nControl=str_pad(($valores->nControl), 8, "0", STR_PAD_LEFT),
								'empresa' =>$valores->empresa,
								'nom_corto' =>$valores->nom_corto,
								'fecha' =>date('d-m-Y',strtotime($valores->fecha)),
								'proveedor' =>$valores->proveedor,
								'rif' =>$valores->rif,
								'direccion' =>$valores->direccion,
								'tipo_contribuyente' =>$valores->tipo_contribuyente,
								'concepto' =>$valores->concepto,
								'n_egreso_cheque' =>$valores->n_egreso_cheque,
								'serie' =>$valores->serie,
								'total_retener' =>number_format($valores->total_retener,2,',','.'),
								'total_letras' =>$valores->total_letras,
								'detalleRetencion'=> $detalleRetencion
								);
				$total=$total+$valores->total_retener;
				$registros[]=$registro;				 
			};		
		
			$total=number_format($total,2,',','.');
		return view('islr.islr.index',['islrs'=>$registros,'empresas'=>$empresas,'paginacion'=>$islr,'total'=>$total]);
	}


	public function buscar($id){
		$islr = DB::select('
			select 
				i.id,
				i.nControl,
				i.nFactura,
				i.nControlFactura,
				i.empresa_id,
				i.proveedor_id,
				i.empresa_rif,
				i.proveedor_rif,
				e.nombre as empresa,
				e.direccion as e_direccion,
				e.nom_corto,
				i.fecha,
				e.rif as e_rif,
				p.nombre proveedor,
				p.codigoFiscal,
				i.proveedor_codfiscal,
				p.rif as p_rif,
				p.direccion as p_direccion,
				p.tipo_contribuyente,
				i.concepto,
				i.n_egreso_cheque,
				i.monto,
				i.serie,
				i.sustraendo,
				i.total_retener,
				i.total_letras,
				e.firma 
			from 
				islrs i,
				proveedors p,
				empresas e 
			where 
				i.empresa_rif=e.rif and 
				i.proveedor_rif=p.rif and 
				i.id=:id_islrs limit 1',['id_islrs'=>$id]);
		return $islr;
	}


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
				e.nom_corto,i.fecha,
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
				e.rif=:empresaRif",
				['fechaini'=>$fechaini,'fechafin'=>$fechafin,'empresaRif'=>$empresa]);
		return $islr;
	}


	public function create(){
		$empresas = Empresa::all();
		$obj_proveedor = new ProveedorController();
		$proveedores = $obj_proveedor->listarProveedorServicio();	

		
		return view('islr.islr.create',['empresas'=>$empresas,'proveedores'=>$proveedores]);
	}


	private function buscarDetalleRetencion($idRetencion){
		
		return $detalleRetenciones=DB::select('select * from islr_detalles where islr_id=:islrId',['islrId'=>$idRetencion]);
	}


	public function registroIslr(Request $request)
	{
		// en este metodo se guardan la empresa el proveedor y el numero de control
		//se valida el numero de control por si la pagina la han recargado y no se vuelva a guardar el mismo numero 

		
		$islr = new Islr();//crear un registro nuevo		
		$empresa= explode('|', $request->get('empresa_id')); //empresa_id trae el id y el rif separador '|'			
		$proveedor= explode('|',$request->get('proveedor_id'));					
		$nControl = Parametro::buscarVariable('contador_retencion_'.$empresa[1]);		
		/*Parametro::actualizarVariable('contador_retencion_'.$empresa[1],$nControl+1);//sumar el contador*/
			
		
		$obj_proveedor = new ProveedorController();
		$proveedores = $obj_proveedor->listarProveedor();
		$empresas = Empresa::all();
		//esto se hace para evitar que se duplique el registro al recargar la pagina y reenviar los datos del//formulario

		//buscamos la variable contador para saber el nuevo numero de control a registrar
		//
		//$islr->nControl = $nControl;//contador de item		
		$islr->empresa_id = $empresa[0];
		$islr->empresa_rif = $empresa[1];
		$islr->proveedor_id = $proveedor[0];
		$islr->proveedor_rif = $proveedor[1];
		$islr->usuario_id = Auth::user()->id;
		$islr->save();//guardamos


		//declaramos el arreglo que mantiene los datos del formulario
		//esto funciona al momento de guardar y no hay monto y porcentaje registrado
		$valores =[
			'fecha'=>'',
			'concepto'=>'',
			'n_egreso_cheque'=>'',
			'nFactura'=>'',
			'nControlFactura'=>'',
			'proveedor_codfiscal'=>'',
			'serie'=>'',			
		];
	
		$datosRetencion= self::buscar($islr->id); //buscamos lo datos previos registrados
		$ultimoPorcentajeProveedor = Islr::ultimoPorcentajeDelProveedor($proveedor[1],$empresa[1]);

		return view('islr.islr.createDetalle',[
			'datosRetenciones'=>$datosRetencion,
			'empresas'=>$empresas,
			'proveedores'=>$proveedores,
			'valores'=>$valores,
			'accion'=>'create',
			'ultimoPorcentajeProveedor'=>$ultimoPorcentajeProveedor,
			'contador'=>$nControl,
		]); 							
		
	}


	public function montoServicios($idRetencion,$ultimoPorcentajeProveedor=0){
		//en esta funcion se carga el iframe donde se registraran 
		//los montos de los servicios a retener impuestos
		$porcentajeRetencion = Retencion::all();

		//buscamos los datos de retencion para que al cargar la vista 
		//editar se muestre el total y total en letras
		$datosRetencion = self::buscar($idRetencion);

		//buscamos los detalles de retencion de las retenciones para la vista
		$detalleRetenciones = self::buscarDetalleRetencion($idRetencion);

		return view('islr.islr.montoServicios',['porcentajeRetencion'=>$porcentajeRetencion,'idRetencion'=>$idRetencion,'detalleRetenciones'=>$detalleRetenciones,'datosRetenciones'=>$datosRetencion,'ultimoPorcentajeProveedor'=>$ultimoPorcentajeProveedor]);
	}
	
	
	public function saveMontoServicios(Request $request,$idRetencion){

		///Guardar los Detalles de la retencion estas son las facturas de los proveedores
		
		//$monto = HerramientasController::convertirMonto($request->get('monto'));
		$monto = $request->get('monto');
		$datosRetencion = self::buscar($idRetencion);//buscamos los datos de retencion

		$porcenId= $request->get('porcentaje_id');//id del porcentaje de retencion		
		
		//Buscar los valores determinados de la retencion
		$porcenReten = Retencion::findOrFail($porcenId);//buscar valores del porcentaje de retencion
		
		$pr=$porcenReten->procent_retencion;//porcentaje de retencion
		$sustra=$porcenReten->sustraendo;	//sustraendo de la retencion		
		


		//Buscar si el proveedor es Natural o Juridico ya que
		// el natural se le aplica el sustrendo
		$obj_proveedor = new ProveedorController();

		//guardamos el ultimo porcentaje de retencion en el proveedor
		$obj_proveedor->actualizarUltimoPorcentajeRetencionIslr($datosRetencion[0]->proveedor_id,$pr);

		$proveedor = $obj_proveedor->search($datosRetencion[0]->proveedor_id);
		if($proveedor->tipo_contribuyente=="Juridico"){
			$sustra=0;
		}		
		$montoRetenido = (($monto*$pr)/100);	

		//buscamos los porcentajes de retencion para la vista.
		$porcentajeRetencion = Retencion::all();

		//guardamos los detalles de la retencion 
		$islrDetalle = new IslrDetalle();
		$islrDetalle->islr_id = $idRetencion;
		$islrDetalle->fecha_factura = $request->get('fecha_factura');
		$islrDetalle->nControl = $request->get('nControlFactura');
		$islrDetalle->nFactura = $request->get('nFactura');				
		$islrDetalle->monto=$monto;
		$islrDetalle->concepto = $request->get('concepto');
		$islrDetalle->id_porcentaje_retencion = $request->get('porcentaje_id');
		$islrDetalle->porcentaje_retencion = $pr;
		$islrDetalle->monto_retenido = $montoRetenido;
		$islrDetalle->sustraendo= $sustra;
		$islrDetalle->total_retener = $montoRetenido-$sustra;			
		$islrDetalle->save();

		//buscamos los detalles de retencion de las retenciones para la vista
		$detalleRetenciones = self::buscarDetalleRetencion($datosRetencion[0]->id);

		// calculamos y actualizamos el monto a retener en numeros y letras 
		self::calculoImpuestoRetener($detalleRetenciones,$datosRetencion[0]->id);

		//despues de actualizar busco nuevamente los datos y los envio a la vista
		$datosRetencion = self::buscar($idRetencion);//buscamos los datos de retencion
		
		return view('islr.islr.montoServicios',['porcentajeRetencion'=>$porcentajeRetencion,'datosRetenciones'=>$datosRetencion,'detalleRetenciones'=>$detalleRetenciones,'idRetencion'=>$idRetencion,'ultimoPorcentajeProveedor'=>$pr]);
	}


	public function saveRegistroIslrGeneral(Request $request,$datosRetencionId,$accion,$encabezadoId=0,$fechaIniFin=''){
		//aqui guardamos los datos generales de la retencion
		
		$islr = Islr::findOrFail($datosRetencionId);
		$islr->fecha = $request->get('fecha');
		$islr->concepto = $request->get('concepto');
		$islr->n_egreso_cheque = $request->get('n_egreso_cheque');
		$islr->nFactura = $request->get('nFactura');
		$islr->nControlFactura = $request->get('nControlFactura');
		$islr->serie = $request->get('serie');
		$islr->proveedor_codfiscal = $request->get('proveedor_codfiscal');
		$islr->usuario_id = Auth::user()->id;
		
		//verificamos si la accion es edit o insertar, si es editar y no tiene numero de control se le agrega
		if($accion!='edit' or ($accion=='edit' and $islr->nControl==null)){
			$nControl = Parametro::buscarVariable('contador_retencion_'.$islr->empresa_rif);		
			Parametro::actualizarVariable('contador_retencion_'.$islr->empresa_rif,$nControl+1);
			$islr->nControl = $nControl;//contador de item
		}

		
		//guardamos en este arreglo los datos generales de la retencion de impuesto
		//para la vista
		$valores =[
			'fecha'=>$request->get('fecha'),
			'concepto'=>$request->get('concepto'),
			'n_egreso_cheque'=>$request->get('n_egreso_cheque'),
			'nFactura'=>$request->get('nFactura'),
			'nControlFactura'=>$request->get('nControlFactura'),
			'serie'=>$request->get('serie'),
			'proveedor_codfiscal'=>$request->get('proveedor_codfiscal')
				];
		$islr->update();

		//actualizamos el codigo concepto proveedor que lo llamamos codigo fiscal del proveedor
		//de esta manera cuando la administradora actualice ente monto aparecera por defecto 
		//al agregar una nueva retencion

		$obj_proveedor = new ProveedorController();
		$proveedor = $obj_proveedor->search($islr->proveedor_id);
		$proveedor->codigoFiscal = $request->get('proveedor_codfiscal');
		$proveedor->save();
		
		
		////////////////////////////////retorno al XML///////////////////////////////////			
			if($accion=='xml'){
				//consultamos el rango de fechas del periodo fiscal para crear nuevamente el reporte xml
				$fechaIniFin = EncabezadoXml::where('id',$encabezadoId)->select('fechas_periodo_fiscal')->get();
							
				$xmlController = new xmlController();
				$xmlController->xmlDelete($encabezadoId);//eliminamos el xml anterior

				//return $xmlController->xmlver($ultimaFechaMes,$islr->empresa_rif,$encabezadoId);
				//creamos el nuevo xml con los nuevos cambios
				return $xmlController->xmlCrearGet($fechaIniFin[0]->fechas_periodo_fiscal,$islr->empresa_rif);
			}else{
				////////////////////retorno al registro de retencion de impuestos ///////////////////
				//return redirect('/regisretenciones');
				return self::view($islr->id);
			}	
	}


	private function calculoImpuestoRetener($detalleRetenciones,$datosRetencionId){
		//******* sumar los sustraendos y monto retenido y calcular el total********
		//inicializamos el contador
		$totalSustraendo=0;
		$totalMontoRetenido=0;
		$totalMonto=0;
		//sumatoria de los montos y sustraendos
		foreach ($detalleRetenciones as $detalleRetencion) {
			$totalSustraendo += $detalleRetencion->sustraendo;
			$totalMontoRetenido += $detalleRetencion->monto_retenido;
			$totalMonto += $detalleRetencion->monto;
		}
		
		$totalRetener = $totalMontoRetenido - $totalSustraendo;
		//convertir los numeros en letras//
		$totalLetras=NumerosEnLetras::convertir(number_format($totalRetener,2,'.',','),'Bolivares',false,'Centimos');
		
		//actualizamos datos de retenciones
		$islr = Islr::findOrFail($datosRetencionId);
		$islr->monto = $totalMonto;
		$islr->sustraendo = $totalSustraendo;		
		$islr->total_retener = $totalRetener;
		$islr->total_letras = $totalLetras;
		$islr->update();

		//return $datos= array('totalRetener'=>$totalRetener,'totalLetras'=>$totalLetras);
	}

	public function detalle(){
		//mostrar 2da vista de registro impuesto a retener
		return view('islr.islr.createDetalle');
	}

	public function deleteDetalles($id,$idRetencion)
	{
		//**** Metodo para ELIMINAR Monto *****

		$porcentajeRetencion = Retencion::all(); //buscamos todos los porcentajes de retencion
		$datosRetencion = self::buscar($idRetencion);//buscamos los datos de retencion

		 //buscamos los detalles de retencion de las retenciones
		//$detalleRetenciones = self::buscarDetalleRetencion($idRetencion);

		$islrDetalle = IslrDetalle::findOrFail($id);
		if($islrDetalle->delete()){

			//buscamos los detalles de retencion de las retenciones
			$detalleRetenciones = self::buscarDetalleRetencion($idRetencion);

			// calculamos y actualizamos el monto a retener en numeros y letras 
			self::calculoImpuestoRetener($detalleRetenciones,$datosRetencion[0]->id);
			$datosRetencion = self::buscar($idRetencion);//buscamos los datos de retencion
			
			return view('islr.islr.montoServicios',['porcentajeRetencion'=>$porcentajeRetencion,'datosRetenciones'=>$datosRetencion,'detalleRetenciones'=>$detalleRetenciones,'idRetencion'=>$idRetencion]);
		}else{

			//buscamos los detalles de retencion de las retenciones
			$detalleRetenciones = self::buscarDetalleRetencion($idRetencion);

			// calculamos y actualizamos el monto a retener en numeros y letras 
			self::calculoImpuestoRetener($detalleRetenciones,$datosRetencion[0]->id);
			$datosRetencion = self::buscar($idRetencion);//buscamos los datos de retencion
			
			return view('islr.islr.montoServicios',['porcentajeRetencion'=>$porcentajeRetencion,'datosRetenciones'=>$datosRetencion,'detalleRetenciones'=>$detalleRetenciones,'idRetencion'=>$idRetencion]);
		}
	}


	public function editDetalles($id,$idRetencion)
	{
		//**** Metodo para EDITAR Monto *****

		$porcentajeRetencion = Retencion::all(); //buscamos todos los porcentajes de retencion
		$datosRetencion = self::buscar($idRetencion);//buscamos los datos de retencion

		 //buscamos los detalles de retencion de las retenciones
		//$detalleRetenciones = self::buscarDetalleRetencion($idRetencion);

		$islrDetalle = IslrDetalle::findOrFail($id);
		if($islrDetalle->delete()){

			//buscamos los detalles de retencion de las retenciones
			$detalleRetenciones = self::buscarDetalleRetencion($idRetencion);

			// calculamos y actualizamos el monto a retener en numeros y letras 
			self::calculoImpuestoRetener($detalleRetenciones,$datosRetencion[0]->id);
			$datosRetencion = self::buscar($idRetencion);//buscamos los datos de retencion
			
			return view('islr.islr.montoServicios',['porcentajeRetencion'=>$porcentajeRetencion,'datosRetenciones'=>$datosRetencion,'detalleRetenciones'=>$detalleRetenciones,'idRetencion'=>$idRetencion,'islrDetalleEditar'=>$islrDetalle]);
		}else{

			//buscamos los detalles de retencion de las retenciones
			$detalleRetenciones = self::buscarDetalleRetencion($idRetencion);

			// calculamos y actualizamos el monto a retener en numeros y letras 
			self::calculoImpuestoRetener($detalleRetenciones,$datosRetencion[0]->id);
			$datosRetencion = self::buscar($idRetencion);//buscamos los datos de retencion
			
			return view('islr.islr.montoServicios',['porcentajeRetencion'=>$porcentajeRetencion,'datosRetenciones'=>$datosRetencion,'detalleRetenciones'=>$detalleRetenciones,'idRetencion'=>$idRetencion]);
		}
	}	

	public function view($id){
		
		$islr=islrController::buscar($id);
		//buscamos los detalles de retencion de las retenciones
		$detalleRetenciones = self::buscarDetalleRetencion($id);
		return view('islr.islr.show',['islr'=>$islr,'detalleRetenciones'=>$detalleRetenciones]);
		
	}

	public function viewPdf($id,$comprobante=''){
		//generamos el archivo pdf y retornamos el objeto pdf y el nombre del archivo
		$nComprobante='';
		$islr=islrController::buscar($id);
		//buscamos los detalles de retencion de las retenciones
		$detalleRetenciones = self::buscarDetalleRetencion($id);
		$datosEmpresa = Empresa::select('direccion','logo','firma')->where('rif',session('empresaRif'))->first();
		$pdf = new Dompdf();
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
		$pdf->setPaper('letter','portrait'); // Establecer la orientación a horizontal
        $pdf->setOptions($options);
        $html = view('islr.islr.comprobantePdf', ['islr'=>$islr,'detalleRetenciones'=>$detalleRetenciones,'datosEmpresa'=>$datosEmpresa])->render(); // Reemplaza 'pdf.example' con el nombre de tu vista
        $pdf->loadHtml($html);
        $pdf->render();		

		///codigo de chat GPT3
		//guardamos el documento en storage de laravel
		if(empty($comprobante)){
			//si se crea el comprobante desde retencion de islr se le agrega su propio comprobante
			//pero si es de retencion de iva se coloca el comprobante de retencion de iva para poder adjuntar ambos archivos.
			$nComprobante = $islr[0]->nControl;
		}else{
			$nComprobante = $comprobante;
		}
		$nombreArchivo = 'ISLR-'.$islr[0]->empresa.'-'.$islr[0]->proveedor.'-'.$nComprobante.'.pdf';
		$nombreArchivo = str_replace(' ','_',$nombreArchivo);

		//eliminar los archivos de la carpeta storage
		// Verificar si el directorio existe		
		$rutaDirectorio='pdf/'.$nombreArchivo;
		$archivos = Storage::files($rutaDirectorio);
		if (!empty($archivos)) {
			foreach ($archivos as $archivo) {
				// Eliminar cada archivo individualmente
				Storage::delete($archivo);
			}			
		}
		
		$rutaArchivo = 'pdf/' . $nombreArchivo; // Ruta donde se guardará el archivo dentro de la carpeta storage

		Storage::put($rutaArchivo, $pdf->output()); // Guardar el archivo PDF en la carpeta storage
		//retornamos el archivo pdf
		return response()->stream(function () use ($pdf) {
			echo $pdf->output();
		}, 200, [
			'Content-Type' => 'application/pdf',
			'Content-Disposition' => 'inline; filename="' . $nombreArchivo. '"'
		]);//fin codigo GPT3	
	}

	public function buscarRetencionPorDocumento($empresaRif,$proveedorRif,$facturas){
		$facturas = explode(',',$facturas);
		foreach($facturas as $factura){
			$facturasArray[] = $factura;
		}
        $facturasFormat ='"'. implode('","',$facturasArray).'"';
		
        //BUSCAMOS LA RETENCION CON LOS DATOS EMPRESA, PROVEEDOR, FACTURAS(0012,0013)        
        
        $sql="SELECT            
            islrs.id,
			islrs.nControl,
            islrs.proveedor_rif,
            islrs.empresa_rif
          FROM
            islr_detalles,
            islrs
          WHERE SUBSTRING_INDEX (islr_detalles.nFactura, '/', 1) in( ".$facturasFormat." )
            AND islr_detalles.`islr_id` = islrs.id
            AND islrs.`proveedor_rif` ='".$proveedorRif."'
            AND islrs.`empresa_rif` ='".$empresaRif."' limit 1";
            
            $datosislr = DB::select($sql);
            
         return $datosislr;   
	}

	public function edit($id,$accion,$encabezadoId=0,$fechaIniFin=''){

		$empresas = Empresa::all();
		$ultimoPorcentajeProveedor=0;
		//buscamos los datos de retencion
		$datosRetencion = self::buscar($id); 
		
		$ultimoPorcentajeProveedor = Islr::ultimoPorcentajeDelProveedor($datosRetencion[0]->proveedor_rif,$datosRetencion[0]->empresa_rif);
		
		//Buscar si el proveedor es Natural o Juridico ya que
		// el natural se le aplica el sustrendo
		$obj_proveedor = new ProveedorController();

		$proveedores = $obj_proveedor->listarProveedor(); 
		
		//declaramos el arreglo que mantiene los datos del formulario
		//esto funciona al momento de guardar y no hay monto y porcentaje registrado

		$valores =[
			'fecha'=>$datosRetencion[0]->fecha,
			'concepto'=>$datosRetencion[0]->concepto,
			'n_egreso_cheque'=>$datosRetencion[0]->n_egreso_cheque,
			'nFactura'=>$datosRetencion[0]->nFactura,
			'serie'=>$datosRetencion[0]->serie,
			'nControlFactura'=>$datosRetencion[0]->nControlFactura,
			'proveedor_codfiscal'=>$datosRetencion[0]->proveedor_codfiscal
		];
		//la el parametro accion nos identifica si se va a crear un nuevo registro o editar ya que estamos reutilizando la vista y el metodo en ambos casos
		$nControl = Parametro::buscarVariable('contador_retencion_'.$datosRetencion[0]->empresa_rif);

			return view('islr.islr.createDetalle',[
				'datosRetenciones'=>$datosRetencion,
				'empresas'=>$empresas,
				'proveedores'=>$proveedores,
				'valores'=>$valores,
				'accion'=>$accion,
				'ultimoPorcentajeProveedor'=>$ultimoPorcentajeProveedor,
				'encabezadoId'=>$encabezadoId, //esto es si el llamado es desde xml para volver a generarlo despues de guardar los cambios
				'fechaIniFin'=>$fechaIniFin,
				'contador'=>$nControl,
			]);
	}

	
	public function eliminarIslr($id,$nControl=0,$empresaRif=''){
		
		$islr = Islr::findOrFail($id);
		$islr->delete();
		$monto_servicios = IslrDetalle::where('islr_id',$id);
		$monto_servicios->delete();

		if($nControl<>0){
			$nControl = Parametro::buscarVariable('contador_retencion_'.$empresaRif);
			Parametro::actualizarVariable('contador_retencion_'.$empresaRif,$nControl-1);//sumar el contador
		}
		return redirect('/regisretenciones');
	}


	public function exportExcel(){
		return Excel::download(new retencionesExport,'retencion-islr.xlsx');
	}
    

    public function convertirMonto($valor){
    	//este metodo cambia los montos 1.000.000,00 en validos para mysql 1000000.00
    	$decimal = str_replace(',', '.', str_replace('.', '', $valor));
    	return $decimal;
	}
}
