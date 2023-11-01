<?php

namespace App\Http\Controllers\InformesAdicionales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Herramientas\HerramientasController;
use App\Models\Parametro;
use App\Models\Empresa;

class HabladoresController extends Controller
{
    private $herramientas='';
    function __construct(){
        
        $this->herramientas = new HerramientasController();
    }
        /* $herramientas = new HerramientasController();
    	$conexionSQL = $herramientas->conexionDinamicaBD($empresa[1]); 
        $registros = $conexionSQL->select("SELECT * from cxp where codorigen=2000 and documento=:nfactura and year(fecha)>=:anioAnterior order by keycodigo",[$nFactura,$anioAnterior]);*/
        
    
    public function index(){        
    	
        $listasPersonalizada = DB::select("SELECT * FROM habladores WHERE listado <> '' and empresa_rif=:empresaRif GROUP BY listado",['empresaRif'=>session('empresaRif')]);

        return view('informesAdicionales.habladores.index',['listasPersonalizada'=>$listasPersonalizada,'empresas'=>$this->herramientas->listarEmpresas()]);
    }

    
    public function cambiarTipoProductoParaCrearLista(Request $campos){
        return self::crearLista($campos->tipo_producto);
    }
    public function cambiarTipoProductoParaEditarLista(Request $campos){
        return self::editarLista($campos->listado,$campos->tipo_producto);
    }
    
    public function crearLista($tipoProducto='1'){
        $conexionSQL = $this->herramientas->conexionDinamicaBD(session('basedata'));
        $tiposProductos = $conexionSQL->select("SELECT keycodigo, nombre from tipoproducto");
        $listasCreadas = DB::select("SELECT codprod, listado from habladores WHERE empresa_rif=:empresaRif",[session('empresaRif')]);        
        $productos = $conexionSQL->select("select codprod,costo,precio,nombre,tipoIva,tipoproducto,linea,sublinea from productos where stock>0 and codtipoproducto=:tipoproducto",[$tipoProducto]);
        
        foreach ($productos as $producto){
            $lista = '';
            foreach ($listasCreadas as $productosEnLista){
                if($producto->codprod == $productosEnLista->codprod){
                    $lista = $lista.' | '.$productosEnLista->listado;
                }
            }

            $productoNuevo = array('codprod'=>$producto->codprod,'costo'=>$producto->costo,'precio'=>$producto->precio,'nombre'=>$producto->nombre,'tipoiva'=>$producto->tipoIva,'tipoproducto'=>$producto->tipoproducto,'listas'=>$lista,'linea'=>$producto->linea,'sublinea'=>$producto->sublinea);
            $todosLosProductosNuevos[]=(object)$productoNuevo;
        }
        session(['nombreListaHablador'=>'']);
        return view('informesAdicionales.habladores.crearLista',['productos'=>$todosLosProductosNuevos,'empresas'=>$this->herramientas->listarEmpresas(),'tiposProductos'=>$tiposProductos,'tipoProductoSelec'=>$tipoProducto]);
    }

    public function editarLista($listado,$tipoProducto='1'){
        
        $conexionSQL = $this->herramientas->conexionDinamicaBD(session('basedata'));
        $tiposProductos = $conexionSQL->select("SELECT keycodigo, nombre from tipoproducto");
        $listasCreadas = DB::select("SELECT codprod, listado from habladores WHERE empresa_rif=:empresaRif",[session('empresaRif')]);        
        $productos = $conexionSQL->select("select codprod,costo,precio,nombre,tipoIva,tipoproducto,linea,sublinea from productos where stock>0 and codtipoproducto=:tipoproducto",[$tipoProducto]);
        
        foreach ($productos as $producto){
            $lista = '';
            foreach ($listasCreadas as $productosEnLista){
                if($producto->codprod == $productosEnLista->codprod){
                    $lista = $lista.'-'.$productosEnLista->listado;
                }
            }

            $productoNuevo = array('codprod'=>$producto->codprod,'costo'=>$producto->costo,'precio'=>$producto->precio,'nombre'=>$producto->nombre,'tipoiva'=>$producto->tipoIva,'tipoproducto'=>$producto->tipoproducto,'listas'=>$lista,'linea'=>$producto->linea,'sublinea'=>$producto->sublinea);
            $todosLosProductosNuevos[]=(object)$productoNuevo;
        }
        session(['nombreListaHablador'=>'']);
        return view('informesAdicionales.habladores.editarLista',['productos'=>$todosLosProductosNuevos,'empresas'=>$this->herramientas->listarEmpresas(),'tiposProductos'=>$tiposProductos,'tipoProductoSelec'=>$tipoProducto,'listado'=>$listado]);
    }

    public function guardarListaCreada(Request $request){
        if(!empty($request->codprod)){
            foreach($request->codprod as $producto){
                DB::insert(" INSERT INTO habladores (empresa_rif,codprod,listado) VALUES (?,?,?)",[session('empresaRif'),$producto,$request->nombre_lista]);
            }
            return self::index();
        }else{
            \Session::flash('message', 'Disculpe no seleccionó ningun producto para crear la lista de habladores, seleccione los productos haciendo click sobre ellos e ingrese el nombre que tendra dicha lista');
            return redirect('/informes/crear-habladores');
        }
        
    }

