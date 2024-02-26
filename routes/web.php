<?php
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cuadres\CuadresController;
use App\Http\Controllers\Admin\GeneralController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RolesPermisosController;
use App\Http\Controllers\Admin\EmpresasController;
use App\Http\Controllers\Herramientas\HerramientasController;
use App\Http\Controllers\Cuadres\CuadresObservacionController;
use App\Http\Controllers\Cuadres\CuadresTransferenciaController;
use App\Http\Controllers\Cuadres\CuadresPrestamoEfectivoController;
use App\Http\Controllers\Admin\BancoController;
use App\Http\Controllers\Admin\ConfiguracionController;
use App\Http\Controllers\Admin\ProveedorController;
use App\Http\Controllers\CuentasPorPagar\CuentasPorPagarController;
use App\Http\Controllers\CuentasPorPagar\ReportesCuentasPorPagarController;
use App\Http\Controllers\Islr\contribuyenteController;
use App\Http\Controllers\Islr\EmpleadoDeclaranteController;
use App\Http\Controllers\Islr\islrController;
use App\Http\Controllers\Islr\RetencionController;
use App\Http\Controllers\Islr\rrhhController;
use App\Http\Controllers\Islr\UtController;
use App\Http\Controllers\Islr\xmlController;
use App\Http\Controllers\RecepcionDivisas\OperacionesDivisasCustodioController;
use App\Http\Controllers\RecepcionDivisas\OperacionesPuntosController;
use App\Http\Controllers\InformesAdicionales\InformesAdicionalesController;
use App\Http\Controllers\InformesAdicionales\VendedorComisionController;
use App\Http\Controllers\InformesAdicionales\HabladoresController;
use App\Http\Controllers\AsistenteCompras\AsistenteComprasController;
use App\Http\Controllers\RetencionIva\RetencionIvaController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();
Route::get('/', [LoginController::class,'inicioSesion'])->name('inicioSesion');
Route::get('/registroalternodeusuario',[RegisterController::class,'registroAlternoUsuarios'])->name('registroAlternoUsuarios');


