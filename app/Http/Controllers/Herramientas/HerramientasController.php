<?php

namespace App\Http\Controllers\Herramientas;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Parametro;
use App\Models\User;
use App\Models\Empresa;
use DateTime;
class HerramientasController extends Controller
{
	public function __construct(){
        /*
        buscamos las empresas registradas y sus datos para la vista
        */
        
	}
	
	public function obtenerIdUsuarioLogeado()
    {
        if (Auth::check()) {
            $idUsuario = Auth::id();
            return $idUsuario;
        }
        
        return 0;
    }
	
    public static function convertirMonto($valor){
    	//este metodo cambia los montos 1.000.000,00 en validos para mysql 1000000.00
    	$decimal = str_replace(',', '.', str_replace('.', '', $valor));
    	return $decimal;
	}

	public static function rellenarConCero($valor){
		//para en numero de control se cuenta la cantidad de registros y se rellenan con 00000001
		//utilizando la funcion str_pad($valor,cantidad_de_digitos_a_rellenar,"relleno",str_pad_left)
		// str_pad_left= direccion del relleno

		return	$nControl=str_pad(($valor), 8, "0", STR_PAD_LEFT);
	}

	public function listarEmpresas($usuarioId=0){

		///si se pasa como parametro el id del uisuario, se busca cuales son las empresas  a las cuales tiene acceso y esas son las que le va a mostrar
		////si no se pasa el usuario id se pasan todas las empresas
		$usuarioId = self::obtenerIdUsuarioLogeado();
 		$rifs=array();
		if($usuarioId > 0 ){
			
			$permisoEmpresas = DB::connection('mysql')->select('select * from usuarios_acceso_empresas where user_id =:userId',['userId'=>$usuarioId]);
			if(empty($permisoEmpresas)){
				dd("Revise las empresas asignadas en los datos del usuario porque al parecer no tiene ninguna empresa asignada");
			}
			foreach($permisoEmpresas as $permisoEmpresa){
			$rifs[]=$permisoEmpresa->empresa_rif;
			}
			$empresas = Empresa::whereIn('rif',$rifs)->get();
		}else{
			$empresas = Empresa::all();
		}
		
		
		return $empresas;
	}

	public function buscarEmpresa($keycodigo=0,$rif=''){
		if($keycodigo > 0){
			return $empresas = DB::connection('farmacias')->select('select * from farmacias where keycodigo=:key',['key'=>$keycodigo]);
		}

		if(!empty($rif)){
			return $empresas = DB::connection('farmacias')->select('select * from farmacias where rif=:rif',['rif'=>$rif]);
		}
	}

	public static function valorDolarPorFecha($fecha){
		$dolar='';
		$conexionSQL = self::conexionDinamicaBD(session('basedata'));
		//buscamos en la configuracin de cuentas por pagar si la tasa de la moneda secundaria es en historial_dolar o en tipo moneda del siace
		$cualTasa = Parametro::buscarVariable('verificar_tasa_dolar_tipo_moneda_o_historial_dolar');
		if($cualTasa == 'historial_dolar_vealo'){
			$dolar = DB::select('SELECT tasa_segunda_actualizacion from historial_dolar where fecha=:fecha',['fecha'=>$fecha]);
		}
		if($cualTasa=='historial_dolar_siace'){
			$dolar = $conexionSQL->select('SELECT tasa_segunda_actualizacion from historial_dolar where fecha=:fecha',['fecha'=>$fecha]);
		}
		if($cualTasa=='tipo_moneda_secundaria'){
			$dolar = $conexionSQL->select('SELECT precio_compra_moneda_nacional as tasa_segunda_actualizacion  FROM tipo_moneda WHERE is_moneda_secundaria=1');
		}
		if($cualTasa=='tipo_moneda_base'){
			$dolar = $conexionSQL->select('SELECT precio_compra_moneda_nacional as tasa_segunda_actualizacion  FROM tipo_moneda WHERE is_moneda_base=1');
		}
		if($cualTasa=='tipo_moneda_historial_tasa_vealo'){
			$dolar = DB::select('SELECT nueva_tasa_de_cambio_en_moneda_nacional as tasa_segunda_actualizacion  FROM tipo_moneda_historial_tasa WHERE fecha=:fecha',['fecha'=>$fecha]);
		}
		if($cualTasa=='tipo_moneda_historial_tasa_siace'){
			$dolar = $conexionSQL->select('SELECT nueva_tasa_de_cambio_en_moneda_nacional as tasa_segunda_actualizacion  FROM tipo_moneda_historial_tasa WHERE fecha=:fecha',['fecha'=>$fecha]);
		}
		//dd($dolar,$fecha,'herramientas controller 42');
		if(empty($dolar) or $dolar==0.000){
			
			\Session::flash('message', 'No se pudo optener el valor de la Tasa del Dolar a la fecha '.$fecha);
			return 1;
		}else{
			return $dolar[0]->tasa_segunda_actualizacion;
		}
		
	}