    public function listarHabladores($nombreDeLaLista){
        //despliega la lista de los productos seleccionado en la lista de habladores
        //esta vista tiene el check para seleccionar los que se van a imprimir y la moneda en que se va a imprimir
        session(['nombreListaHablador'=>$nombreDeLaLista]);
        $hablador=array();
        $habladores = array();
        $conexionSQL = $this->herramientas->conexionDinamicaBD(session('basedata'));
        $precio = 0;
        $montoDivisa=0;
        
        //consultamos la lista de habladores ya registrada en la tabla habladores del vealo
        $listaHabladores = DB::select("SELECT * FROM habladores where empresa_rif =:empresaRif AND listado=:listado",[session('empresaRif'),$nombreDeLaLista]);
        
        //buscamos el valor del iva
        $datosIva = $conexionSQL->select('SELECT iva FROM areas');
        $iva = $datosIva[0]->iva;
        
        //Valor de la moneda secundaria
        $tipoMonedas = $conexionSQL->select('SELECT nombre_singular,abreviatura,precio_venta_moneda_nacional,is_moneda_base,is_moneda_secundaria FROM tipo_moneda WHERE is_moneda_secundaria =1 OR is_moneda_base = 1 order by  is_moneda_secundaria desc');
        foreach($tipoMonedas as $moneda){
            if($moneda->is_moneda_secundaria == 1){
                $divisa =  $moneda;
            }
        }        

        //por cada hablador buscamos los datos del producto porque en habladores solo esta es el codigo
        foreach($listaHabladores as $datoslista){
            $productos = $conexionSQL->select("SELECT nombre,costo,precio,tipoIva,stock FROM productos WHERE codprod=:codigoproducto and stock >0 ",[$datoslista->codprod]);
            
            foreach($productos as $producto){
                $tipoIva=trim($producto->tipoIva);

                //verificamos si el producto tiene iva
                if($tipoIva=='NORMAL'){
                    //BUSCAMOS EL VALOR DEL IVA SI EL PRODUCTO TIENE IVA
                    $precio = $producto->precio + (($producto->precio * $iva)/100);
                    $tipoIva='con IVA';
                }else{
                    $precio = $producto->precio;
                    $tipoIva='EXENTO';
                }

                $montoDivisa = $precio/$divisa->precio_venta_moneda_nacional;
                $hablador= array('id'=>$datoslista->id,'codprod'=>$datoslista->codprod,'tipoIva'=>$tipoIva,'nombre'=>$producto->nombre,'precio'=>$precio,'divisa'=>$montoDivisa,'abreviaturaMonedaSecundaria'=>$divisa->abreviatura);
                $tipoIva='';                               
            }
            $habladores[] = $hablador;
            $hablador = array();
        }
       
        return view('informesAdicionales.habladores.listaHabladores',['nombreDeLaLista'=>$nombreDeLaLista,'habladores'=>$habladores,'tipoMonedas'=>$tipoMonedas,'empresas'=>$this->herramientas->listarEmpresas()]);
    }

    public function imprimirHabladores(Request $request){
    //se carga el archivo con los habladores es la vista final y de alli se puede imprimir la pagina o gaurdar en pdf
    //aca salen cuadraditos
        $igtf = $request->igtf;
        $porcentajeIGTF = 0;
        $monto=0;
        $hablador=array();
        $habladores=array();
        $archivo = "";
        $porcentajeIGTF = Parametro::buscarVariable('igtf'); 
        $Empresa = Empresa::where('rif',session('empresaRif'))->first();
        $logoEmpresa = $Empresa->logo;
        if(empty($request->habladores)){
            \Session::flash('message', 'Disculpe no seleccionó ningun producto para generar habladores');
            return redirect('/informes/listar-habladores/'.$request->nombre_lista);
            
        }

        //recorremos el arreglo del checkbox y lo almacenamos en otro especificando cada campo 
        //verificamos si es en moneda nacional y o divisas para cambiar el monto y el simbolo
        
        foreach($request->habladores as $listadoHablador){
            $datoshablador = explode('|',$listadoHablador);
            
            if($request->moneda_base==1){
                //moneda nacional
                $hablador = array('codprod'=>$datoshablador[0],'nombre'=>$datoshablador[1],'precio'=>$datoshablador[2],'tipoMoneda'=>'Bs.');
            }else{
                //moneda extranjera
                if($igtf=='on'){
                    $monto = $datoshablador[3]+(($datoshablador[3]*$porcentajeIGTF)/100);
                    $hablador = array('codprod'=>$datoshablador[0],'nombre'=>$datoshablador[1],'precio'=>$monto,'tipoMoneda'=>'Ref.');
                }else{
                    $hablador = array('codprod'=>$datoshablador[0],'nombre'=>$datoshablador[1],'precio'=>$datoshablador[3],'tipoMoneda'=>'Ref.');
                    
                }
                
            }    
                
            
            $habladores[]=$hablador;
        }
            switch($request->tamanio){
                case "s":
                    $archivo = "informesAdicionales.habladores.archivoPDFimprimirS";
                break;
                case "m":
                    $archivo = "informesAdicionales.habladores.archivoPDFimprimirM";
                break;
                case "xl":
                    $archivo = "informesAdicionales.habladores.archivoPDFimprimirXL";
                break;    
            }    
            
        return view($archivo,['habladores'=>$habladores,'porcentajeIGTF'=>$porcentajeIGTF,'logoEmpresa'=>$logoEmpresa]);
        
    }

    public function eliminarListaHabladores($lista){
        DB::delete('DELETE FROM habladores WHERE listado=? and empresa_rif=?',[$lista,session('empresaRif')]);
        return redirect('/informes/habladores');
    }

    public function eliminarProductoDeListadoHablador($lista,$id){
        DB::delete('DELETE FROM habladores WHERE id=? ',[$id]);
        return self::listarHabladores($lista);
    }
}
