<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<?php 
    
    //use App\User;
    use App\Http\Controllers\HerramientasController;
    $version = '3.2';
?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Vealo {{$version}}</title>    <!-- Scripts -->



   
    <!-- Fonts -->
   <!--  <link rel="dns-prefetch" href="//fonts.gstatic.com"> -->


    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('fontawesome-free-5.15.3/css/all.min.css')}}">
    <script defer src="{{ asset('fontawesome-free-5.15.3/js/all.min.js')}}"></script>
    <!-- <link href="{{ asset('css/adminlte.min.css') }}" rel="stylesheet"> -->

   
</head>

<body class="hold-transition sidebar-mini layout-fixed">

    <div id="app">
        
        <div class="wrapper">

            <!-- Navbar -->
            <nav class="main-header navbar fixed-top navbar-dark">
                <!-- Left navbar links -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars ml-2" aria-hidden="true"></i>Minimizar Menú</i></a>
                    </li>
                </ul>

                

                <!-- Right navbar links -->
                <ul class="navbar-nav ml-auto">
                                 
                    <!-- Notifications Dropdown Menu -->
                    <li class="nav-item dropdown">
                        @can('guardarValorTasa')
                            <a href="{{route('cotizacion.tasa')}}" onclick="centeredPopup(this.href, 'myWindow', 700, 750); return false;" class="nav-link" >
                            
                            <span class="badge badge-warning navbar-badge" >                               
                                        
                            <i class="fas fa-dollar-sign"></i> Cotizacion
                            </span>
                        </a>
                        @endcan
                    </li> 
                </ul>
                 
            </nav>
            <!-- /.navbar -->

            <!-- Main Sidebar Container -->
            <aside class="main-sidebar sidebar-dark-primary elevation-4">
                <!-- Brand Logo -->
                <a href="{{ url('/') }}" class="brand-link">
                     
                    <span class="brand-text font-weight-light h4">Vealo {{$version}} SAN-FDO</span>
                </a>

                <!-- Sidebar -->
                <div class="sidebar">
                    <!-- Sidebar user panel (optional) -->
                    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                        <div class="image">
                            <img src="" class="img-circle elevation-2" alt="">
                        </div>
                        <div class="info">
                            <a href="#" class="d-block">
                                @guest
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Iniciar Sesión') }}</a>
                                @else
                                {{ Auth::user()->name }}
                                <a class="" href="{{ route('logout') }}" onclick="event.preventDefault();
                                           document.getElementById('logout-form').submit();">
                                    Cerrar Sesión
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                </form>

                                @endguest
                            </a>
                        </div>
                    </div>

                    <!-- Sidebar Menu -->
                    <nav class="mt-2">
                        <div class="accordion" id="accordionExample">
                            <ul class="nav  nav-sidebar flex-column">
                                <li class="nav-item">
                                    <a href="{{ asset('/')}}" class="{{ Request::path() === '/' ? 'nav-link active' : 'nav-link border-bottom border-secondary' }}">
                                        <i class="nav-icon fas fa-home"></i>
                                        <p>Inicio</p>
                                    </a>
                                </li>
                                @can('cuentasporpagar.inicio')
                                <li class="nav-item">
                                    <a href="#" class="{{ Request::segment(1) === 'cuentasporpagar' ? 'nav-link active' : 'nav-link border-bottom border-secondary' }}" data-toggle="collapse" data-target="#cuentasporpagar">
                                        <i class="fas fa-handshake {{ Request::segment(1) === 'cuentasporpagar' ? 'text-light' : 'text-primary' }} "></i>
                                        Cuentas Por Pagar
                                    </a>
                                </li>
                                @endcan
                            </ul>
                            
                            <div id="cuentasporpagar" class=" {{ Request::segment(1) === 'cuentasporpagar' ? 'collapse show ' : 'collapse' }} ml-2" data-parent="#accordionExample">

                                <ul class="nav nav-pills nav-treeview flex-column" data-widget="treeview" role="menu" data-accordion="true">
                                    
                                    <li class="nav-item">
                                        <a href="{{ route('cuentasporpagar.inicio')}}" class="{{ Request::path() === 'cuentasporpagar/inicio' ? 'nav-link active' : 'nav-link' }}">
                                            <!-- <i class="nav-icon fas fa-home"></i> -->
                                            <i class="nav-icon fas fa-hand-holding-usd"></i>
                                            Modos de Pago
                                        </a>
                                    </li>
                                    @can('cuentasporpagar.facturasPorPagar')
                                    <li class="nav-item">
                                        <a href="{{ route('cuentasporpagar.facturasPorPagar')}}"
                                            class="{{ Request::path() === 'cuentasporpagar/facturasPorPagar' ? 'nav-link active' : 'nav-link' }}">
                                            <i class="fas fa-file-download nav-icon text-white "></i>
                                            Ingreso Facturas
                                        </a>
                                    </li>                 
                                    
                                    @endcan
                                    
                                    @can('relacionPagoFacturasIndex')
                                    <li class="nav-item">
                                        <a href="{{ route('relacionPagoFacturasIndex')}}"
                                            class="{{ Request::path() === 'cuentasporpagar/relacion-pago-facturas' ? 'nav-link active' : 'nav-link' }}">
                                            <i class="fab fa-buffer nav-icon text-primary"></i>
                                            Facturas a relacionar
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{route('listadoFacturasCalculadas')}}"
                                            class="{{ Request::path() === 'cuentasporpagar/facturas-calculadas' ? 'nav-link active' : 'nav-link' }}">
                                            <i class="fa fa-calculator nav-icon text-warning"></i>
                                            Relacion Pago Facturas
                                        </a>
                                    </li>
                                                                
                                    @endcan
                                    @can('reportecuntaspagas')                                 
                                    
                                    <li class="nav-item">
                                        <a href="{{route('cuentasporpagar.facturasPagadas')}}"
                                            class="{{ Request::path() === 'cuentasporpagar/facturasPagadas' ? 'nav-link active' : 'nav-link' }}">
                                            <i class="fas fa-handshake text-success"></i>
                                            Facturas Pagadas
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('reportecuntaspagas')}}"
                                            class="{{ Request::path() === 'cuentasporpagar/cuentaspagadas-reportepagos' ? 'nav-link active' : 'nav-link' }}">
                                            <i class="fa fa-print nav-icon"></i>
                                            Reporte Pagos Bancos
                                        </a>
                                    </li> 
                                    <li class="nav-item">
                                        <a href="{{ route('reportePagoBolivares')}}"
                                            class="{{ Request::path() === 'cuentasporpagar/ReporteBolivares' ? 'nav-link active' : 'nav-link' }}">
                                            <i class="fa fa-print nav-icon"></i>
                                            Pagos Bs. de las Divisas
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('reporteRelacionPagosPorEmpresa')}}"
                                            class="{{ Request::path() === 'cuentasporpagar/reporte-cuentasporpagar-reportePagosEmpresa' ? 'nav-link active' : 'nav-link' }}">
                                            <i class="fa fa-print nav-icon"></i>
                                            Relacion Pagos Empresa
                                        </a>
                                    </li>                                   
                                    <li class="nav-item">
                                        <a href="{{ route('reportePagoPorProvedorTodasEmpresas')}}"
                                            class="{{ Request::path() === 'cuentasporpagar/reporte-cuentasporpagar-proveedorTodasEmpresa' ? 'nav-link active' : 'nav-link' }}">
                                            <i class="fa fa-print nav-icon"></i>
                                            Relacion Pago Proveedor
                                        </a>
                                    </li>
                                </ul> <!-- ul del menu cuentas por pagar -->    
                            </div>
                            @endcan 
                            </div><!-- fin div capa cuentas por pagar -->

                            <!-- div capa cuadres -->
                            @can('cuadres.index')
                            <ul class="nav  nav-sidebar flex-column">
                                <li class="nav-item">
                                    <a href="#" class="{{ Request::segment(1) === 'recaudo' ? 'nav-link active' : 'nav-link border-bottom border-secondary' }}" data-toggle="collapse" data-target="#cuadres">
                                    <i class="fas fa-cash-register text-warning"></i>
                                        <p>Cuadres</p>
                                    </a>
                                </li>
                            </ul>
                            <div id="cuadres" class=" {{ Request::segment(1) === 'recaudo' ? 'collapse show' : 'collapse' }} ml-2" data-parent="#accordionExample">
                                <ul class="nav nav-pills nav-treeview flex-column" data-widget="treeview" role="menu" data-accordion="true">
                                    
                                    <li class="nav-item">
                                        <a href="{{route('cuadres.index')}}" 
                                        class="{{ Request::path() === 'recaudo/cuadres' ? 'nav-link active' : 'nav-link' }}"><i class="fab fa-stack-exchange nav-icon mr-2"></i>Inicio</a>
                                    </li>
                                    
                                    @can('cuadres.vistaRegistrarCuadre')
                                    <li class="nav-item">
                                        <a href="{{route('cuadres.vistaRegistrarCuadre')}}" 
                                        class="{{ Request::path() === 'recaudo/cuadres-nuevo-registro' ? 'nav-link active' : 'nav-link' }}"><i class="fab fa-stack-exchange nav-icon mr-2"></i>Registro Cuadres</a>
                                    </li>
                                    @endcan
                                                                            
                                </ul>
                            </div>
                            @endcan
                            <!-- fin div capa cuadres -->

                            <!--  capa islr -->
                            @can('islr.index')
                            <ul class="nav  nav-sidebar flex-column">
                                <li class="nav-item">
                                    <a href="#" class="{{ Request::segment(1) === 'regisretenciones' ? 'nav-link active' : 'nav-link border-bottom border-secondary' }}" data-toggle="collapse" data-target="#islr">
                                        <i class="fas fa-chart-line text-danger"></i>
                                        <p>ISLR</p>
                                    </a>
                                </li>
                            </ul>    
                            <div id="islr" class=" {{ Request::segment(1) === 'regisretenciones' ? 'collapse show ' : 'collapse' }} ml-2" data-parent="#accordionExample">
                                <ul class="nav nav-pills nav-treeview flex-column" data-widget="treeview" role="menu" >
                                
                                    @can('islr.index')
                                    
                                    <li class="nav-item">
                                        <a href="{{ route('islr.index')}}"
                                            class="{{ Request::path() === 'regisretenciones' ? 'nav-link active' : 'nav-link' }}">
                                            
                                            <i class="fa fa-file"></i>Agregar Documento
                                        </a>
                                    </li>
                                    @endcan
                                    @can('islr.xml.listar')
                                    <li class="nav-item">
                                        <a href="{{route('islr.xml.listar')}}"
                                            class="{{ Request::path() === 'regisretenciones/xml-listar' ? 'nav-link active collapse show' : 'nav-link' }}">
                                            <i class="fas fa-file-excel "></i>
                                            Generar XML
                                        </a>
                                    </li>
                                        
                                    @endcan
                                    
                                    
                                                                
                                    @can('rrhh.index')
                                    
                                    <li class="nav-item">
                                        <a href="{{ route('rrhh.index')}}"
                                            class="{{ Request::path() === 'regisretenciones/rrhh' ? 'nav-link active' : 'nav-link' }}">
                                            <i class="fas fa-restroom"></i>
                                            RRHH Empleados
                                        </a>
                                    </li>
                                    @can('declarantes.index')
                                    <li class="nav-item">
                                        <a href="{{route('declarantes.index')}}"
                                            class="{{ Request::path() === 'regisretenciones/declarantes' ? 'nav-link active' : 'nav-link' }}">
                                            <i class="fas fa-user-shield"></i>
                                            RRHH Directivos
                                        </a>
                                    </li>
                                    @endcan
                                        
                                    @endcan
                                    
                                    
                                    @can('ut.index')
                                    
                                            
                                    <li class="nav-item">
                                        <a href="{{route('ut.index')}}"
                                            class="{{ Request::path() === 'regisretenciones/ut' ? 'nav-link active' : 'nav-link' }}">
                                            <i class="fas fa-chart-line"></i>
                                            Valor UT
                                        </a>
                                    </li>
                                    
                                    <li class="nav-item">
                                        <a href="{{route('retencion.index')}}"
                                            class="{{ Request::path() === 'regisretenciones/retencion' ? 'nav-link active' : 'nav-link' }}">
                                            <i class="fas fa-percentage"></i>
                                            % Retencion
                                        </a>
                                    </li>
                                    @endcan
                                    @can('contribuyente.index')
                                    <li class="nav-item">
                                        <a href="{{route('contribuyente.index')}}"
                                            class="{{ Request::path() === 'regisretenciones/contribuyentes' ? 'nav-link active' : 'nav-link' }}">
                                            <i class="fas fa-donate"></i>
                                            Contribuyentes
                                        </a>
                                    </li>                                         
                                        
                                    @endcan
                                </ul>
                            </div> @endcan <!-- fin capa islr -->
                            <!-- capa recepcion de divisas -->
                            @can('listar.operaciones.divisas')
                            <ul class="nav  nav-sidebar flex-column">
                                <li class="nav-item">
                                    <a href="#" class="{{ Request::segment(1) === 'divisas' ? 'nav-link active' : 'nav-link border-bottom border-secondary' }}" data-toggle="collapse" data-target="#recepcion_divisas">
                                    <i class="fas fa-money-bill text-success"></i>
                                        <p>Recepción de Divisas</p>
                                    </a>
                                </li>
                            </ul>    
                            <div id="recepcion_divisas" class=" {{ Request::segment(1) === 'divisas' ? 'collapse show ' : 'collapse' }} ml-2" data-parent="#accordionExample">
                                <ul class="nav nav-pills nav-treeview flex-column" data-widget="treeview" role="menu" data-accordion="true">
                                    <li class="nav-item">
                                        <a href="{{route('listar.operaciones.divisas')}}" 
                                        class="{{ Request::path() === 'divisas' ? 'nav-link active' : 'nav-link' }}"><i class="fa fa-calculator nav-icon "></i>Operacion en Divisas</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{route('listar.pago.movil')}}"
                                        class="{{ Request::path() === 'divisas/listar-pagos' ? 'nav-link active' : 'nav-link' }}"><i class="fa fa-phone nav-icon "></i>Procesar PagoMovil</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{route('divisas.reporte.general')}}"
                                        class="{{ Request::path() === 'divisas/reporte' ? 'nav-link active' : 'nav-link' }}"><i class="fa fa-chart-line nav-icon "></i>Reporte Administración</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{route('divisa.reporte.recaudo')}}"
                                        class="{{ Request::path() === 'divisas/reporte-recaudo' ? 'nav-link active' : 'nav-link' }}"><i class="fa fa-chart-bar nav-icon "></i>Reporte Recaudo calculadora</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{route('divisa.reporte.recaudo.movpagos')}}"
                                        class="{{ Request::path() === 'divisas/reporte-recaudo-movpago' ? 'nav-link active' : 'nav-link' }}"><i class="fa fa-chart-bar nav-icon "></i>Reporte Recaudo </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{route('porcentaje.puntosventas')}}" 
                                        class="{{ Request::path() === 'divisas/relacion-puntosdeventa' ? 'nav-link active' : 'nav-link' }}"><span class="Left badge badge-warning"><i class="fas fa-calculator nav-icon"></i>Relación Puntos de Venta </span></a>
                                    </li>    
                                </ul>
                            </div> <!-- fin de capa recepcion de divisas  -->
                            @endcan
                            <!-- capa Asistente de compra  -->
                            @can('asistentecompra.visualizadorPrecios')
                            <ul class="nav  nav-sidebar flex-column">
                                <li class="nav-item">
                                    <a href="#" class="{{ Request::segment(1) === 'asistentecompra' ? 'nav-link active' : 'nav-link border-bottom border-secondary' }}" data-toggle="collapse" data-target="#asistente_compra">
                                    <i class="fas fa-cart-plus"></i>
                                        <p>Asistente de Compra</p>
                                    </a>
                                </li>
                            </ul>    
                            <div id="asistente_compra" class=" {{ Request::segment(1) === 'asistentecompra' ? 'collapse show' : 'collapse' }} ml-2" data-parent="#accordionExample">
                                <ul class="nav nav-pills nav-treeview flex-column" data-widget="treeview" role="menu" data-accordion="true">
                                    
                                    <li class="nav-item">
                                        <a href="{{route('asistentecompra.visualizadorPrecios')}}"
                                        class="{{ Request::path() === 'asistentecompra/inicio' ? 'nav-link active' : 'nav-link' }}"><i class="fab fa-shopify nav-icon mr-2"></i>Productos a Comprar</a>
                                    </li>
                                    
                                                                            
                                </ul>
                            </div> 
                            @endcan
                            <!-- fin de capa Asistente de compras  -->
                            <!-- capa Informes adicionales -->
                            @can('habladores.index')
                            <ul class="nav  nav-sidebar flex-column">
                                <li class="nav-item">
                                    <a href="#" class="{{ Request::segment(1) === 'informes' ? 'nav-link active' : 'nav-link border-bottom border-secondary' }}" data-toggle="collapse" data-target="#informes_adicionales">
                                    <i class="fas fa-print text-info"></i>
                                        <p>Informes Adicionales</p>
                                    </a>
                                </li>
                            </ul>    
                            <div id="informes_adicionales" class=" {{ Request::segment(1) === 'informes' ? 'collapse show ' : 'collapse' }} ml-2" data-parent="#accordionExample">
                                <ul class="nav nav-pills nav-treeview flex-column" data-widget="treeview" role="menu" data-accordion="true">
                                    @can('habladores.index')
                                    <li class="nav-item">
                                        <a href="{{route('habladores.index')}}" 
                                        class="{{ Request::path() === 'informes/habladores' ? 'nav-link active' : 'nav-link' }}"><i class="fab fa-stack-exchange nav-icon mr-2"></i>Habladores</a>
                                    </li>
                                    @endcan
                                    @can('comisionPorVentas')
                                    <li class="nav-item">
                                        <a href="{{route('comisionPorVentas')}}"
                                        class="{{ Request::path() === 'informes/comision-ventas' ? 'nav-link active' : 'nav-link' }}"><i class="fas fa-cash-register nav-icon mr-2"></i>Comisión por ventas</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{route('empleadosComisionEspecial')}}"
                                        class="{{ Request::path() === 'informes/vendedores-comision' ? 'nav-link active' : 'nav-link' }}"><i class="fas fa-percentage nav-icon mr-2"></i>Definir Porcentajes para el calculo de comisión</a>
                                    </li>
                                    @endcan                                        
                                </ul>
                            </div> <!-- fin de capa Informes Adicionales  -->
                            @endcan

                            <!-- capa Retencion de Iva -->
                            @can('retencion.iva.index')
                            <ul class="nav  nav-sidebar flex-column">
                                <li class="nav-item">
                                    <a href="#" class="{{ Request::segment(1) === 'retencion-iva' ? 'nav-link active' : 'nav-link border-bottom border-secondary' }}" data-toggle="collapse" data-target="#retencion_iva">
                                    <i class="fas fa-donate " style="color:#D4A5FB"></i>
                                        <p>Retencion IVA</p>
                                    </a>
                                </li>
                            </ul>    
                            <div id="retencion_iva" class=" {{ Request::segment(1) === 'retencion-iva' ? 'collapse show ' : 'collapse' }} ml-2" data-parent="#accordionExample">
                                <ul class="nav nav-pills nav-treeview flex-column" data-widget="treeview" role="menu" data-accordion="true">
                                    @can('retencion.iva.index')
                                    <li class="nav-item">
                                        <a href="{{route('retencion.iva.index')}}"  
                                        class="{{ Request::path() === 'retencion-iva/index' ? 'nav-link active' : 'nav-link' }}"><i class="fas fa-receipt nav-icon mr-2"></i>Registro Documento</a>
                                    </li>
                                    @endcan
                                    @can('comisionPorVentas')
                                    <li class="nav-item">
                                        <a href="{{route('retencion.iva.listar')}}"
                                        class="{{ Request::path() === 'retencion-iva/listar-retencion' ? 'nav-link active' : 'nav-link' }}"><i class="fas fa-hand-holding-usd nav-icon mr-2"></i>Retención IVA</a>
                                    </li>                                   
                                    @endcan
                                    <li class="nav-item">
                                        <a href="{{route('retencion.iva.generarTxt')}}"
                                        class="{{ Request::path() === 'retencion-iva/generar-txt' ? 'nav-link active' : 'nav-link' }}"><i class="fas fa-file-prescription nav-icon mr-2"></i></i>Generar TXT</a>
                                    </li>                                        
                                </ul>
                            </div> <!-- fin de capa Retencion Iva -->
                            @endcan

                            @can('proveedor.index')
                            <ul class="nav  nav-sidebar flex-column">
                                
                                <li class="nav-item">
                                    <a href="{{ url('proveedor')}}" class="{{ Request::path() === 'proveedor' ? 'nav-link active' : 'nav-link border-bottom border-secondary' }}">
                                        <i class="fa fa-address-book "></i>
                                        <p>Proveedores </p> 
                                    </a>
                                </li>
                                
                            </ul>
                            @endcan
                            @can('bancos.index')
                            <ul class="nav  nav-sidebar flex-column">                                
                                    <li class="nav-item">
                                        <a href="{{ route('banco.index')}}" class="{{ Request::path() === 'bancos' ? 'nav-link active' : 'nav-link border-bottom border-secondary' }}">
                                            <i class="fa fa-university " ></i>

                                            <p>Bancos </p> 
                                        </a>
                                    </li>                               
                            </ul>  
                            @endcan  
                            <!-- capa configuracion -->
                            @can('admin.general.datosEmpresa')
                            <ul class="nav  nav-sidebar flex-column">
                                <li class="nav-item">
                                    <a href="#" class="{{ Request::segment(1) === 'admin' ? 'nav-link active' : 'nav-link border-bottom border-secondary' }}" data-toggle="collapse" data-target="#configuracion">
                                    <i class="fas fa-tools mr-1"></i>
                                        <p>Configuracion</p>
                                    </a>
                                </li>
                            </ul>
                            <div id="configuracion" class=" {{ Request::segment(1) === 'admin' ? 'collapse show' : 'collapse' }} ml-2" data-parent="#accordionExample">
                                <ul class="nav nav-pills nav-treeview flex-column" data-widget="treeview" role="menu" data-accordion="true">
                                    
                                    <li class="nav-item"><a class="{{ Request::path() === 'admin' ? 'nav-link active' : 'nav-link' }}" href="{{route('configuracionGeneral')}}">Configuración General</a></li>
                                    
                                    @can('admin.user.index')
                                    <li class="nav-item">
                                        <a class="{{ Request::path() === 'admin/user' ? 'nav-link active' : 'nav-link' }}" href="{{route('admin.user.index')}}">Usuarios</a>
                                    </li>
                                    @endcan
                                    @can('admin.role.index')                                
                                    <li class="nav-item"><a href="{{route('admin.role.index')}}" class="{{ Request::path() === 'admin/roles' ? 'nav-link active' : 'nav-link' }}">Roles</a></li>
                                    <li class="nav-item"><a href="{{route('admin.permiso.index')}}" class="{{ Request::path() === 'admin/permisos' ? 'nav-link active' : 'nav-link' }}">Permisos</a></li>                                
                                    @endcan
                                    <li ><hr class="dropdown-divider"></li>
                                    <li class="nav-item"><a class="{{ Request::path() === 'admin/empresas' ? 'nav-link active' : 'nav-link' }}" href="{{route('admin.empresas.index')}}">Empresas</a></li>
                                    <li class="nav-item"><a class="{{ Request::path() === 'bancos' ? 'nav-link active' : 'nav-link' }}" href="{{route('banco.index')}}">Entidades Bancarias</a></li>
                                    <li class="nav-item"><a class="{{ Request::path() === 'proveedor' ? 'nav-link active' : 'nav-link' }}" href="{{ url('proveedor')}}">Proveedores</a></li>
                                    <li><hr class="dropdown-divider"></li>                                    
                                    <li  class="nav-item"><a class="{{ Request::path() === 'admin/configuracion/cuentasPorPagar' ? 'nav-link active' : 'nav-link' }}" href="{{route('indexConfiguracionCuentasPorPagar')}}">Configuracion Cuentas <br> por pagar</a></li>
                                    <li><a  class="{{ Request::path() === 'admin/configuracion/retencionIva' ? 'nav-link active' : 'nav-link' }}" href="{{route('indexConfiguracionRetencionIva')}}">Configurar Retencion IVA</a></li>
                                </ul>
                            </div> <!-- fin capa configuracion  -->
                            @endcan 
                        </div> <!-- el acordion general -->

                    </nav>
                    <!-- /.sidebar-menu -->
                </div><!-- /.sidebar -->
                
            </aside>

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper bg-white">
                <!-- Content Header (Page header) -->
                <div class="content-header">
                    <p>Cabecera </p>
                </div>
                <!-- /.content-header -->

                <!-- Main content -->
                <section class="content bg-white">
                    @yield('content')
                   
                </section>
                
                
            </div>           

            <!-- Control Sidebar -->
            <aside class="control-sidebar control-sidebar-dark">
                <!-- Control sidebar content goes here -->
                <p>Esta seccion donde se encuentra</p>
                <!-- /.content-wrapper -->
            
            </aside>
            <!-- /.control-sidebar -->
            
            

            <!-- Control Sidebar -->
            <aside class="control-sidebar control-sidebar-dark">
                <!-- Control sidebar content goes here -->

            </aside>
            <!-- /.control-sidebar -->
        </div>
    </div>

    <!-- <div class=" text-center border-top">
        <footer class="d-print-none" style="position: absolute;"> -->
                <!-- NO QUITAR -->
                <!-- <strong>Elaborado por: Jose Rivero & Eric Leon | jarh18@gmail.com</strong>
        </footer>
    </div -->

        
    <script src="{{ asset('js/app.js')}}"></script>

    @yield('js')
    <script>
        function centeredPopup(url, winName, w, h) {
            /*centar la ventana pop up*/
            const dualScreenLeft = window.screenLeft !== undefined ? window.screenLeft : window.screen.left;
            const dualScreenTop = window.screenTop !== undefined ? window.screenTop : window.screen.top;

            const width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
            const height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

            const left = (width / 2) - (w / 2) + dualScreenLeft;
            const top = (height / 2) - (h / 2) + dualScreenTop;

            const newWindow = window.open(url, winName, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);

            if (window.focus) {
                newWindow.focus();
            }
        }
    </script>
</body>
</html>
