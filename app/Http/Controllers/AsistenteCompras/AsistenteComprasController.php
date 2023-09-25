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

    public function descargarExcel(){
        $rutaArchivoDestino = public_path()."\PLUS_MEDICAL_cargado.xlsx";
        if(file_exists($rutaArchivoDestino)){
            
            unlink($rutaArchivoDestino);
        }

        $rutaArchivo = public_path()."/PLUS_MEDICAL.xlsx";
        $documento = IOFactory::load($rutaArchivo);
        $hojaActual = $documento->getSheet(0);
        $hojaActual->setCellValue('L32','10');
        $hojaActual->setCellValue('L33','11');
        $hojaActual->setCellValue('L34','1');
        $hojaActual->setCellValue('L35','13');
        $hojaActual->setCellValue('L36','14');
        $hojaActual->setCellValue('L37','20');
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
}
