<?php

namespace App\Http\Controllers\InformesAdicionales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Herramientas\HerramientasController;
use Illuminate\Support\Facades\DB;
use App\Models\FacturasPorPagar;
use App\Models\Empresa;
use App\Models\ParametroCalculoComision;

class InformesAdicionalesController extends Controller
{
    public function index(){
        $herramientas = new HerramientasController();
        return view("informesAdicionales.index",['empresas'=>$herramientas->listarEmpresas()]);
    }

    public function seleccionSucursal($rifEmpresa,$vista=''){
        $empresa = Empresa::where('rif','=',$rifEmpresa)->first();
        session(['empresaNombre'=>$empresa->nombre,'empresaRif'=>$empresa->rif,'codTipoMoneda'=>3,'modoPago'=>'dolares','basedata'=>$empresa->basedata]);
        return back()->withInput();
    }

    public function comisionPorVentas($fechaini='',$fechafin=''){
        $herramientas = new HerramientasController();
        return view("informesAdicionales.comisionVentas.comisionPorVentas",['empresas'=>$herramientas->listarEmpresas(),'fechaini'=>$fechaini,'fechafin'=>$fechafin]);
    }

    public function sqlBuscarVentasDelVendedorSiace($codVendedor,$fechaini,$fechafin){
        $herramientas = new HerramientasController();
        $conexionSQL = $herramientas->conexionDinamicaBD(session('basedata'));
        $resultado =  $conexionSQL->select("            

            /* consulta por vendedor*/
            SELECT
                p.keycodigo keycodigo_siace,
                p.fecha fecha,                
                c.vendedor_codusua codigoVendedor,
                c.vendedor_nombre nombreVendedor,
                p.codclie cCliente,
                c.codgrupo cGrupo,
                c.nombre cliente,
                c.rif rif,
                p.codtipopago tipoPago,
                p.codtipomoneda tipoMoneda,
                p.monto_moneda montoMoneda,
                p.monto_base montoBase,                
                p.documento documento   
                FROM
                mov_pago_cxc AS p, 
                clientes AS c
                WHERE
                p.codclie = c.keycodigo
                AND (p.fecha BETWEEN :fechaini AND :fechafin)
                AND p.codtipopago IN(1,2,3,4,10,11,14,15)
                AND c.vendedor_codusua =:codVendedor
                
            /* fin consulta por vendedor*/           

                ",['fechaini'=>$fechaini,'fechafin'=>$fechafin,'codVendedor'=>$codVendedor]);
        
        return $resultado;
    }

    public function sqlBuscarVentasDelSiace($fechaini,$fechafin){
        $herramientas = new HerramientasController();
        $conexionSQL = $herramientas->conexionDinamicaBD(session('basedata'));
        $resultado =  $conexionSQL->select("
            SELECT
                p.keycodigo keycodigo_siace,
                p.fecha fecha,
                c.vendedor_codusua codigoVendedor,
                c.vendedor_nombre nombreVendedor,
                p.codclie cCliente,
                c.codgrupo cGrupo,
                c.nombre cliente,
                c.rif rif,
                p.codtipopago tipoPago,
                p.codtipomoneda tipoMoneda,
                SUM(p.monto_moneda) montoMoneda,
                SUM(p.monto_base) montoBase,
                ROUND(SUM(p.monto_moneda)/SUM(p.monto_base),2) tasaConversion,
                p.documento documento   
                FROM
                mov_pago_cxc AS p, 
                clientes AS c
                WHERE
                p.codclie = c.keycodigo
                AND (p.fecha BETWEEN :fechaini AND :fechafin)
                AND p.codtipopago IN(1,2,3,4,10,11,14,15)
                GROUP BY p.documento          

                ",['fechaini'=>$fechaini,'fechafin'=>$fechafin]);
        
        return $resultado;
    }

    public function sqlBuscarVentasDelVendedor($codVendedor,$fechaini,$fechafin){
        
        $ventas = DB::select("select * from mov_pago_cxc_comision_ventas where codigoVendedor=:codVendedor and fecha >=:fechaini and fecha <=:fechafin and empresa_rif=:rifEmpresa",[$codVendedor,$fechaini,$fechafin,session('empresaRif')]);
        
        return $ventas;
    }

    public function sqlBuscarCambioDeVendedor($codVendedor,$fechaini,$fechafin){
        
        $ventas = DB::select("select * from mov_pago_cxc_comision_ventas where cod_vendedor_antiguo=:codVendedor and fecha >=:fechaini and fecha <=:fechafin and empresa_rif=:rifEmpresa",[$codVendedor,$fechaini,$fechafin,session('empresaRif')]);
        return $ventas;
    }

    public function sqlInsertarVentasDelSiace($datos){
        //dd($datos,'107 informe adicional controller');
       
        foreach($datos as $dato){
            DB::insert("insert into mov_pago_cxc_comision_ventas (empresa_rif,fecha,codigoVendedor,nombreVendedor,cCliente,cGrupo,cliente,rif,tipoPago,tipoMoneda,montoMoneda,montoBase,tasaConversion,documento) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?)",[
                session('empresaRif'),$dato->fecha,$dato->codigoVendedor,$dato->nombreVendedor,$dato->cCliente,$dato->cGrupo,$dato->cliente,$dato->rif,$dato->tipoPago,$dato->tipoMoneda,$dato->montoMoneda,$dato->montoBase,$dato->tasaConversion,$dato->documento
            ]);
        }
    }

    public function buscarVentasDelVendedorCalculoComision($codVendedor,$fechaini,$fechafin){
        
        $vendedores=array();
        $datosVendedor=array();
        $herramientas = new HerramientasController();
        $conexionSQL = $herramientas->conexionDinamicaBD(session('basedata'));
        //consulta con las notas de debito
       
        if(!empty($codVendedor)){
            
            //buscamos los registro en la tabla del vealo si no lo encontramos los buscamos en el siace
            $resultado = self::sqlBuscarVentasDelVendedorSiace($codVendedor,$fechaini,$fechafin);
            /* $resultado = self::sqlBuscarVentasDelVendedor($codVendedor,$fechaini,$fechafin);
            if(empty($resultado)){
                //buscamos si hubo cambios de vendedores
                $resultado = self::sqlBuscarCambioDeVendedor($codVendedor,$fechaini,$fechafin);
                if(empty($resultado)){
                         //si sigue estado vacio buscamos en el siace los datos 
                    $resultado =  self::sqlBuscarVentasDelVendedorSiace($codVendedor,$fechaini,$fechafin);
                    
                    //insertamos los datos nuevos en vealo
                    self::sqlInsertarVentasDelSiace($resultado); 
                    //volvemos a buscar en el vealo para traer los id en caso de modificar los registros
                    $resultado = self::sqlBuscarVentasDelVendedor($codVendedor,$fechaini,$fechafin);
                }else{
                    $resultado=array();
                }    
            } */ 
            
        }else{
            $resultado =  self::sqlBuscarVentasDelSiace($fechaini,$fechafin);
        }
        //detalles de las facturas cobradas
        
        foreach($resultado as $datos){
            if(empty($datos)){
                continue;
            }  
            $comision=0;
            $detalleFactura='';
            $documento='';
            $fechaCancelacion='';
              
            

            $parametrosVendedors = ParametroCalculoComision::where('codgrupo','=',$datos->cGrupo)->where('empresa_rif',session('empresaRif'))->get();
            //recorremos todas las configuraciones que tenga el codgrupo
            foreach($parametrosVendedors as $parametrosVendedor){
                    
                //buscamos en numero de las facturas
                 $documentoFactura =  $conexionSQL->select("SELECT cxc.documento,cxc.fecha,facturas.fiscalcomp FROM cxc,cobro_cxc,cobro,facturas WHERE cxc.documento = facturas.documento AND cobro.documento=:documento_mov_pago_cxc AND cobro.keycodigo = cobro_cxc.codcobro AND cobro_cxc.codcxc = cxc.keycodigo",['documento_mov_pago_cxc'=>$datos->documento]);
                foreach($documentoFactura as $factura){
                    //como el resultado mysql viene dentro de un array lo recorremos y lo asignamos a una variable para acceder a ellos de forma de objeto
                    //esto se hace porque el numero del documento cobro_cxc no es el numero de factura y por eso hay que buscarlos en cxc
                    $documento = $factura->documento;
                    $comprobante = $factura->fiscalcomp;
                } 

                if(empty($parametrosVendedor->vendedores_especiales_id)){
                    //si no se especificaron vendedores se aplica a todos los usuarios de ese grupo
                    $comision=0;
                   
                    $porcentajeDescuento = floatval($parametrosVendedor->porcentaje_descuento_comision);
                    $porcentajeComision = floatval($parametrosVendedor->porcentaje_calculo_comision);
                    $montoParaComision = $datos->montoBase;
                   
                    //si posee descuento al total facrturas cobradas se descuenta del monto total a calcular comision
                    if($porcentajeDescuento > 0.0){
                        //$montoParaComision = $montoParaComision - (($montoParaComision * $porcentajeDescuento)/100);
                        $montoParaComision = ($montoParaComision * 100 )/(100+$porcentajeDescuento);
                    }
                    //calculamos la comision del vendedor
                    $comision = ($montoParaComision*$porcentajeComision)/100;
                   
                    //guardamos los datos en un arreglo para mostrar en la vista
                    //$datosVendedor = array('porcentajeDescuento'=>$porcentajeDescuento,'porcentajeComision'=>$porcentajeComision,'cGrupo'=>$datos->cGrupo,'codusua'=>$datos->cVendedor,'vendedor'=>$datos->vendedor,'cliente'=>$datos->cliente,'comision'=>$comision,'mCobrado'=>$totalCobrado);
                    $datosVendedor = array(
                        'id'=>$datos->keycodigo_siace,
                        'porcentajeDescuento'=>$porcentajeDescuento,
                        'porcentajeComision'=>$porcentajeComision,
                        'cGrupo'=>$datos->cGrupo,
                        'codusua'=>$datos->codigoVendedor,
                        'vendedor'=>$datos->nombreVendedor,
                        'cliente'=>$datos->cliente,
                        'comision'=>$comision,
                        'montoCobrado'=>$datos->montoBase,
                        'montoParaComision'=>$montoParaComision,
                        'fCobro'=>$datos->fecha,
                        'documento'=>$documento,
                        'comprobante'=>$comprobante,                       
                        
                    );
                    
                }else{
                    //si se espesifico usuarios a ese grupo se aplica dicha configuracion a los espesificados
                    $comision=0;
                    
                    $grupo = explode(',',$parametrosVendedor->vendedores_especiales_id);
                    foreach($grupo as $vendedor){
                        if($datos->codigoVendedor == $vendedor){
                            $porcentajeDescuento = floatval($parametrosVendedor->porcentaje_descuento_comision);
                            $porcentajeComision = floatval($parametrosVendedor->porcentaje_calculo_comision);
                            $montoParaComision = $datos->montoBase;
                           
                            //si posee descuento al total facrturas cobradas se descuenta del monto total a calcular comision
                            if($porcentajeDescuento > 0.0){
                                //$montoParaComision = $montoParaComision - (($montoParaComision * $porcentajeDescuento)/100);
                                $montoParaComision = ($montoParaComision * 100 )/(100+$porcentajeDescuento);
                            }
                            $comision = ($montoParaComision*$porcentajeComision)/100;
                            $datosVendedor = array(
                                'id'=>$datos->keycodigo_siace,
                                'porcentajeDescuento'=>$porcentajeDescuento,
                                'porcentajeComision'=>$porcentajeComision,
                                'cGrupo'=>$datos->cGrupo,
                                'codusua'=>$datos->codigoVendedor,
                                'vendedor'=>$datos->nombreVendedor,
                                'cliente'=>$datos->cliente,
                                'comision'=>$comision,
                                'montoCobrado'=>$datos->montoBase,
                                'montoParaComision'=>$montoParaComision,
                                'fCobro'=>$datos->fecha,
                                'documento'=>$documento,
                                'comprobante'=>$comprobante,
                            );    
                           
                        }
                    }
                }
                
            }
            
            $vendedores[]=(object)$datosVendedor;
            
        }
        
        return $vendedores;
    }

    public function cambioDeVendedor($id,$fechaini='',$fechafin=''){
        $herramientas = new HerramientasController();
        $conexionSQL = $herramientas->conexionDinamicaBD(session('basedata'));
        $listaVendedores =  $conexionSQL->select("SELECT clientes.vendedor_codusua codusua,clientes.vendedor_nombre usuario, clientes.nombre FROM cxc, clientes WHERE cxc.codclie = clientes.keycodigo AND clientes.vendedor_codusua<>0 GROUP BY  clientes.vendedor_codusua order by clientes.vendedor_nombre asc");
        $datosFactura= DB::select('select * from mov_pago_cxc_comision_ventas where id=:id',[$id]);
        return view("informesAdicionales.comisionVentas.cambioDeVendedor",['empresas'=>$herramientas->listarEmpresas(),'datosFactura'=>$datosFactura,'listaVendedores'=>$listaVendedores,'fechaini'=>$fechaini,'fechafin'=>$fechafin]);
    }

    public function guardarCambioVendedor(Request $request){
        
        $id = $request->id;
        $codigoVendedor= '';
        $nombreVendedor = '';
        if(!empty($request->nuevo_vendedor)){
            $nuevoVendedor = explode('|',$request->nuevo_vendedor);
            $codigoVendedor= $nuevoVendedor[0];
            $nombreVendedor = $nuevoVendedor[1];
        }
        //cambiamos el vendedor asignado
        DB::update('update mov_pago_cxc_comision_ventas set codigoVendedor=?,nombreVendedor=?,cod_vendedor_antiguo=?,nombre_vendedor_antiguo=? where id=?',[$codigoVendedor,$nombreVendedor,$request->cod_vendedor_antiguo,$request->nombre_vendedor_antiguo,$id]);
        return self::comisionPorVentas($request->fechaini,$request->fechafin);
    } 

    public function buscarComisionPorVentas(Request $request){
        $herramientas = new HerramientasController();
        $datosVendedor=array();
        $vendedores = array();
        
        $fechaini = $request->fechaini;
        $fechafin = $request->fechafin;
        //buscamos los vendedores activos en cxc
        $conexionSQL = $herramientas->conexionDinamicaBD(session('basedata'));
        $listaVendedores =  $conexionSQL->select("SELECT clientes.vendedor_codusua codusua,clientes.vendedor_nombre usuario, clientes.nombre FROM cxc, clientes WHERE cxc.codclie = clientes.keycodigo AND clientes.vendedor_codusua<>0 GROUP BY  clientes.vendedor_codusua");
        
        foreach($listaVendedores as $vendedor){ 
            $codVendedor = $vendedor->codusua;
            
            //$ventas = self::sqlBuscarVentasDelVendedorSiace($codVendedor,$fechaini,$fechafin);
            $ventas = self::buscarVentasDelVendedorCalculoComision($codVendedor,$fechaini,$fechafin);
            
            if(empty($ventas)){
                continue;
            }           
            $datosVendedor=array('codVendedor'=>$codVendedor,'nomVendedor'=>$vendedor->usuario,'ventas'=>$ventas);
            $vendedores[]=$datosVendedor;
        } 
        
   
        return view("informesAdicionales.comisionVentas.comisionPorVentas",['tipoResultado'=>$request->tipo_resultado,'vendedores'=>$vendedores,'empresas'=>$herramientas->listarEmpresas(),'fechaini'=>$fechaini,'fechafin'=>$fechafin]);
    }

    public function eliminarComisionVentas($id,$fechaini,$fechafin){
        DB::delete('DELETE from mov_pago_cxc_comision_ventas WHERE id=?',[$id]);
        \Session::flash('message', '¡Registro Eliminado!, vuelva a consultar en el rango de fecha deseado');
        return self::comisionPorVentas($fechaini,$fechafin);
    }

    public function reporteComisionPorVentasExcel($desde,$hasta){
       /*  $ventas = self::buscarVentasDelVendedorCalculoComision('',$desde,$hasta);
        
        $libro = new Spreadsheet();
        $libro
            ->getProperties()
            ->setCreator("Nombre del autor")
            ->setLastModifiedBy('Juan Perez')
            ->setTitle('Excel creado con PhpSpreadSheet')
            ->setSubject('Excel de prueba')
            ->setDescription('Excel generado como prueba')
            ->setKeywords('PHPSpreadsheet')
            ->setCategory('Categoría de prueba');
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Reporte Excel.xlsx"');
        header('Cache-Control: max-age=0');
        $hoja = $libro->getActiveSheet();
        $hoja->setTitle("Hoja 1");
        
        $hoja->setCellValue("A1", "Cod Grupo");
        $hoja->setCellValue("B1", "Cod vendedor");
        $hoja->setCellValue("C1", "Vendedor");
        $hoja->setCellValue("D1", "Cliente");
        $hoja->setCellValue("E1", "Factura");        
        $hoja->setCellValue("F1", "Fecha Cobro");        
        $hoja->setCellValue("G1", "Monto Cobro");        
        $hoja->setCellValue("H1", "Porcen Descuento");
        $hoja->setCellValue("I1", "Porcen Comision");
        $hoja->setCellValue("J1", "Monto Comision");
        $fila=2;
        $coumna=1;
        foreach($ventas as $venta){
            $hoja->setCellValueByColumnAndRow(1, $fila, $venta->cGrupo);
            $hoja->setCellValueByColumnAndRow(2, $fila, $venta->codusua);
            $hoja->setCellValueByColumnAndRow(3, $fila, $venta->vendedor);
            $hoja->setCellValueByColumnAndRow(4, $fila, $venta->cliente);
            $hoja->setCellValueByColumnAndRow(5, $fila, $venta->documento);            
            $hoja->setCellValueByColumnAndRow(6, $fila, $venta->fCobro);           
            $hoja->setCellValueByColumnAndRow(7, $fila, $venta->mCobrado);          
            $hoja->setCellValueByColumnAndRow(8, $fila, $venta->porcentajeDescuento);
            $hoja->setCellValueByColumnAndRow(9, $fila, $venta->porcentajeComision);
            $hoja->setCellValueByColumnAndRow(10, $fila, $venta->comision); 
            $fila++;
        }
        

        $writer = IOFactory::createWriter($libro, 'Xlsx');
        $writer->save('php://output');
        exit; */
    }

    public function eliminarListadoComicionVentas($fechaini,$fechafin){
        //elimina todos los registros de la tabla mov_pago_cxc_comision_ventas que pertenecen a la tienda seleccionada
        //y en el rango de fecha indicado, asi cuando se buelva a buscasr los registros se traeran los del siace
        
        DB::delete("DELETE FROM mov_pago_cxc_comision_ventas WHERE empresa_rif=? and fecha>=? and fecha<=?",[session('empresaRif'),$fechaini,$fechafin]);
        \Session::flash('message', '¡Registros Eliminados!, vuelva a consultar en el rango de fecha deseado');
        return self::comisionPorVentas($fechaini,$fechafin);
    }

    /*****************  VARIACION DE COSTOS DE LOS PRODUCTOS A LO LARGO DEL TIEMPO  ******************** */
    public function productosVariacionPrecioCompra(){
        $herramientas = new HerramientasController();
        $conexionSQL = $herramientas->conexionDinamicaBD(session('basedata'));
        $productos =  $conexionSQL->select("SELECT codprod,nombre,costo,precio from productos where stock>0");
       
        return view('informesAdicionales.variacionPrecioCompra',['empresas'=>$herramientas->listarEmpresas(),'productos'=>$productos]);
    }

    public function buscarProductosVariacionPrecioCompra(Request $request){
        
        $compras = array();
        $herramientas = new HerramientasController();
        $conexionSQL = $herramientas->conexionDinamicaBD(session('basedata'));
        $productos =  $conexionSQL->select("SELECT codprod,nombre,costo,precio from productos where stock>0");
        //recorremos el arreglo con los codigos de productos seleccionados
        foreach($request->codigo_productos as $codprod){
            $resultados = self::busquedaEnComprasDeProductos($codprod);
            $compras[]=$resultados;
        }
        
        return view('informesAdicionales.variacionPrecioCompra',['empresas'=>$herramientas->listarEmpresas(),'productos'=>$productos,'compras'=>$compras]);

    }

    public function busquedaEnComprasDeProductos($codprod){
        $herramientas = new HerramientasController();
        $conexionSQL = $herramientas->conexionDinamicaBD(session('basedata'));
        /*buscamos en compras las ultimas 10 compras entre este año y el pasdo 
          junto con el keycodigo de kardex al cual le pertenece a la compra
          con este keycodigo podemos buscar en kerdex las ventas en ese rango mas adelante
        */
        $compras =  $conexionSQL->select("
        SELECT
            kardex.keycodigo keycodigo_kardex,
            compras.documento,
            compras.fecha,
            compras.cierre,
            compras.codprod,
            compras.nombre,
            SUM(compras.cantidad)cantidad,
            compras.costo,
            compras.precio,
            compras.codprov,
            compras.proveedor,
            compras.rif_proveedor
        FROM 
            (SELECT c.documento,c.fecha,c.cierre,c.codprod,c.nombre,c.cantidad,c.costo,c.precio,p.keycodigo AS codprov,p.nombre AS proveedor,p.rif AS rif_proveedor FROM compras c,proveedores p WHERE c.procodigo = p.keycodigo AND c.codprod=:codprod ORDER BY c.keycodigo DESC LIMIT 10)AS compras,
            kardex
        WHERE 
            YEAR(compras.fecha) >= YEAR(DATE_SUB(NOW(),INTERVAL 1 YEAR))
            AND kardex.documento=compras.documento
            AND kardex.codprod = compras.codprod
            AND kardex.codprov = compras.codprov
        GROUP BY compras.documento 
        ORDER BY compras.fecha ASC
       ",[$codprod]);

       /* Recorremos el resultado de las ultimas compras para luego buscar las ventas, costo,precio y promedio de utilidad
         del primer ingreso hasta el segundo y el segundo con el tercero y asi sucesivamente
       */
       $cantidadCompras = count($compras);

       /** Recorremos el arreglo que genero la consulta para luego generar otro arreglo 
        * le incluimos las ventas
        */
      
       $ventas =array();
       for($i=0 ; $i<$cantidadCompras-1; ){
        /* buscamo la fecha de pago de la factura inicial porque desde que recibo la factura hasta 
            que la cancelo puede subir de precio y si calculo en cuanto vendi los productos hasta la 
            proxima factura puedo tener un mal calculo ya que pueden llegar otras facturas de otros proveedores
            a un mayor costo
        */
           $fechaPagoFactura = FacturasPorPagar::select('fecha_real_pago')->where('documento',$compras[$i]->documento)->where('proveedor_rif',$compras[$i]->rif_proveedor)->first();       
           $kardex = $conexionSQL->select("SELECT SUM(salidas),SUM(costo),SUM(precio),AVG(utilreal) FROM kardex WHERE codprod=:codprod AND  keycodigo >=:desde AND keycodigo <=:hasta AND codconcept=1000 GROUP BY codprod",[$compras[$i]->codprod,$compras[$i]->keycodigo_kardex,$compras[$i+1]->keycodigo_kardex]);
           
           $datosCompras = array('keycodigo_kardex'=>$compras[$i]->keycodigo_kardex,'documento'=>$compras[$i]->documento,'fecha_pago_desde'=>$fechaPagoFactura,'contradocumento'=>$compras[$i+1]->documento,'fecha'=>$compras[$i+1]->fecha,'proveedor'=>$compras[$i+1]->proveedor,'cantidad'=>$compras[$i+1]->cantidad,'costo'=>$compras[$i+1]->costo,'kardex'=>$kardex); 
           $ventas[]=$datosCompras;
           $i++;
       }
       
       dd($ventas,'informes adionales controler linea 453');
        //return $productos;
    }

    /***************** FIN  VARIACION DE COSTOS DE LOS PRODUCTOS A LO LARGO DEL TIEMPO  ******************** */
}