	public static function listarTodasLasTasa(){
		$dolar='';
		$conexionSQL = self::conexionDinamicaBD(session('basedata'));
		//buscamos en la configuracin de cuentas por pagar si la tasa de la moneda secundaria es en historial_dolar o en tipo moneda del siace
		$cualTasa = Parametro::buscarVariable('verificar_tasa_dolar_tipo_moneda_o_historial_dolar');
		if($cualTasa == 'historial_dolar_vealo'){
			$dolar = DB::select('SELECT tasa_segunda_actualizacion,fecha from historial_dolar order by fecha desc');
		}
		if($cualTasa=='historial_dolar_siace'){
			$dolar = $conexionSQL->select('SELECT tasa_segunda_actualizacion,fecha from historial_dolar  order by fecha desc');
		}
		
		if($cualTasa=='tipo_moneda_historial_tasa_vealo'){
			$dolar = DB::select('SELECT nueva_tasa_de_cambio_en_moneda_nacional as tasa_segunda_actualizacion,fecha  FROM tipo_moneda_historial_tasa  order by fecha desc');
		}
		if($cualTasa=='tipo_moneda_historial_tasa_siace'){
			$dolar = $conexionSQL->select('SELECT nueva_tasa_de_cambio_en_moneda_nacional as tasa_segunda_actualizacion, fecha  FROM tipo_moneda_historial_tasa  order by fecha desc');
		}
		//dd($dolar,$fecha,'herramientas controller 42');
		if(empty($dolar) or $dolar==0.000){
			
			\Session::flash('message', 'No se pudo optener el valor de la Tasa del Dolar a la fecha ');
			return 1;
		}else{
			return $dolar;
		}
		
	}
	
	public static function ultimoValorDolar(){
		$dolar='';
		//si hay empresa seleccionada
		if(!empty(session('basedata'))){
			$conexionSQL = self::conexionDinamicaBD(session('basedata'));
			//buscamos en la configuracin de cuentas por pagar si la tasa de la moneda secundaria es en historial_dolar o en tipo moneda del siace
			$cualTasa = Parametro::buscarVariable('verificar_tasa_dolar_tipo_moneda_o_historial_dolar');
			if($cualTasa=='historial_dolar_vealo'){
				$dolar = DB::select('SELECT * from historial_dolar order by keycodigo desc limit 1');
			}
			if($cualTasa=='historial_dolar_siace'){
				$dolar = $conexionSQL->select('SELECT * from historial_dolar order by keycodigo desc limit 1');
			}
			if($cualTasa=='tipo_moneda_secundaria'){
				$dolar = $conexionSQL->select('SELECT precio_compra_moneda_nacional as tasa_segunda_actualizacion  FROM tipo_moneda WHERE is_moneda_secundaria=1');
			}
			if($cualTasa=='tipo_moneda_base'){
				$dolar = $conexionSQL->select('SELECT precio_compra_moneda_nacional as tasa_segunda_actualizacion  FROM tipo_moneda WHERE is_moneda_base=1');
			}	
			if($cualTasa=='tipo_moneda_historial_tasa_vealo'){
				$dolar = DB::select('SELECT nueva_tasa_de_cambio_en_moneda_nacional as tasa_segunda_actualizacion  FROM tipo_moneda_historial_tasa ORDER BY keycodigo DESC limit 1');
			}
			if($cualTasa=='tipo_moneda_historial_tasa_siace'){
				$dolar = $conexionSQL->select('SELECT nueva_tasa_de_cambio_en_moneda_nacional as tasa_segunda_actualizacion  FROM tipo_moneda_historial_tasa ORDER BY keycodigo DESC limit 1');
			}
			//dd($dolar,$fecha,'herramientas controller 42');
			if(empty($dolar) or $dolar==0.000){
				
				\Session::flash('message', 'No se pudo optener el valor de la Tasa del Dolar');
				return 1;
			}else{
				return $dolar[0];
			}
		}else{
			return 0;
		}		
	}

