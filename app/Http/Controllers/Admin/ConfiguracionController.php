<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Models\Parametro;
use App\Models\Empresa;

class ConfiguracionController extends Controller
{
    public function indexConfiguracionCuentasPorPagar(){
        $pago_facturas_desde_facturas_por_pagar = Parametro::buscarVariable('pago_facturas_desde_facturas_por_pagar');
        $verificar_facturas_en_siace = Parametro::buscarVariable('verificar_facturas_en_siace');
        $verificar_tasa_dolar_tipo_moneda_o_historial_dolar = Parametro::buscarVariable('verificar_tasa_dolar_tipo_moneda_o_historial_dolar');        
        $base_datos_tipo_moneda = Parametro::buscarVariable('base_datos_tipo_moneda');
        $importar_server2_a_server1_cxp = Parametro::buscarVariable('importar_server2_a_server1_cxp');
        $numero_registros_importar_cxp = Parametro::buscarVariable('numero_registros_importar_cxp');
        $numero_registros_importar_notacredito = Parametro::buscarVariable('numero_registros_importar_notacredito');
        return view('configuraciones.confCuentasPorPagar',
        [
            'pago_facturas_desde_facturas_por_pagar'=>$pago_facturas_desde_facturas_por_pagar,
            'verificar_facturas_en_siace'=>$verificar_facturas_en_siace,
            'verificar_tasa_dolar_tipo_moneda_o_historial_dolar'=>$verificar_tasa_dolar_tipo_moneda_o_historial_dolar,
            'base_datos_tipo_moneda'=>$base_datos_tipo_moneda,
            'importar_server2_a_server1_cxp'=>$importar_server2_a_server1_cxp,
            'numero_registros_importar_cxp'=>$numero_registros_importar_cxp,
            'numero_registros_importar_notacredito'=>$numero_registros_importar_notacredito,
        ]);
    }

    public function guardarConfiguracionCuentasPorPagar(Request $request){
        $bandera=0;
        $pago_facturas_desde_facturas_por_pagar            = $request->pago_facturas_desde_facturas_por_pagar;
        $verificar_tasa_dolar_tipo_moneda_o_historial_dolar=$request->verificar_tasa_dolar_tipo_moneda_o_historial_dolar;
        $conversion_moneda_nacional_a_extranjera           = $request->conversion_moneda_nacional_a_extranjera;
        $verificar_facturas_en_siace                       = $request->verificar_facturas_en_siace;
        $base_datos_tipo_moneda                            = $request->base_datos_tipo_moneda;
        $importar_server2_a_server1_cxp                    = $request->importar_server2_a_server1_cxp;
        $numero_registros_importar_cxp                     = $request->numero_registros_importar_cxp;
        $numero_registros_importar_notacredito             = $request->numero_registros_importar_notacredito; 
        Parametro::actualizarVariable('base_datos_tipo_moneda',$base_datos_tipo_moneda);  


        if($pago_facturas_desde_facturas_por_pagar=='on'){
            $bandera=1;
        }else{
            $bandera=0;
        }
        
        Parametro::actualizarVariable('pago_facturas_desde_facturas_por_pagar',$bandera);
        $bandera=0;

        if($verificar_facturas_en_siace=='on'){
            $bandera=1;
        }else{
            $bandera=0;
        }
        Parametro::actualizarVariable('verificar_facturas_en_siace',$bandera);
        $bandera=0;
       
        Parametro::actualizarVariable('verificar_tasa_dolar_tipo_moneda_o_historial_dolar',$verificar_tasa_dolar_tipo_moneda_o_historial_dolar);

        Parametro::actualizarvariable('conversion_moneda_nacional_a_extranjera',$conversion_moneda_nacional_a_extranjera);

        if($importar_server2_a_server1_cxp=='on'){
            $bandera=1;
        }else{
            $bandera=0;
        }
        Parametro::actualizarVariable('importar_server2_a_server1_cxp',$bandera);  
        Parametro::actualizarVariable('numero_registros_importar_cxp',$numero_registros_importar_cxp);
        Parametro::actualizarVariable('numero_registros_importar_notacredito',$numero_registros_importar_notacredito);
        return self::indexConfiguracionCuentasPorPagar();
    }

