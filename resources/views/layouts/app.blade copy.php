<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>



    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <!-- <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet"> -->
    <!-- <script src="{{ mix('/js/app.js') }}"></script> -->
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('fontawesome-free-5.15.3/css/all.min.css')}}">
    <script defer src="{{ asset('fontawesome-free-5.15.3/js/all.min.js')}}"></script>
</head>
<body>
    <div id="app" >
        
        <nav class="navbar navbar-expand-md fixed-top navbar-light shadow-sm bg-navbar d-print-none" >
        <!-- <nav class="navbar navbar-expand-md navbar-light shadow-sm bg-navbar" > -->
            <div class="container-fluid">
                <a class="navbar-brand h5" href="{{ url('/home') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                    @can('islr.index')    
                        <li class="nav-item  ">
                            <a class="nav-link dropdown-toggle " href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-chart-line text-danger mr-1"></i>ISLR
                            </a>
                            <div class="dropdown-menu mega-menu">
                                <div class="row">
                                    <div class="col-md-3">
                                        <img src="{{asset('imagen/bg-card1.png')}}" alt="" class="img-fluid">
                                        <hr>
                                        <h4 class="text-danger">Impuesto Sobre la Renta</h4>
                                        <p>Gestion de calculo de impuesto sobre la renta, GRUPO FARMA DESCUENTO</p>
                                    </div>
                                    <div class="col-md-3">
                                        <h5><strong class="sub-menu-heading">Registro Retención</strong></h5><hr>
                                        <p><i class="fa fa-file"></i><a href="{{ route('islr.index')}}" class="ml-2">Agregar Documento</a></p>
                                        <p><i class="fas fa-file-excel text-success"></i><a href="{{route('islr.xml.listar')}}" class="ml-2">Generar XML</a></p>
                                        <hr>
                                        <p><i class="fa fa-address-book nav-icon"></i><a href="{{ url('proveedor')}}" class="ml-2">Proveedores</a></p>
                                    </div>
                                    <div class="col-md-3">
                                        <h5><strong class="sub-menu-heading">Recursos Humanos</strong></h5><hr>
                                        <p><i class="fas fa-restroom"></i><a href="{{ url('rrhh')}}" class="ml-2">Empleados</a></p>
                                        <p><i class="fas fa-user-shield"></i><a href="{{url('/declarantes')}}" class="ml-2">Directivos</a></p>
                                    </div>
                                    <div class="col-md-3">
                                        <h5><strong class="sub-menu-heading">Configuración</strong></h5><hr>
                                        <p><i class="fas fa-chart-line"></i><a href="{{route('ut.index')}}" class="ml-2">Valor de Unidad Tributaria</a></p>
                                        <p><i class="fas fa-percentage"></i><a href="{{url('/retencion')}}" class="ml-2"> Determinacion de la Retención</a></p>
                                        <p><i class="fas fa-donate"></i><a href="{{url('/contribuyentes')}}" class="ml-2">Contribuyentes</a></p>
                                    </div>
                                </div>

                            </div>
                        </li>
                        @endcan
                        @can('cuentasporpagar.inicio')
                        <li class="nav-item ">
                            <a class="nav-link dropdown-toggle " href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-handshake text-primary mr-1"></i>Ceuntas por Pagar
                            </a>
                            <div class="dropdown-menu mega-menu">
                                <div class="row">
                                    <div class="col-md-3">
                                        <img src="{{asset('imagen/bg-card2.png')}}" alt="" class="img-fluid" style="width: 92px; height: 92px;">
                                        <hr>
                                        <h4 class="text-primary">Cuentas Por Pagar</h4>
                                        <p>Registro y control de cuentas por pagar, GRUPO FARMA DESCUENTO</p>
                                    </div>
                                    @can('cuentasporpagar.inicio')
                                    <div class="col-md-3">
                                        <h5><strong class="sub-menu-heading">Inicio</strong></h5><hr>
                                        <p><i class="nav-icon fas fa-hand-holding-usd"></i><a href="{{ route('cuentasporpagar.inicio')}}" class="ml-2">Modo de Pago</a></p>
                                        <p><i class="fa fa-address-book nav-icon"></i><a href="{{ url('proveedor')}}" class="ml-2">Proveedores</a></p>
                                        <p><i class="fa fa-university nav-icon" ></i><a href="{{ url('bancos')}}" class="ml-2">Banco</a></p>
                                                                                
                                    </div>
                                    @endcan
                                    @can('cuentasporpagar.facturasPorPagar')
                                    <div class="col-md-3">
                                        <h5><strong class="sub-menu-heading">Relación Facturas</strong></h5><hr>
                                        @can('cuentasporpagar.facturasPorPagar')<p><i class="fas fa-file-download nav-icon text-primary"></i><a href="{{ route('cuentasporpagar.facturasPorPagar')}}" class="ml-2">Ingreso Facrturas</a></p>@endcan
                                        @can('relacionPagoFacturasIndex')<p><i class="fab fa-buffer nav-icon text-darck"></i><a href="{{ route('relacionPagoFacturasIndex')}}" class="ml-2">Facturas a relacionar</a></p>@endcan
                                        @can('listadoFacturasCalculadas')<p><i class="fa fa-calculator nav-icon text-warning"></i><a href="{{route('listadoFacturasCalculadas')}}" class="ml-2">Facturas Calculadas</a></p>@endcan
                                    </div>
                                    @endcan
                                    <div class="col-md-3">
                                        <h5><strong class="sub-menu-heading">Reportes</strong></h5><hr>
                                        @can('reportecuntaspagas')<p><i class="fas fa-handshake text-primary "></i><a href="{{route('reportecuntaspagas')}}" class="ml-2">Facturas Pagadas</a></p>@endcan
                                        @can('reportePagoBolivares')<p><i class="fas fa-percentage"></i><a href="{{route('reportePagoBolivares')}}" class="ml-2"> Pagos Bs. de las Divisas</a></p>@endcan
                                        @can('reporteRelacionPagosPorEmpresa')<p><i class="fas fa-store"></i><a href="{{route('reporteRelacionPagosPorEmpresa')}}" class="ml-2">Relacion Pagos Empresa</a></p>@endcan
                                        @can('reportePagoPorProvedorTodasEmpresas')<p><i class="fas fa-user-tie"></i><a href="{{route('reportePagoPorProvedorTodasEmpresas')}}" class="ml-2">Relacion Pago Proveedores</a></p>@endcan
                                    </div>
                                </div>

                            </div>
                        </li>
                        @endcan
                        @can('listar.operaciones.divisas')
                        <li class="nav-item  ">
                            <a class="nav-link dropdown-toggle " href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-money-bill-alt text-success mr-1"></i>Recepción de Divisas
                            </a>
                            <div class="dropdown-menu mega-menu">
                                <div class="row">
                                    <div class="col-md-4">
                                        <img src="{{asset('imagen/bg-card4.png')}}" alt="" class="img-fluid" style="width: 92px; height: 92px;">
                                        <hr>
                                        <h4 class="text-success">Recepción de Divisas</h4>
                                        <p>Registro y control de divisas y pago movil, GRUPO FARMA DESCUENTO</p>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col">
                                                <h5><strong class="sub-menu-heading">Inicio</strong></h5><hr>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        
                                                        <p><i class="fa fa-calculator nav-icon "></i><a href="{{route('listar.operaciones.divisas')}}" class="ml-2">Operacion en Divisas</a></p>
                                                        <p><i class="fa fa-phone nav-icon "></i><a href="{{route('listar.pago.movil')}}"class="ml-2">Procesar PagoMovil</a></p>
                                                        <p><i class="fa fa-chart-line nav-icon "></i><a href="{{route('divisas.reporte.general')}}"class="ml-2">Reporte Administración</a></p>
                                                                
                                                    </div>
                                                    <div class="col-md-4">
                                                        <p><i class="fa fa-chart-bar nav-icon "></i><a href="{{route('divisa.reporte.recaudo')}}"class="ml-2">Reporte Recaudo</a></p>
                                                        <p><i class="fas fa-calculator nav-icon"></i><a href="{{route('porcentaje.puntosventas')}}" class="ml-2"><span class="Left badge badge-warning">Relación Puntos de Venta </span></a></p>                             
                                                    </div>
                                                    <div class="col-md-4">

                                                    </div>
                                                </div>                                                
                                            </div>    
                                        </div>    
                                    </div>
                                                                        
                                </div>

                            </div>
                        </li>
                        @endcan
                        @can('informesAdicionales.index')
                        <li class="nav-item ">
                            <a class="nav-link dropdown-toggle " href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-print text-info"></i>Informes Adicionales
                            </a>
                            <div class="dropdown-menu mega-menu">
                                <div class="row">
                                    <div class="col-md-3">
                                        <img src="{{asset('imagen/bg-card5.jpg')}}" alt="" class="rounded-circle" style="width: 92px; height: 92px;">
                                        <hr>
                                        <h4 class="text-primary">Informes Adicionales</h4>
                                        <p>Multiples Reportes de, GRUPO FARMA DESCUENTO</p>
                                    </div>
                                    <div class="col-md-3">
                                        <h5><strong class="sub-menu-heading">Herramientas</strong></h5><hr>
                                        @can('habladores.index')<p><i class="fab fa-stack-exchange nav-icon mr-2"></i><a href="{{route('habladores.index')}}" class="">Habladores</a></p>@endcan                                        
                                                                                
                                    </div>                                    
                                    <div class="col-md-3">
                                        <h5><strong class="sub-menu-heading">Reportes</strong></h5><hr>                                        
                                        @can('comisionPorVentas')<p><i class="fas fa-cash-register nav-icon mr-2"></i><a href="{{route('comisionPorVentas')}}" class="">Comisión por ventas</a></p>@endcan
                                        @can('comisionPorVentas')<p><i class="fas fa-percentage nav-icon mr-2"></i><a href="{{route('empleadosComisionEspecial')}}" class="">Definir Porcentajes para el calculo de comisión</a></p>@endcan
                                    </div>
                                    
                                </div>

                            </div>
                        </li>
                        @endcan
                        @can('cuadres.index')
                        <li class="nav-item ">
                            <a class="nav-link dropdown-toggle " href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-cash-register text-warning mr-1"></i>Cuadres
                            </a>
                            <div class="dropdown-menu mega-menu">
                                <div class="row">
                                    <div class="col-md-3">
                                    <img src="{{asset('imagen/bg-card6.jpeg')}}" class="rounded-circle" style="width: 92px; height: 92px;">

                                        <hr>
                                        <h4 class="text-primary">Cuadres</h4>
                                        <p>Multiples Reportes de, GRUPO FARMA DESCUENTO</p>
                                    </div>
                                    <div class="col-md-3">
                                        <h5><strong class="sub-menu-heading">Principal</strong></h5><hr>
                                        @can('cuadres.index')<p><a class="dropdown-item" href="{{route('cuadres.index')}}">Inicio</a></p>@endcan

                                        @can('cuadres.vistaRegistrarCuadre')<p><a class="dropdown-item" href="{{route('cuadres.vistaRegistrarCuadre')}}">Registro Cuadres</a></p>@endcan
                                                                               
                                                                               
                                    </div>                                    
                                    
                                </div>

                            </div>
                        </li>
                        @endcan                        
                        @can('admin.general.datosEmpresa')
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle " href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-tools mr-1"></i>Administrador
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="{{route('admin.general.datosEmpresa')}}">General</a></li>
                                <li><hr class="dropdown-divider"></li>
                                @can('admin.user.index')<li><a class="dropdown-item" href="{{route('admin.user.index')}}">Usuarios</a></li>@endcan
                                @can('admin.role.index')                                
                                <li><a href="{{route('admin.role.index')}}" class="dropdown-item">Roles</a></li>
                                <li><a href="{{route('admin.permiso.index')}}" class="dropdown-item">Permisos</a></li>                                
                                @endcan
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{route('admin.empresas.index')}}">Empresas</a></li>
                                <li><a class="dropdown-item" href="{{route('banco.index')}}">Entidades Bancarias</a></li>
                                <li><a class="dropdown-item" href="{{ url('proveedor')}}">Proveedores</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{route('configuracionGeneral')}}">Configuración General</a></li>
                                <li><a class="dropdown-item" href="{{route('indexConfiguracionCuentasPorPagar')}}">Configuracion Cuentas por pagar</a></li>
                            </ul>
                        </li>
                        @endcan
                    </ul>
                    </div>   
                    
                    
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto left-right">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                    
                <!-- </div> -->
            </div>
            
        </nav>
        <div class="py-5"></div>
        <main >
            @yield('content')
        </main>
    </div>
        <!-- Scripts -->
        
        <script src="{{ asset('js/app.js')}}"></script>
        @yield('js')
</body>
</html>