	public function guardarTasa(Request $request){
		//este metodo se encarga de guardar el valor de la tasa de la divisa, el verifica con cual base de datos se esta trabajando, esto se defina en configuracion->cuentas por pagar
		//si la tabla base es del vealo se registran en ella y si es en siace lo hace en el siace pero solo uno de ambas, esto busca si se registro una fecha anteriormente lo edita si no exste 
		//crea un nuevo registro y si es viernes inserta viernes,sabado y domingo.
		$fecha = $request->fecha;
		$tasa = $request->tasa;
		$dia_semana = date('l', strtotime($fecha));
		$ndias = 0;
		if($dia_semana == 'Friday'){
			$ndias=2;
		}
		$conexionSQL = self::conexionDinamicaBD(session('basedata'));
		//buscamos en la configuracin de cuentas por pagar si la tasa de la moneda secundaria es en historial_dolar o en tipo moneda del siace
		$cualTasa = Parametro::buscarVariable('verificar_tasa_dolar_tipo_moneda_o_historial_dolar');
			//si es con la base de datos del VEALO	
		if($cualTasa=='tipo_moneda_historial_tasa_vealo'){
			//buscamos si el registro existe, de lo contrario se insertan
			$dolar = DB::select('SELECT nueva_tasa_de_cambio_en_moneda_nacional as tasa_segunda_actualizacion,fecha  FROM tipo_moneda_historial_tasa  where fecha=:fecha ',['fecha'=>$fecha]);
			if(isset($dolar[0]->tasa_segunda_actualizacion)){
				DB::update("update tipo_moneda_historial_tasa set nueva_tasa_de_cambio_en_moneda_nacional=? where fecha=?",[$tasa,$fecha]);
			}else{
				for($i=0;$i<=$ndias;){
					$fecha_actual = date('Y-m-d', strtotime($fecha . ' +'.$i.' day'));
					DB::insert("INSERT INTO tipo_moneda_historial_tasa (codtipomoneda,nueva_tasa_de_cambio_en_moneda_nacional,fecha) VALUES	(?,?,?)",[3,$tasa,$fecha_actual]);
					$i++;
				}
				
			}
		}
			//si es con la base de datos del SIACE
		if($cualTasa=='tipo_moneda_historial_tasa_siace'){
			$codTipoMoneda = 0;
			$dolar = $conexionSQL->select('SELECT nueva_tasa_de_cambio_en_moneda_nacional as tasa_segunda_actualizacion, fecha  FROM tipo_moneda_historial_tasa  where fecha=:fecha ',['fecha'=>$fecha]);
			//buscamos el codigo del tipo moneda divisas en el siace
			$tipoMoneda = $conexionSQL->select('SELECT keycodigo FROM tipo_moneda WHERE is_moneda_secundaria=1 AND is_activo=1');
			//verificamos que existe el registro para evitar error al momento de ejecuatar
			if(isset($tipoMoneda[0]->keycodigo)){
				$codTipoMoneda = $tipoMoneda[0]->keycodigo;
			}
			if(isset($dolar[0]->tasa_segunda_actualizacion)){
				$conexionSQL->update("update tipo_moneda_historial_tasa set nueva_tasa_de_cambio_en_moneda_nacional=? where fecha=?",[$tasa,$fecha]);
			}else{
				
				for($i=0;$i<=$ndias;){
					$fecha_actual = date('Y-m-d', strtotime($fecha . ' +'.$i.' day'));
					$conexionSQL->insert("INSERT INTO tipo_moneda_historial_tasa (codtipomoneda,nueva_tasa_de_cambio_en_moneda_nacional,fecha) VALUES	(?,?,?)",[$codTipoMoneda,$tasa,$fecha_actual]);
					$i++;
				}
			}
		}

	}

	public function conexionDinamicaBD($conexion) {
		//metodo que genera una conexion dimnamica de la base de datos.
		//buscamos los datos de las distintas conexiones guardadas en la tabla farmacia.
		$datosConexion = DB::connection('mysql')
			->select('select * from empresas where basedata=:conexion',[$conexion]);

		//llenamos el arreglo con los datos de conexion	
		if(empty($conexion)){
			dd('Verifique en la tabla empresa si esta lleno el campo que pertenece al nombre d ela base de datos porque no hay conexion con la base de datos');
		}
		$configDb = [
			'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => $datosConexion[0]->servidor,
            'port' => $datosConexion[0]->puerto,
            'database' => $datosConexion[0]->basedata,
            'username' => $datosConexion[0]->nomusua,
            'password' => $datosConexion[0]->clave,
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,            
	    ];
	    //configuramos un datos temporales de conexion
	    \Config::set('database.connections.DB_Serverr', $configDb);	
		
		$conexionSQL = \DB::connection('DB_Serverr');
			
		return $conexionSQL;		
	    	    
	}

	

