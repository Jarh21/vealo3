<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{config('app.name')}} </title>    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" ></script>
    <script src="{{asset('js/jquery-3.5.1.min.js')}}"></script>
    <script src="{{ asset('dist/js/adminlte.js')}}"></script>
    

    <!-- Font Awesome Icons -->
    <!--<link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css')}}">-->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free-5.15.3/css/all.min.css')}}">
    <script defer src="{{ asset('plugins/fontawesome-free-5.15.3/js/all.min.js')}}"></script>
    
    <!-- Fonts -->
    <!--<link rel="dns-prefetch" href="//fonts.gstatic.com">-->
    <!--<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">-->

    <!-- Styles -->
    <link href="{{ asset('dist/css/adminlte.min.css') }}" rel="stylesheet">
    <link type="text/css" href="{{asset('css/bootstrap4.5.2.css')}}" rel="stylesheet" >
    <!--<link rel="stylesheet" type="text/css" href="{{asset('css/custom.css')}}">-->
    @yield('css')
   
</head>
<body>
    <div class="container-fluid bg-warning">
        <header>
            
            <nav class="navbar navbar-light bg-warning">
                <span class="navbar-brand mb-0 h1">Grupo Farma Descuento</span>
            </nav>
               
        </header>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-6">
                <div class="card mt-5">
                    <div class="card-header">
                        Empresas del Grupo Farmadescuento
                    </div>
                    <div class="card-body">
                        <table class="table">                   
                        
                        @foreach($empresas as $empresa)
                            <tr>
                                <td><a href="{{route('cambioempresa.solicitud-divisas',$empresa->keycodigo)}}">{{$empresa->rif}} {{$empresa->nombre}}</a></td>
                            </tr>
                        @endforeach
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <ul class="mt-5">
                    <div class="alert alert-success">
                        <li>Seleccione la empresa a utilizar</li>
                    </div>
                    <div class="alert alert-primary">
                        <li>Al seleccionar la empresa todas las operaciones se realizaran en la empresa seleccionada</li>
                    </div>
                </ul>
            </div>
        </div>
        
    </div>
</body>
 @yield('js')