Route::middleware(['auth'])->group(function(){

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    /*************CUADRES */
    Route::get('recaudo/cuadres',[CuadresController::class,'index'])->name('cuadres.index');
    Route::post('recaudo/cuadres/buscarMes',[CuadresController::class,'buscarMes'])->name('cuadres.buscarMes');
    Route::get('recaudo/obtener-empresa-seleccionada',[EmpresasController::class,'obtenerEmpresaSeleccionada']);
    Route::get('recaudo/cuadres-nuevo-registro',[CuadresController::class,'vistaRegistrarCuadre'])->name('cuadres.vistaRegistrarCuadre');
    Route::post('recaudo/cuadres-nuevo-registro',[CuadresController::class,'seleccionFechaRegistroCuadre'])->name('cuadres.seleccionFechaRegistroCuadre');
    Route::get('recaudo/cuadres-listar-observaciones/{tipoObservacion}',[CuadresObservacionController::class,'obtenerObservacionCuadre']);
    Route::post('recaudo/cuadres-guardar-observacion',[CuadresObservacionController::class,'guardarObservacion']);
    Route::get('recaudo/cuadres-lista-empleados-arqueo',[CuadresObservacionController::class,'listaEmpleadosCuadre']);
    Route::get('recaudo/cuadres-eliminar-observacion/{id}',[CuadresObservacionController::class,'eliminarObservacionCuadre']);
    Route::get('recaudo/banco-lista-bancos',[BancoController::class,'listaBancos']);
    
    /*************CUADRES TRANSFERENCIAS */
    Route::get('recaudo/cuadres-listar-transferencias',[CuadresTransferenciaController::class,'index']);
    Route::post('recaudo/cuadres-registro-transferencias',[CuadresTransferenciaController::class,'guardarTransferenciaCuadre']);
    Route::get('recaudo/cuadres-eliminar-transferencias/{id}',[CuadresTransferenciaController::class,'eliminarTransferenciaCuadre']);
    Route::get('recaudo/cuadres-listar-transferencias-siace',[CuadresTransferenciaController::class,'transferenciasDelSiace']);

    /*************CUADRES PRESTAMOS EN EFECTIVO */
    Route::get('recaudo/cuadres-listar-prestamo-efectivo',[CuadresPrestamoEfectivoController::class,'index']);
    Route::post('recaudo/cuadres-guardar-prestamo-efectivo',[CuadresPrestamoEfectivoController::class,'guardarPrestamoEfectivo']);
    Route::get('recaudo/cuadres-eliminar-prestamo-efectivo/{id}',[CuadresPrestamoEfectivoController::class,'eliminar']);

    /*************ADMINISTRADOR usuarios*/
    Route::get('/admin',[GeneralController::class,'datosEmpresa'])->name('admin.general.datosEmpresa');
    Route::get('/admin/user',[UserController::class,'index'])->name('admin.user.index');
    Route::get('/admin/user/edit/{id?}',[UserController::class,'edit'])->name('admin.user.edit');
    Route::put('/admin/user/edit/{id?}',[UserController::class,'update'])->name('admin.user.update');
    Route::get('/admin/user/delete/{id?}',[UserController::class,'delete'])->name('admin.user.delete');
    Route::get('/admin/user/register',[UserController::class,'register'])->name('admin.user.register');
    Route::post('/admin/user/register',[UserController::class,'save'])->name('admin.user.save');

    /*************ADMINISTRADOR roles */
    Route::get('/admin/roles',[RolesPermisosController::class,'roleIndex'])->name('admin.role.index');
    Route::get('/admin/roles/api',[RolesPermisosController::class,'roleIndexApi']);
    Route::get('/admin/roles/all-permisos/api/{rolid?}',[RolesPermisosController::class,'allPermisosApi']);
    Route::post('/admin/roles/guardarApi',[RolesPermisosController::class,'guardarRolApi']);
    Route::get('/admin/roles/revocarPermisosRolId/{id?}',[RolesPermisosController::class,'revocarPermisosRolId']);
    Route::get('/admin/roles/eliminarRole/{id}',[RolesPermisosController::class,'eliminarRole']);
    Route::get('/admin/roles/editarRole/{roleId}',[RolesPermisosController::class,'editarRol'])->name('admin.role.editar');
    Route::put('/admin/roles/actualizar/{roleId}',[RolesPermisosController::class,'updateRol'])->name('admim.role.update');

    /*************ADMINISTRADOR permisos */
    Route::get('/admin/permisos',[RolesPermisosController::class,'permisosIndex'])->name('admin.permiso.index');
    Route::post('/admin/permisos/guardar-permisos',[RolesPermisosController::class,'guardarPermisos']);
    Route::get('/admin/permisos/eliminar-permisos/{id?}',[RolesPermisosController::class,'eliminarPermisos']);
    Route::get('/admin/permisos/listar',[RolesPermisosController::class,'permisosListar']);

    /**************ADMINISTRADOR Registro de empresas*/
    Route::get('/admin/empresas',[EmpresasController::class,'index'])->name('admin.empresas.index');
    Route::get('/admin/empresas/create',[EmpresasController::class,'create'])->name('admin.empresas.create');
    Route::post('/admin/empresas/save',[EmpresasController::class,'save'])->name('admin.empresas.save');
    Route::get('/admin/empresas/edit/{id}',[EmpresasController::class,'edit'])->name('admin.empresas.edit');
    Route::put('/admin/empresas/edit/{id}',[EmpresasController::class,'update'])->name('admin.empresas.update');
    Route::get('/admin/empresas/delete/{id}',[EmpresasController::class,'delete'])->name('admin.empresas.delete');
    Route::get('/admin/empresa/seleccion',[EmpresasController::class,'listarSeleccion'])->name('admin.empresas.listar.seleccion');
    Route::get('/admin/empresa/listar/api',[EmpresasController::class,'listarEmpresasApi']);
    Route::get('/admin/empresa/cambair-empresa/{empresaRif}',[EmpresasController::class,'cambiarEmpresa']);

    /***********ADMINISTRADOR Configuracion*/
    Route::get('admin/configuracion/cuentasPorPagar',[ConfiguracionController::class,'indexConfiguracionCuentasPorPagar'])->name('indexConfiguracionCuentasPorPagar');
    Route::post('admin/configuracion/cuentasPorPagar',[ConfiguracionController::class,'guardarConfiguracionCuentasPorPagar'])->name('guardarConfiguracionCuentasPorPagar');
    Route::get('admin/configuracion/general',[ConfiguracionController::class,'configuracionGeneral'])->name('configuracionGeneral');
    Route::post('admin/configuracion/general',[ConfiguracionController::class,'guardarConfiguracionGeneral'])->name('guardarConfiguracionGeneral');
    Route::get('admin/configuracion/cuentasPorPagar-sincronizar-server',[ConfiguracionController::class,'sincorinzarServidoresTraerUltimosRegistros'])->name('sincorinzarServidores');
    Route::get('admin/configuracion/cuentasPorPagar-sincronizar-todo-server',[ConfiguracionController::class,'sincorinzarServidoresTraerTodosLosRegistros'])->name('sincorinzarServidoresTodo');

    //bancos
    Route::get('/bancos',[BancoController::class,'index'])->name('banco.index');
    Route::post('/bancos',[BancoController::class,'create'])->name('banco.create');
    Route::get('/banco/edit/{id}',[BancoController::class,'edit'])->name('banco.edit');
    Route::put('/banco/edit/{id}',[BancoController::class,'update'])->name('banco.update');
    Route::get('/banco/delete/{id}',[BancoController::class,'delete'])->name('banco.delete');
    Route::get('/banco-lista-bancos',[BancoController::class,'listaBancos']);
    //fin bancos

    //cuentas por pagar
    Route::get('/cuentasporpagar/inicio/{ruta?}',[CuentasPorPagarController::class,'seleccionarEmpresa'])->name('cuentasporpagar.inicio');
    Route::post('/cuentasporpagar/empresa',[CuentasPorPagarController::class,'guardarSeleccionEmpresa'])->name('cuentasporpagar.guardarSeleccionEmpresa');
    Route::get('/cuentasporpagar/nuevaFactura',[CuentasPorPagarController::class,'nuevaFacturaPorPagar2'])->name('nuevafacturaporpagar.index');
    Route::get('/cuentasporpagar/facturasPorPagar',[CuentasPorPagarController::class,'facturasPorPagar'])->name('cuentasporpagar.facturasPorPagar');
    Route::get('/cuentasporpagar/facturasPagadas',[CuentasPorPagarController::class,'facturasPagadas'])->name('cuentasporpagar.facturasPagadas');
    Route::get('/cuentasporpagar/otrospagos',[CuentasPorPagarController::class,'addOtrosPagos'])->name('addotrospagos');
    Route::post('cuentasporpagar/is',[CuentasPorPagarController::class,'buscarCuentasPagadas'])->name('cuentaspagadas.buscar');
    Route::post('/cuentasporpagar/optenerfacturas',[CuentasPorPagarController::class,'optenerFacturasPorPagar'])->name('optenerfacturasporpagar');
    Route::post('/cuentasporpagar/pagarfacturas',[CuentasPorPagarController::class,'vistaPagarFacturas'])->name('vistaPagarFacturas');
    Route::post('/cuentasporpagar/guardarpagarfacturas',[CuentasPorPagarController::class,'guardarPagarFacturas'])->name('guardarPagarFacturas');
    Route::get('/cuentasporpagar/pagar/{id}',[CuentasPorPagarController::class,'pagarCuentas'])->name('pagarcuenta');
    Route::post('/cuentasporpagar/pagar',[CuentasPorPagarController::class,'pagarCuentasSave'])->name('pagarcuenta.save');
    Route::post('/cuentasporpagar/nuevaFacturaSave',[CuentasPorPagarController::class,'saveNuevaFacturaPorPagar'])->name('nuevafacturaporpagar.save');
    Route::get('/cuentasporpagar/detalle/{id}',[CuentasPorPagarController::class,'detalleCuentasPagadas'])->name('detallecuentaspagadas');
    Route::post('cuentasporpagar/save',[CuentasPorPagarController::class,'savePagarVariasCuentas'])->name('pagarvariascuentas.save');
    Route::get('/cuentasporpagar/eliminaAsientoCuentasPorPagar/{id?}/{codigorelacion?}',[CuentasPorPagarController::class,'elimarAsientoCuentasPorPagar'])->name('elimarAsientoCuentasPorPagar');
    Route::get('/cuentasporpagar/desvincular/{id?}/{codigo?}',[CuentasPorPagarController::class,'desvincularAsientoCuentasPorPagar'])->name('desvincularAsientoCuentasPorPagar');
    Route::get('/cuentasporpagar/desvincular_bolivares/{id?}/{codigo?}',[CuentasPorPagarController::class,'desvincularAsientoCuentasPorPagarBolivares'])->name('desvincularAsientoCuentasPorPagarBolivares');
    Route::get('/cuentasporpagar/cuentaspagadas',[CuentasPorPagarController::class,'litarCunetasPagadas'])->name('listarcuentaspagadas.index');

    Route::get('/cuentasporpagar/eliminarporpagar/{id}/{urlRetorno?}',[CuentasPorPagarController::class,'eliminarFacturasPorPagar'])->name('eliminarFacturaPorPagar');
    Route::get('/cuentasporpagar/editarFacturasCargadasporpagar/{id}/{urlRetorno?}',[CuentasPorPagarController::class,'editarFacturasPorPagar'])->name('editarFacturasPorPagar');
    Route::put('/cuentasporpagar/editarFacturasCargadasporpagar/{id}',[CuentasPorPagarController::class,'updateFacturasPorPagar'])->name('updateFacturasPorPagar');
    Route::get('/cuentasporpagar/eliminar_todo/{codrelacion}',[CuentasPorPagarController::class,'eliminarTodasPorPagar'])->name('eliminarTodasPorPagar');	
    Route::post('/cuentasporpagar/saveotros',[CuentasPorPagarController::class,'saveOtrosPagos'])->name('saveotrospagos');	
    Route::get('/cuentasporpagar/limpiartodo',[CuentasPorPagarController::class,'limpiarTodo'])->name('cuentasPorPagarlimpiarTodo');
    Route::get('/cuentasporpagar/reciboPago/{codigorelacion?}',[CuentasPorPagarController::class,'reciboPagoFacturas'])->name('reciboPagoFacturas');
    ///------------------------fin cuentas por pagar----------------------///

    ////------------------Relacion Pago de Facturas Cuentas Por Pagar------/////
    Route::get('/cuentasporpagar/relacion-pago-facturas',[CuentasPorPagarController::class,'relacionPagoFacturasIndex'])->name('relacionPagoFacturasIndex');
    Route::post('/cuentasporpagar/calculo-pagos',[CuentasPorPagarController::class,'calculoDeDeudasPorFacturas'])->name('calculoDeDeudasPorFacturas');
    Route::get('/cuentasporpagar/facturas-calculadas',[CuentasPorPagarController::class,'listadoFacturasCalculadas'])->name('listadoFacturasCalculadas');
    Route::get('/cuentasporpagar/sacar-facturas-calculadas/{id}',[CuentasPorPagarController::class,'eliminaFacturaCalculada'])->name('eliminaFacturaCalculada');	
    Route::get('/cuentasporpagar/verPagarFacturas/{codigoRelacion?}/{id?}',[CuentasPorPagarController::class,'verVistaPagarFacturas'])->name('verVistaPagarFacturas');
    Route::get('/cuentasporpagar/ReporteBolivares',[CuentasPorPagarController::class,'reportePagoBolivaresDeDolares'])->name('reportePagoBolivares');
    Route::post('/cuentasporpagar/resulReporteBolivares',[CuentasPorPagarController::class,'resulReportePagoBolivaresDeDolares'])->name('resulReportePagoBolivares');
    Route::post('/cuentasporpagar/fecha_facturas-calculadas',[CuentasPorPagarController::class,'seleccionarRangoFechaFacturasCalculadas'])->name('seleccionarRangoFechaFacturasCalculadas');
    

    ///-------------------Fin Relacion Pago de Facturas Cuentas Por Pagar--///// 

    ///-------------------REPORTES DE CUENTAS POR PAGAR -------------------/////
    Route::get('/cuentasporpagar/cuentaspagadas-reportepagos',[ReportesCuentasPorPagarController::class,'reporteCuentasPagadas'])->name('reportecuntaspagas');
    Route::post('/cuentasporpagar/cuentaspagadas-reportepagos',[ReportesCuentasPorPagarController::class,'buscarReporteCuentasPagadas'])->name('buscar.reportecuentaspagadas');
    Route::get('/cuentasporpagar/reporte-cuentasporpagar-reportePagosEmpresa',[ReportesCuentasPorPagarController::class,'reporteRelacionPagosPorEmpresa'])->name('reporteRelacionPagosPorEmpresa');
    Route::post('/cuentasporpagar/reporte-cuentasporpagar-reportePagosEmpresa',[ReportesCuentasPorPagarController::class,'resultadoReporteRelacionPagosPorEmpresa'])->name('resultadoReporteRelacionPagosPorEmpresa');
    Route::get('/cuentasporpagar/reporte-cuentasporpagar-proveedorTodasEmpresa',[ReportesCuentasPorPagarController::class,'reportePagoPorProvedorTodasEmpresas'])->name('reportePagoPorProvedorTodasEmpresas');
    Route::post('/cuentasporpagar/reporte-cuentasporpagar-proveedorTodasEmpresa',[ReportesCuentasPorPagarController::class,'resultadoReportePagoPorProvedorTodasEmpresas'])->name('resultadoReportePagoPorProvedorTodasEmpresas');

    /*Rutas de Proveedores*/
    Route::get('/proveedor/{origen?}',[ProveedorController::class,'index'])->name('proveedor.index');
    Route::get('/proveedor/create/{origen?}',[ProveedorController::class,'create'])->name('proveedor.create');
    Route::post('/proveedor/create/{origen?}',[ProveedorController::class,'save'])->name('proveedor.save');
    Route::get('/proveedor/ver/{id}/{origen?}',[ProveedorController::class,'ver'])->name('proveedor.ver');
    Route::get('/proveedor/edit/{id}/{origen?}',[ProveedorController::class,'edit'])->name('proveedor.edit');
    Route::PUT('/proveedor/update/{id}/{origen?}',[ProveedorController::class,'update'])->name('proveedor.update');
    /*fin rutas de proveedores */

    /*Registro de las retenciones ISLR*/
    Route::get('/regisretenciones/',[islrController::class,'index'])->name('islr.index');
    Route::get('/regisretenciones-ajax/',[islrController::class,'todosRegistrosIslrAjax']);
    Route::post('/regisretenciones/filtro',	[islrController::class,'filtrar'])->name('islr.filtrar');
    Route::get('/regisretenciones/create',[islrController::class,'create'])->name('islr.create');
    Route::post('/regisretenciones/create-detalle',	[islrController::class,'registroIslr'])->name('islr.save');
    Route::PUT('/regisretenciones/create-detalle2/{idislr?}',[islrController::class,'saveMontoServicios'])->name('islr.savedetalle');
    Route::get('/regisretenciones/view/{id}/{vista?}',[islrController::class,'view'])->name('islr.view');
    Route::get('/regisretenciones/edit/{id}/{accion}/{idxml?}/{fechaIniFin?}',[islrController::class,'edit'])->name('islr.edit');
    Route::put('/regisretenciones/edit/{id}',[islrController::class,'update'])->name('islr.update');
    Route::get('/regisretenciones/excel',[islrController::class,'index'])->name('islr.export.excel');
    Route::get('/detalleretenciones/delete/{id}/{idRetencion}',[islrController::class,'deleteDetalles'])->name('islr.detalle.destroy');
    Route::put('/regisretenciones/create-detalle/{idRetencion?}/{accion?}/{idxml?}/{fechasPeriodo?}',[islrController::class,'saveRegistroIslrGeneral'])->name('islr.save2');
    Route::get('/regisretenciones/monto-servicios/{idRetencion?}/{ultimoPorcentajeProveedor?}',[islrController::class,'montoServicios'])->name('islr.montoServicios');
    Route::get('/regisretenciones/editDetalle/{id}/{idRetencion}',[islrController::class,'editDetalles'])->name('islr.detalle.edit');
    Route::get('/regisretenciones/delete/{id}/{ncontrol?}/{rifempresa?}',[islrController::class,'eliminarIslr'])->name('islr.delete');
    /*FIN Registro de las retenciones*/


    /*Registro de Retenciones elaboracion del archivo XML ISLR*/
    Route::get('/regisretenciones/xml/{vista?}',[xmlController::class,'xml'])->name('islr.xml.create');
    Route::get('/regisretenciones/xml-listar',[xmlController::class,'xmlListar'])->name('islr.xml.listar');
    Route::post('/regisretenciones/xml',[xmlController::class,'xmlCrear'])->name('islr.xmlCrear');
    Route::get('/regisretenciones/xmlVer/{fecha?}/{rif?}/{xmlid?}/{fechasPeriodo?}',[xmlController::class,'xmlVer'])->name('islr.xml.ver');
    Route::get('/regisretenciones/xmlDow/{rif}/{periodo}',[xmlController::class,'descargarXml'])->name('islr.descargarXml');
    Route::put('/regisretenciones/xmlEdit/{id}/{empresa}/{fecha}/{xmlid}',[xmlController::class,'xmlUpdate'])->name('xml.update');
    Route::get('regisretenciones/xmlNew/{encabezadoid}',[xmlController::class,'xmlNew'])->name('xml.new');
    Route::get('/regisretenciones/xmlDelete/{id}',[xmlController::class,'xmlDelete'])->name('xml.delete');

    /* FINRegistro de Retenciones elaboracion del archivo XML*/


    /*Registro de la unidad Tributaria ISLR*/
    Route::get('regisretenciones/ut',[UtController::class,'index'])->name('ut.index');
    Route::get('regisretenciones/ut/create',[UtController::class,'create'])->name('ut.create');
    Route::post('regisretenciones/ut/create',[UtController::class,'save'])->name('ut.save');
    Route::get('regisretenciones/ut/edit/{id?}',[UtController::class,'edit'])->name('ut.edit');
    Route::PUT('regisretenciones/ut/edit/{id}',[UtController::class,'update'])->name('ut.update');
    /*FIN Registro de la unidad Tributaria*/

    /*Recursos Humanos ISLR*/
    Route::get('regisretenciones/rrhh',[rrhhController::class,'index'])->name('rrhh.index');
    Route::get('regisretenciones/rrhh/destroy/{id}/{empresaRif?}/{accion?}/{idxml?}/{fechaIniFin?}',[rrhhController::class,'destroy'])->name('rrhh.destroy');
    Route::post('regisretenciones/rrhh/import-excel',[rrhhController::class,'importExcel'])->name('import-list-excel');
    Route::get('regisretenciones/rrhh/edit/{id}/{empresa}/{accion?}/{idxml?}/{fechaIniFin?}',[rrhhController::class,'edit'])->name('rrhh.edit');
    Route::put('regisretenciones/rrhh/edit/{id}/{accion?}/{idxml?}/{fechaIniFin?}',[rrhhController::class,'update'])->name('rrhh.update');
    Route::get('regisretenciones/rrhh/create',[rrhhController::class,'create'])->name('rrhh.create');
    Route::post('regisretenciones/rrhh/create',[rrhhController::class,'saveRrhh'])->name('rrhh.save');
    Route::post('regisretenciones/rrhh/empresa',[rrhhController::class,'postIndex'])->name('rrhh.postindex');
    Route::get('regisretenciones/rrhh/export/{rif?}',[rrhhController::class,'exportarRrhhCsv'])->name('rrhh.export');
    Route::post('regisretenciones/rrhh/update-all',[rrhhController::class,'updateMasivo'])->name('updateMasivo');
    /* FIN Recursos Humanos*/


    /*tipos de contribuyentes natural, juridico empleado ISLR*/
    Route::get('regisretenciones/contribuyentes',[contribuyenteController::class,'index'])->name('contribuyente.index');
    Route::get('regisretenciones/contribuyentes/create',[contribuyenteController::class,'create'])->name('contribuyente.create');
    Route::get('regisretenciones/contribuyentes/edit/{id}',[contribuyenteController::class,'edit'])->name('contribuyente.edit');
    Route::post('regisretenciones/contribuyentes/create',[contribuyenteController::class,'save'])->name('contribuyente.save');
    Route::put('regisretenciones/contribuyentes/edit/{id}',[contribuyenteController::class,'update'])->name('contribuyente.update');
    Route::get('regisretenciones/contribuyentes/destroy/{id}',[contribuyenteController::class,'destroy'])->name('contribuyente.destroy');
    /* FIN tipos de contribuyentes natural, juridico empleado*/

    /*Rutas porcentajes de Retencion de ISRL*/
    Route::get('regisretenciones/retencion',[RetencionController::class,'index'])->name('retencion.index');
    Route::get('regisretenciones/retencion/create',[RetencionController::class,'create'])->name('retencion.create');
    Route::POST('regisretenciones/retencion/create',[RetencionController::class,'save'])->name('retencion.save');
    Route::get('regisretenciones/retencion/edit/{id}',[RetencionController::class,'edit'])->name('retencion.edit');
    Route::put('regisretenciones/retencion/edit/{id}',[RetencionController::class,'update'])->name('retencion.update');
    Route::get('regisretenciones/retencion/destroy/{id}',[RetencionController::class,'destroy'])->name('retencion.destroy');
    /*FIN Rutas porcentajes de Retencion de ISRL*/


    /*REgistro de los empleados que declaran Impuesto ISLR*/
    Route::get('regisretenciones/declarantes',[EmpleadoDeclaranteController::class,'index'])->name('declarantes.index');
    Route::get('regisretenciones/declarantes/create',[EmpleadoDeclaranteController::class,'create'])->name('declarantes.create');
    Route::post('regisretenciones/declarantes/create',[EmpleadoDeclaranteController::class,'save'])->name('declarantes.save');
    Route::get('regisretenciones/declarantes/delete/{id}/{empresaRif?}/{accion?}/{idxml?}/{fechaIniFin?}',[EmpleadoDeclaranteController::class,'destroy'])->name('declarantes.delete');
    Route::get('regisretenciones/declarantes/edit/{id}/{accion?}/{idxml?}/{fechaIniFin?}',[EmpleadoDeclaranteController::class,'edit'])->name('declarantes.edit');
    Route::put('regisretenciones/declarantes/update/{id}/{accion?}/{idxml?}/{fechaIniFin?}',[EmpleadoDeclaranteController::class,'updateSalarios'])->name('declarantes.update.salario')	;
    /*FIN  REgistro de los empleados que declaran Impuesto*/

    //operaciones con divisas
    Route::get('/divisas',[OperacionesDivisasCustodioController::class,'index'])->name('listar.operaciones.divisas');
    Route::post('/divisas',[OperacionesDivisasCustodioController::class,'buscarOperacionDivisa'])->name('buscar.operaciones.divisas');
    Route::post('/divisas/search/{co}/{fecha}',[OperacionesDivisasCustodioController::class,'buscarOperacionDivisaAsesor'])->name('buscar.operaciones.divisas.asesor')	;
    Route::get('/divisas/custodio',[OperacionesDivisasCustodioController::class,'create'])->name('divisasCustodio.create');
    Route::post('/divisas/custodio',[OperacionesDivisasCustodioController::class,'saveOperacionDivisa'])->name('save.operacio.divisa');
    Route::get('/divisas/reporte',[OperacionesDivisasCustodioController::class,'reporteGeneral'])->name('divisas.reporte.general');
    Route::post('/divisas/reporte',[OperacionesDivisasCustodioController::class,'resultadoReporteGeneral'])->name('divisas.reporte.general.buscar');
    Route::get('/divisas/custodio/edit/{id}',[OperacionesDivisasCustodioController::class,'edit'])->name('divisas.custodio.edit');
    Route::get('/divisas/reporte-detalle/{co}/{tasa}/{fecha}',[OperacionesDivisasCustodioController::class,'reporteDetalladoGerencia'])->name('divisa.reporte.detallado.gerencia');
    Route::get('/divisas/reporte-recaudo',[OperacionesDivisasCustodioController::class,'reporteRecaudo'])->name('divisa.reporte.recaudo');
    Route::get('/divisas/reporte-recaudo-movpago',[OperacionesDivisasCustodioController::class,'reporteRecaudoMovpago'])->name('divisa.reporte.recaudo.movpagos');
    

    Route::post('/divisas/reporte-buscar-recaudo',[OperacionesDivisasCustodioController::class,'buscarReporteRecaudo'])->name('divisa.buscar.reporte.recaudo');
    Route::get('/divisas/listar-pagos',[OperacionesDivisasCustodioController::class,'listarPagoMovil'])->name('listar.pago.movil');
    Route::post('/divisas/listar-pagos',[OperacionesDivisasCustodioController::class,'buscarListarPagoMovil'])->name('buscar.listar.pago.movil');
    Route::get('/divisas/procesar-pago/{co?}/{id}',[OperacionesDivisasCustodioController::class,'procesarPagoMovil'])->name('procesar.pago.movil');
    Route::PUT('/divisas/procesar-pago/{co?}/{id}',[OperacionesDivisasCustodioController::class,'savePagoMovil'])->name('save.pago.movil');
    Route::get('/divisas/anular-pago/{co?}/{id}',[OperacionesDivisasCustodioController::class,'anularPagoMovil'])->name('anular.pago.movil');
    
        //fin operaciones con divisas

    //Relacion porcentual puntos de ventas
    Route::get('divisas/relacion-puntosdeventa',[OperacionesPuntosController::class,'index'])->name('porcentaje.puntosventas');
    Route::post('divisas/relacion-puntosdeventa',[OperacionesPuntosController::class,'relacionPorcentualPuntosVentas'])->name('mostart.porcentaje.puntosventas');
    //fin relacion porcentual puntos de ventas

    //------------------------informes Adicionales------------------------
    Route::get('informes',[InformesAdicionalesController::class,'index'])->name('informesAdicionales.index');
    Route::get('informes/comision-ventas',[InformesAdicionalesController::class,'comisionPorVentas'])->name('comisionPorVentas');
    Route::post('informes/comision-ventas',[InformesAdicionalesController::class,'buscarComisionPorVentas'])->name('buscarComisionPorVentas');
    Route::get('informes/seleccion-sucursal/{rifEmpresa}/{vista?}',[InformesAdicionalesController::class,'seleccionSucursal'])->name('seleccionSucursal');
    Route::get('informes/vendedores-comision',[VendedorComisionController::class,'index'])->name('empleadosComisionEspecial');
    Route::post('informes/vendedores-comision',[VendedorComisionController::class,'guardar'])->name('guardarEmpleadosComisionEspecial');
    Route::get('informes/vendedor-comision-eliminar/{id}',[VendedorComisionController::class,'eliminar'])->name('eliminarEmpleadosComisionEspecial');
    Route::get('informes/vendedor-comision-editar/{id}',[VendedorComisionController::class,'editar'])->name('editarEmpleadosComisionEspecial');
    Route::get('informes/vendedor-comision-excel/{desde}/{hasta}',[InformesAdicionalesController::class,'reporteComisionPorVentasExcel'])->name('reporteComisionPorVentasExcel');
    Route::get('informes/cambio-vendedor/{id}/{fechaini?}/{fechafin?}',[InformesAdicionalesController::class,'cambioDeVendedor'])->name('cambioDeVendedor');
    Route::post('informes/guardar-cambio-vendedor',[InformesAdicionalesController::class,'guardarCambioVendedor'])->name('guardarCambioVendedor');
    Route::get('informes/variacion-compras',[InformesAdicionalesController::class,'productosVariacionPrecioCompra'])->name('productosVariacionPrecioCompra');
    Route::post('informes/buscar-variacion-compras',[InformesAdicionalesController::class,'buscarProductosVariacionPrecioCompra'])->name('buscarProductosVariacionPrecioCompra');
    Route::get('informes/eliminar-comision/{id}/{fechaini}/{fechafin}',[InformesAdicionalesController::class,'eliminarComisionVentas'])->name('eliminarComisionVentas');
    Route::get('informes/eliminar-todas-las-comisiones/{fechaini}/{fechafin}',[InformesAdicionalesController::class,'eliminarListadoComicionVentas'])->name('eliminarListadoComicionVentas');

    ///-----------------------fin informes adicionales--------------------

    /***********************INFORMES ADICIONALES HABLADORES */ 
    Route::get('/informes/habladores',[HabladoresController::class,'index'])->name('habladores.index');	
    Route::get('/informes/crear-habladores',[HabladoresController::class,'crearLista'])->name('habladores.crearLista');
    Route::post('/informes/guardar-habladores-creados',[HabladoresController::class,'guardarListaCreada'])->name('guardarListaCreada');
    Route::get('/informes/editar-habladores/{lista}',[HabladoresController::class,'editarLista'])->name('habladores.editarLista');
    Route::post('/informes/cambiar-tipoproducto',[HabladoresController::class,'cambiarTipoProductoParaCrearLista'])->name('cambiarTipoProductoParaCrearLista');
    Route::post('/informes/cambiar-tipoproducto-edit',[HabladoresController::class,'cambiarTipoProductoParaEditarLista'])->name('cambiarTipoProductoParaEditarLista');
    Route::get('/informes/listar-habladores/{nombreLista}',[HabladoresController::class,'listarHabladores'])->name('listarHabladores');
    Route::post('/informes/imprimir-habladores',[HabladoresController::class,'imprimirHabladores'])->name('imprimirHabladores');
    Route::get('/informes/eliminar-lista-habladores/{lista}',[HabladoresController::class,'eliminarListaHabladores']);
    Route::get('/informes/eliminar-producto-listado-hablador/{lista}/{id}',[HabladoresController::class,'eliminarProductoDeListadoHablador']);
    Route::get('/informes/habladores/manual',function(){
        return view('informesAdicionales/habladores/manualAyuda');
    });

    /***********************ASISTENTE DE COMPRAS******** */
    Route::get('/asistentecompra/inicio',[AsistenteComprasController::class,'index'])->name('asistentecompra.visualizadorPrecios');
    Route::get('/asistentecompra/listado-precios-droguerias',[AsistenteComprasController::class,'apiListadoPrecioDrogueria']);
    Route::post('/asistentecompra/guardar-pedido-detallado',[AsistenteComprasController::class,'guardarPedidoDetallado'])->name('guardar-pedido-detallado');
    Route::get('/asistentecompra/pedir/{id?}',[AsistenteComprasController::class,'seleccionarPedido'])->name('asistentecompra.pedir');
    Route::get('/asistentecompra/ApiListarEditarPedidos/{id}',[AsistenteComprasController::class,'apiListarRegistrosAsistenteCompra'])->name('apiListarRegistrosAsistenteCompra');
    Route::get('/asistentecompra/descargarExcel',[AsistenteComprasController::class,'descargarExcel'])->name('asistentecompra.descargarExcel');
    /**FIN DEL ASISTENTE DE COMPRAS */

    //puntos de ventas
    /* Route::get('/listar-puntos-de-ventas-registrados',[]) */


    /***********************HERRAMINETAS CONTROLLER******* */
    Route::get('/herramientas/cotizacionTasa',[HerramientasController::class,'cotizacionTasa'])->name('cotizacion.tasa');
    Route::get('/herraminetas/valorTasaActual',[HerramientasController::class,'ultimoValorDolar']);
    Route::get('/herraminetas/listarTodasLasTasas',[HerramientasController::class,'listarTodasLasTasa']);
    Route::post('/herraminetas/guardarTasa',[HerramientasController::class,'guardarTasa'])->name('guardarValorTasa');

    /***********************RETENCION DE IVA  ************/
    Route::get('/retencion-iva/index',[RetencionIvaController::class,'index'])->name('retencion.iva.index');
    Route::post('/retencion-iva/buscar-documento',[RetencionIvaController::class,'guardarFacturaRetencionIva'])->name('retencion.iva.buscarFacturasSiace');
    Route::get('/retencion-iva/eliminar-factura/{id}',[RetencionIvaController::class,'eliminarFactura'])->name('retencion.iva.eliminarFactura');
    Route::post('/retencion-iva/generar-retencion',[RetencionIvaController::class,'generarRetencionIva'])->name('retencion.iva.generar');
    Route::post('/retencion-iva/guardar-retencion',[RetencionIvaController::class,'guardarComprobanteRetencionIva'])->name('retencion.iva.guardar');
    Route::get('/retencion-iva/listar-retencion',[RetencionIvaController::class,'listarRetencionesIva'])->name('retencion.iva.listar');
    Route::get('/retencion-iva/generar-comprobante/{comprobanteRetencion}/{firma?}',[RetencionIvaController::class,'mostrarComprobanteRetencionIva'])->name('retencion.iva.generar_comprobante');
    Route::post('/retencion-iva/buscar-retencion',[RetencionIvaController::class,'buscarRetencionIva'])->name('retencion.iva.buscar_retencion');
    Route::get('/retencion-iva/seleccion-sucursal/{empresa_rif}',[RetencionIvaController::class,'seleccionSucursal'])->name('retencion.iva.seleccion_sucursal');
    Route::get('/retencion-iva/editar-retencion/{comprobante}',[RetencionIvaController::class,'editarRetencionIva'])->name('retencion.iva.editar_retencion');
    Route::get('/retencion-iva/editar-retencion/listar-detalle-retencion/{comprobante}',[RetencionIvaController::class,'detallesRetencionIva']);


});
//se sacaron del middleware Auth para que no requiere de inicio de sesion al momento de buscar informacion
//esto ayuda a que el arqueador no le quede la pagina sin respuesta despues de expirar el tiempo de sesion
Route::get('/divisas/listado-asesores/{fecha}',[OperacionesDivisasCustodioController::class,'listarAsesoresPorFecha']);
Route::post('/divisas/reporte-recaudo-movpago',[OperacionesDivisasCustodioController::class,'buscarReporteRecaudoMovpago'])->name('buscar.reporte.recaudo.movpagos');