	public static function minusculaMayuscula($var){
		if (isset($var)){
			$$valo = $var;
			
		}
	}

	public function sumarDiasAFecha($fecha,$dias){
		$fechaPago = strtotime($fecha."+ ".$dias." days");
    	return date('Y-m-d',$fechaPago);

	}

	public function diferenciaEntreFechas($fechaInicio,$fechaFin){

		$fechaPago = new DateTime($fechaInicio);
		$fechaActual = new DateTime($fechaFin);
		$diferencia = $fechaPago->diff($fechaActual);
		$dias = $diferencia->days;
		
		/* $diferencia = abs(strtotime($fechaFin)-strtotime($fechaInicio));

		 $years = floor($diferencia / (365*60*60*24));
		
		 $months = floor(($diferencia - $years * 365*60*60*24) / (30*60*60*24));
		
		 $days = floor(($diferencia - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24)); */

		 return $dias;
	}

	public function consultarMonedaBase(){
		$monedaBase="nacional";
		if(!empty(session('basedata'))){
			
			$configuracion =  Parametro::buscarVariable('base_datos_tipo_moneda');
			if(empty($configuracion)){
				Parametro::actualizarVariable('base_datos_tipo_moneda','tipo_moneda_vealo');
				$tipoMonedas = DB::select("SELECT * FROM tipo_moneda WHERE is_moneda_base=1 ");
			}else{
				if($configuracion=='tipo_moneda_vealo'){
					$tipoMonedas = DB::select("SELECT * FROM tipo_moneda WHERE is_moneda_base=1 ");
				}
				if($configuracion == 'tipo_moneda_siace'){
					$conexionSQL = self::conexionDinamicaBD(session('basedata'));
					$tipoMonedas = $conexionSQL->select("SELECT * FROM tipo_moneda WHERE is_moneda_base=1 ");
				}
				
			}
			
			foreach($tipoMonedas as $tipoMoneda){
				
				if($tipoMoneda->is_nacional == 1){
					$monedaBase="nacional";
				}else{
					$monedaBase="extranjera";
				}
			}
			
			return $monedaBase;
		}
		dd("Error no se pudo conectar con el siace para determinar la moneda base de dicho sistema, herraminetasController linea 340");
	}
	
	public static function valorAlCambioMonedaSecundaria($monto,$tasa=0){
		//este metodo convierte el monto de bolivares en divisa identificando el tipo de moneda del siace, 
		//porque si es el dolar la moneda base hace la conversion en bolivares.
		//si la moneda base es nacional se divide el monto / divisas en caso contrario monto * divisa
		$monto = floatval($monto);
		$tasa = floatval($tasa);
		
		
		if(!empty(session('basedata'))){
			$conversion = 0;
			$configuracion =  Parametro::buscarVariable('base_datos_tipo_moneda');
			if(empty($configuracion)){
				Parametro::actualizarVariable('base_datos_tipo_moneda','tipo_moneda_vealo');
				$tipoMonedas = DB::select("SELECT * FROM tipo_moneda WHERE is_moneda_base=1 ");
			}else{
				if($configuracion=='tipo_moneda_vealo'){
					$tipoMonedas = DB::select("SELECT * FROM tipo_moneda WHERE is_moneda_base=1 ");
				}
				if($configuracion == 'tipo_moneda_siace'){
					$conexionSQL = self::conexionDinamicaBD(session('basedata'));
					$tipoMonedas = $conexionSQL->select("SELECT * FROM tipo_moneda WHERE is_moneda_base=1 ");
				}
				
			}
			
			foreach($tipoMonedas as $tipoMoneda){
				
				if($tipoMoneda->is_nacional == 1){
					$conversion =  $monto/$tasa;
				}else{
					$conversion = $monto*$tasa;
				}
			}
			
			return $conversion;
		}
		dd("Error no se pudo conectar con el siace para determinar la moneda base de dicho sistema, herraminetasController linea 340");
	}

	public function cotizacionTasa(){
		//muestra la vista de la cotizacion de las tasas
		return view('cuentasPorPagar.cotizacionTasa');
	}
}