    public function configuracionGeneral(){
        $nombreEmpresa = Parametro::buscarVariable('nombre_general_empresa');
        $logoEmpresa = Parametro::buscarVariable('logo_empresa');
        
        return view('configuraciones.confGeneral',['nombreEmpresa'=>$nombreEmpresa,'logoEmpresa'=>$logoEmpresa]);
    }

    public function guardarConfiguracionGeneral(Request $request){

        Parametro::actualizarVariable('nombre_general_empresa',$request->nombre_general_empresa);
        //si hay una imagen
        if($request->hasfile('logo_empresa')){

			$file = $request->file('logo_empresa');
			$destinatinoPath ='imagen/';
			$filename = time().'-'.$file->getClientOriginalName();
			$uploadsuccess = $request->file('logo_empresa')->move($destinatinoPath,$filename);    
			
            Parametro::actualizarVariable('logo_empresa',$destinatinoPath.$filename);

		}
        return self::configuracionGeneral();
    }

    public function sincorinzarServidoresTraerUltimosRegistros(){
        $empresaRif = session('empresaRif');
        $datosEmpresa = Empresa::where('rif',$empresaRif)->first();
        if(!empty($datosEmpresa->servidor2)){

            //creamos la conexion dinamica del servidor 2
            $configDb2 = [
                'driver' => 'mysql',
                'url' => env('DATABASE_URL'),
                'host' => $datosEmpresa->servidor2,
                'port' => $datosEmpresa->puerto2,
                'database' => $datosEmpresa->basedata2,
                'username' => $datosEmpresa->nomusua2,
                'password' => $datosEmpresa->clave2,
                'unix_socket' => env('DB_SOCKET', ''),
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix' => '',
                'prefix_indexes' => true,
                'strict' => false,
                'engine' => null,            
            ];
            //configuramos un datos temporales de conexion
            \Config::set('database.connections.DB_Serverr2', $configDb2);	            
            $conexionSQL2 = \DB::connection('DB_Serverr2');            
            //fin conexion del servidor 2

            //creamos la conexion dinamica del servidor 1
            $configDb1 = [
                'driver' => 'mysql',
                'url' => env('DATABASE_URL'),
                'host' => $datosEmpresa->servidor,
                'port' => $datosEmpresa->puerto,
                'database' => $datosEmpresa->basedata,
                'username' => $datosEmpresa->nomusua,
                'password' => $datosEmpresa->clave,
                'unix_socket' => env('DB_SOCKET', ''),
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix' => '',
                'prefix_indexes' => true,
                'strict' => false,
                'engine' => null,            
            ];
            //configuramos un datos temporales de conexion
            \Config::set('database.connections.DB_Serverr', $configDb1);	            
            $conexionSQL1 = \DB::connection('DB_Serverr');
            //fin conexion del servidor 1
            //buscamos el ultimo keycodigo de cxp en el servidor donde se esta 
            $datosServer1 = $conexionSQL1->select("SELECT keycodigo FROM cxp ORDER BY keycodigo DESC LIMIT 1");
            if(!empty($datosServer1)){
                $ultimoKeycodigo = $datosServer1[0]->keycodigo;
                
                //buscamos todos los registros de la farmacia para luego trerlos al servidor donde se van a trabajar en este caso administracion en calabozo
                $datosServer2 = $conexionSQL2->select("SELECT cxp.keycodigo, cxp.ncontrol, cxp.cierre, cxp.rif, cxp.FECHA, cxp.dias_credito, cxp.VCTO, cxp.nomprov, cxp.DOCUMENTO, cxp.CONCEPTO, cxp.debitos, cxp.creditos, cxp.poriva, cxp.montoiva, cxp.gravado, cxp.exento, cxp.codusua, cxp.usuario, cxp.equipo, cxp.cerrado, cxp.codorigen, IF(notacredito.keycodigo IS NULL,0,notacredito.keycodigo) AS notacredito_keycodigo,notacredito.creditos AS notacredito_creditos,notacredito.montoiva AS notacredito_montoiva  FROM cxp LEFT JOIN notacredito ON cxp.keycodigo = notacredito.codfact WHERE cxp.keycodigo >:ultimoKeycodigo ",[$ultimoKeycodigo]);
                
                foreach($datosServer2 as $ultimasFacturas){
                    //buscamos la factura y con un join en nota credito por si lo tiene
                    $conexionSQL1->insert("INSERT INTO cxp (keycodigo, nomprov, rif, FECHA, dias_credito, VCTO, cierre, CONCEPTO, documento, ncontrol,codorigen, debitos, creditos, poriva,montoiva,gravado, exento, codusua, usuario, equipo, cerrado) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",[$ultimasFacturas->keycodigo,$ultimasFacturas->nomprov,  $ultimasFacturas->rif, $ultimasFacturas->FECHA, $ultimasFacturas->dias_credito, $ultimasFacturas->VCTO, $ultimasFacturas->cierre, $ultimasFacturas->CONCEPTO, $ultimasFacturas->DOCUMENTO, $ultimasFacturas->ncontrol, $ultimasFacturas->codorigen, $ultimasFacturas->debitos, $ultimasFacturas->creditos, $ultimasFacturas->poriva,  $ultimasFacturas->montoiva, $ultimasFacturas->gravado, $ultimasFacturas->exento, $ultimasFacturas->codusua, $ultimasFacturas->usuario, $ultimasFacturas->equipo, $ultimasFacturas->cerrado]);
                    
                    if($ultimasFacturas->notacredito_keycodigo > 0){
                    //si la factura tiene una nota de credito la insertamos en la tabla notacredito
                        $conexionSQL1->insert("INSERT INTO notacredito (keycodigo,creditos,montoiva) value (?,?,?)",[$ultimasFacturas->notacredito_keycodigo,$ultimasFacturas->notacredito_creditos,$ultimasFacturas->notacredito_montoiva]);
                    }
                }

                \Session::flash('message', 'Se importaron '.count($datosServer2).' Registros');
                return redirect("/cuentasporpagar/facturasPorPagar");
            }else{
                dd("no existe ningun registro previo en cxp de la base de datos del servidor principal del vealo, se requiere para saber el ultimo keycodigo registrado");
            }
        }
    }

