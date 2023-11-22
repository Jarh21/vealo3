<?php

namespace App\Http\Controllers\AsistenteCompras;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Herramientas\HerramientasController;
use App\Models\AsistenteCompraDetalle;
use App\Http\Controllers\Admin\EmpresasController;
use Illuminate\Support\Facades\DB;
/* use Maatwebsite\Excel\Facades\Excel; */
# Indicar que usaremos el IOFactory
use PhpOffice\PhpSpreadsheet\IOFactory;

class AsistenteComprasController extends Controller
{
    private $empresas = '';
    private $herramientas='';
 
    public function __construct(){
        /*
        buscamos las empresas registradas y sus datos para la vista
        */
        $this->middleware('auth');
        $this->herramientas = new HerramientasController();
        
       
    }

    public function index(){
        
        return view('asistenteCompras.visualizadorPrecios');
    }

    public function descargarExcel($nombreDrogueria='Drolanca',$farmaciaId='2'){
        $rutaArchivoDestino = public_path()."\cargado.xlsx";
        $rutaArchivo='';
        $conexionSQL = $this->herramientas->conexionDinamicaBD('hptal');
        //acceder a un archivo fuera del proyecto
        //recorremos la carpeta y copiamos todos los archivos para copiarlos

        $from  = base_path('../../../RESPALDO_BASE_DATOS_ADMON/lista_precio_proveedores');
        $to = public_path('/droguerias');

        //Abro el directorio que voy a leer
        $dir = opendir($from);

        //Recorro el directorio para leer los archivos que tiene
        while(($file = readdir($dir)) !== false){
            //Leo todos los archivos excepto . y ..
            if(strpos($file, '.') !== 0){
                //Copio el archivo manteniendo el mismo nombre en la nueva carpeta
                copy($from.'/'.$file, $to.'/'.$file);
            }
        }

        
        
        //buscamos el archivo correspondiente a la drogueria
       // $datosArchivo = DB::connection('slave')->select("SELECT nombre_archivo FROM relacion_proveedores WHERE nombre=:nombreDrogueria",['nombreDrogueria'=>$nombreDrogueria]);
        $datosArchivo = $conexionSQL->select("SELECT nombre_archivo FROM relacion_proveedores WHERE nombre=:nombreDrogueria",['nombreDrogueria'=>$nombreDrogueria]);
               
        foreach($datosArchivo as $archivo){
            $rutaArchivo = public_path('droguerias/').$archivo->nombre_archivo;
        }
        
        
        
        

        $documento = IOFactory::load($rutaArchivo);
        $hojaActual = $documento->getSheet(0);
        //buscamos los registros de la base de datos a guardar en el archivo
        //$detallesCompras = DB::connection('slave')->select("SELECT a.archivo_pedido_id,a.drogueria,a.id_farmacia,a.cantidad,a.coordenadas_archivo,r.nombre_archivo FROM valores_pred v, asistente_compras_detallado a,relacion_proveedores r WHERE a.archivo_pedido_id = v.id_pedido AND a.drogueria COLLATE utf8mb4_general_ci = r.nombre AND a.drogueria=:drogueria AND a.id_farmacia=:farmaciaId",['drogueria'=>$nombreDrogueria,'farmaciaId'=>$farmaciaId]);
        $detallesCompras = $conexionSQL->select("SELECT a.archivo_pedido_id,a.drogueria,a.id_farmacia,a.cantidad,a.coordenadas_archivo,r.nombre_archivo FROM valores_pred v, asistente_compras_detallado a,relacion_proveedores r WHERE a.archivo_pedido_id = v.id_pedido AND a.drogueria COLLATE utf8mb4_general_ci = r.nombre AND a.drogueria=:drogueria AND a.id_farmacia=:farmaciaId",['drogueria'=>$nombreDrogueria,'farmaciaId'=>$farmaciaId]);
        foreach($detallesCompras as $detalles){
            $hojaActual->setCellValue($detalles->coordenadas_archivo,$detalles->cantidad);
        }
        
        $guardar = IOFactory::createWriter($documento,"Xlsx");
        $guardar->save($rutaArchivoDestino);
        
        
        //$coordenadas = "A1";
        # Lo que hay en A1
        //$celda = $hojaActual->getCell($coordenadas);
        //$celda = $hojaActual->getCellByColumnAndRow(1,1);
        # El valor, así como está en el documento
        //$valorRaw = $celda->getValue();
        
        
    }

    public function apiListadoPrecioDrogueria(){
        $conexionSQL = $this->herramientas->conexionDinamicaBD('hptal');
        $preciosDroguerias = $conexionSQL->select("select * from visualizador_precios_drogueria_descripcion order by costo,drogueria");
        
        /* return datatables($preciosDroguerias)
        ->addColumn('btn','asistenteCompras.acciones')//agregamos una columna y va a contener la vista que se encuentra en asistenteCompras.acciones esto es como una plantilla
        ->rawColumns(['btn'])//para que renderice(interpretar) el html
        ->toJson(); */
        return $preciosDroguerias;
    }

    public function apiListarRegistrosAsistenteCompra($visualizadorId){
        $datos = DB::select("SELECT a.cantidad, e.nom_corto FROM asistente_compras_detalles a,empresas e WHERE a.empresa_rif COLLATE utf8mb4_unicode_ci = e.rif AND a.visualiador_precio_drogueria_id=:keycodigo",["keycodigo"=>$visualizadorId]);
       // $datos = DB::select("SELECT a.cantidad FROM asistente_compras_detalles a WHERE a.visualiador_precio_drogueria_id=:keycodigo",["keycodigo"=>$visualizadorId]);
        return $datos;
    }

    public function guardarPedidoDetallado(Request $request){
        $objEmpresas = new EmpresasController();
        $empresas = $objEmpresas->listarEmpresasApi();
        $empresas = json_decode($empresas,true);
        $contador = 0;

        foreach($request->cantidad as $valor ){
            if(empty($valor)){
                $contador ++;
            }else{
                $compras = new AsistenteCompraDetalle();
                $compras->producto = $request->producto;
                $compras->empresa_rif =$empresas[$contador]['rif'];
                $compras->cantidad = $valor;
                $compras->drogueria = $request->drogueria;
                $compras->costo = $request->costo;
                $compras->visualiador_precio_drogueria_id = $request->visualizadorPrecioId;
                $compras->save();
                $contador ++;
            }
            
        }
        
        
    }

    public function buscarArchivosListaPrecioProveedores(){
        $directorio = "C:/RESPALDO_BASE_DATOS_ADMON/lista_precio_proveedores/";
        $tables = array();
	        $n=0;
	        //recorremos el directorio
	        if($carpeta = opendir($directorio)){
	        	while(false !== ($file = readdir($carpeta))){
	        		//por razones desconocidas al buscar los archivos en el directorio sale(. , ..) al inicio y lo toma como archivo
	        		if(($file <> '.') and ($file <> '..')){ 
	        			$tables[]=$file;//guardamos los nombres de los archivos en un array
	        		}
	        	}

	        	closedir($carpeta);//cerramos el directorio
	        }
    }
}