    public function sincronizarVealoLocal_VealoRemoto(){
        //Este metodo se encarga de traer los datos de un vealo remoto a un vealo local
        //este consulta cuakles son las tabalas mas importantes las cuales estan especificado en la tabla tablas_sincronizar_vealo_remoto
        //se consultan las empresas autorizadas para hacer dicha tare ya que se repetira por empresa y por cada tabla espècificada
        //se seleccionan los ultimos id de la tabla destino para traer los que faltan, una vez optenidos los datos  de la tabla origen se insertan en la tabla destino
        //$empresas[] = session('empresaRif');

        //verificamos si empresaRif esta vacia, esto indica que no se esta ejecutando manualmente, sino por tarea
        //si se ejecuta por tarea entonces se usa los parametrso en el siguiente arreglo.
        
            $objEmpresas = Empresa::where('is_sincronizacion_remota',1)->get();
            //$nRegistroscxp = Parametro::buscarVariable('numero_registros_importar_cxp');
            //$nRegistrosNotacredito = Parametro::buscarVariable('numero_registros_importar_notacredito');

            //creamos las conexiones dinamicas
            foreach($objEmpresas as $datosEmpresa){
                $empresas[]=$datosEmpresa->rif;
                                
                if(!empty($datosEmpresa->servidor2)){

                    //creamos la conexion dinamica del servidor 2
                    $configDb2 = [
                        'driver' => 'mysql',
                        'url' => env('DATABASE_URL'),
                        'host' => $datosEmpresa->servidor2,
                        'port' => $datosEmpresa->puerto2,
                        'database' => $datosEmpresa->basedata2,
                        'username' => $datosEmpresa->nomusua2,
                        'password' => $datosEmpresa->clave2,
                        'unix_socket' => env('DB_SOCKET', ''),
                        'charset' => 'utf8',
                        'collation' => 'utf8_unicode_ci',
                        'prefix' => '',
                        'prefix_indexes' => true,
                        'strict' => false,
                        'engine' => null,            
                    ];
                    //configuramos un datos temporales de conexion
                    \Config::set('database.connections.'.$datosEmpresa->rif.'DB_Serverr2', $configDb2);	            
                    $conexionSQL2 = \DB::connection($datosEmpresa->rif.'DB_Serverr2');     
                    //llenamos el arreglo de conexiones       
                     $conexiones[$datosEmpresa->rif.'con2']=$conexionSQL2;
                    //fin conexion del servidor 2
                   
                    //creamos la conexion dinamica del servidor 1
                    /* $configDb1 = [
                        'driver' => 'mysql',
                        'url' => env('DATABASE_URL'),
                        'host' => $datosEmpresa->servidor,
                        'port' => $datosEmpresa->puerto,
                        'database' => $datosEmpresa->basedata,
                        'username' => $datosEmpresa->nomusua,
                        'password' => $datosEmpresa->clave,
                        'unix_socket' => env('DB_SOCKET', ''),
                        'charset' => 'utf8',
                        'collation' => 'utf8_unicode_ci',
                        'prefix' => '',
                        'prefix_indexes' => true,
                        'strict' => false,
                        'engine' => null,            
                    ];
                    //configuramos un datos temporales de conexion
                    \Config::set('database.connections.'.$datosEmpresa->rif.'DB_Serverr', $configDb1);	            
                    $conexionSQL1 = \DB::connection($datosEmpresa->rif.'DB_Serverr');
                    //llenamos el arreglo de conexiones 
                    $conexiones[$datosEmpresa->rif.'con1']=$conexionSQL1; */
                }else{
                        dd("Erorr no existe servidor 2 en los datos de la empresa");                    
                }
            }            
       
        foreach($empresas as $empresaRif){

            if(!empty($datosEmpresa->servidor2)){
                $ultimoKeycodigo=0;
            
                $tablas = DB::select("select nombre_tabla from tablas_sincronizar_vealo_remoto");
                foreach($tablas as $tabla){
                    $ultimoKeycodigo=0;
                    //buscar ultimo keycodigo de la tabla local para buscarla en la tabla remoto
                    $ultimoKeycodigo = DB::select("select id from ".$tabla->nombre_tabla." order by id desc limit 1");
                    
                    foreach($ultimoKeycodigo as $ultimoId){

                        //consultamos el el servidor de vealo remoto los registro de la tabla actual los registros despues del ultimo keycodigo de la tabla local
                        //esto es para que se traiga solo los nuevos registros
                        $datosTablaServer2 = $conexiones[$empresaRif.'con2']->select("SELECT *  FROM ".$tabla->nombre_tabla." WHERE id >:nId",['nId'=>$ultimoId->id]);
                        
                        //pasamos los datos de la consulta a array porque los resultados vienen en objetos y para insertarlos en la tabla sin especificar los campos tiene que venir en un arreglo
                        $data = collect($datosTablaServer2)->map(function($x){ return (array) $x; })->toArray(); 
                        
                        //una vez optenido los ultimos registros los insertamos en la tabla local
                        DB::table($tabla->nombre_tabla)->insert($data);
                        
                    }
                    
                }
                
                           
                
            }else{
                //si no encontro datos del servidor 2
                    dd("Erorr no existe servidor 2 en los datos de la empresa");
                
            }
                    
           
        }//fin del foreach de empresas
           
    }

    public function sincorinzarServidoresTraerTodosLosRegistros(){
        
        self::sincronizarVealoLocal_VealoRemoto();
        
        $fechaHora = date('Y-m-d H:i:s');
        $fechaActualizacionServidorRemoto = Parametro::actualizarVariable('fecha_actualizacion_servidor_remoto',$fechaHora);       
        \Session::flash('message', 'Se importaron los datos faltantes de los sistemas remotos '.$fechaActualizacionServidorRemoto);
        return redirect("/cuentasporpagar/facturasPorPagar");
               
    }
}
