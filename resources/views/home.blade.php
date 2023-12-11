@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row-cols-12 row-cols-sm-2 text-center mt-5">
        <span class="h1 text-primary font-weight-lighter">{{session('nombre_general_empresa')}} Sistema De Gestion y Control Administrativo</span>
    </div>
    <hr class="mb-5">
    <div class="row d-flex justify-content-start justify-content-sm-center">
        @can('islr.index')
        <div class="card m-1 offset-3" style="width: 23rem; border-radius: 6px;">
            <div class="card-body d-inline-flex m-0 p-0 border" style="border-radius: 6px;">
                <div class="row-cols">
                    <div class="bg-modulo-card1 m-0 p-0 d-flex justify-content-center align-content-center" style="width: 7rem; height: 100%; border-radius: 6px;">
                        <img src="{{asset('imagen/bg-card1.png')}}" class="mt-5" style="width: 92px; height: 92px;">
                    </div>
                </div>
                <div class="row-col m-3">
                    <h4 class="card-title text-danger font-weight-bold float-right">DECLARACION DEL ISLR</h4>
                    <p class="card-text text-dark text-center">Gestion de calculo de impuesto sobre la renta, {{session('nombre_general_empresa')}}</p>
                    <a href="{{route('islr.index')}}" class="btn btn-danger d-flex justify-content-center">Ir al modulo<i class="fas fa-arrow-circle-right ml-2 mt-1"></i></a>
                </div>
            </div>
        </div>
        @endcan
        @can('cuentasporpagar.facturasPorPagar')
        <div class="card m-1 offset-3" style="width: 23rem; border-radius: 6px;">
            <div class="card-body d-inline-flex m-0 p-0 border" style="border-radius: 6px;">
                <div class="row-cols">
                    <div class="bg-modulo-card1 m-0 p-0 d-flex justify-content-center align-content-center" style="width: 7rem; height: 100%; border-radius: 6px;">
                        <img src="{{asset('imagen/bg-card2.png')}}" class="mt-5" style="width: 92px; height: 92px;">
                    </div>
                </div>
                <div class="row-col m-3">
                    <h4 class="card-title font-weight-bold text-primary float-right">CUENTAS POR PAGAR</h4>
                    <p class="card-text text-dark text-center">Registro y control de cuentas por pagar, {{session('nombre_general_empresa')}}</p>
                    <a href="{{route('cuentasporpagar.inicio')}}" class="btn btn-primary d-flex justify-content-center">Ir al modulo<i class="fas fa-arrow-circle-right ml-2 mt-1"></i></a>
                </div>
            </div>
        </div>
        @endcan

        @can('divisasCustodio.create')
        <div class="card m-1 offset-3" style="width: 23rem; border-radius: 6px;">
            <div class="card-body d-inline-flex m-0 p-0 border" style="border-radius: 6px;">
                <div class="row-cols">
                    <div class="bg-modulo-card1 m-0 p-0 d-flex justify-content-center align-content-center" style="width: 7rem; height: 100%; border-radius: 6px;">
                        <img src="{{asset('imagen/bg-card4.png')}}" class="mt-5" style="width: 92px; height: 92px;">
                    </div>
                </div>
                <div class="row-col m-3">
                    <h4 class="card-title font-weight-bold text-success float-right">RECEPCION DE DIVISAS</h4>
                    <p class="card-text text-dark text-center">Registro y control de divisas y pago movil, {{session('nombre_general_empresa')}}</p>
                    <a href="{{route('listar.operaciones.divisas')}}" class="btn btn-success d-flex justify-content-center">Ir al modulo<i class="fas fa-arrow-circle-right ml-2 mt-1"></i></a>
                </div>
            </div>
        </div>
        @endcan
        @can('informesAdicionales.index')
        <div class="card m-1 offset-3" style="width: 23rem; border-radius: 6px;">
            <div class="card-body d-inline-flex m-0 p-0 border" style="border-radius: 6px;">
                <div class="row-cols">
                    <div class="bg-modulo-card1 m-0 p-0 d-flex justify-content-center align-content-center" style="width: 7rem; height: 100%; border-radius: 6px;">
                        <img src="{{asset('imagen/bg-card5.jpg')}}" class="mt-5 rounded-circle" style="width: 92px; height: 92px;">
                    </div>
                </div>
                <div class="row-col m-3">
                    <h4 class="card-title font-weight-bold float-right" style="">Informes Adicionales</h4>
                    <p class="card-text text-dark text-center">Multiples Reportes de, {{session('nombre_general_empresa')}}</p>
                    <a href="{{route('informesAdicionales.index')}}" class="btn d-flex justify-content-center text-white" style="background-color:#cc5d4c">Ir al modulo<i class="fas fa-arrow-circle-right ml-2 mt-1"></i></a>
                    <a href="{{url('/informes/habladores/manual')}}" class="float-right">Ayuda</a>
                </div>
            </div>
        </div>
        @endcan
        @can('cuadres.index')
        <div class="card m-1 offset-3" style="width: 23rem; border-radius: 6px;">
            <div class="card-body d-inline-flex m-0 p-0 border" style="border-radius: 6px;">
                <div class="row-cols">
                    <div class="bg-modulo-card1 m-0 p-0 d-flex justify-content-center align-content-center" style="width: 7rem; height: 100%; border-radius: 6px;">
                        <img src="{{asset('imagen/bg-card6.jpeg')}}" class="mt-5 rounded-circle" style="width: 92px; height: 92px;">
                    </div>
                </div>
                <div class="row-col m-3">
                    <h4 class="card-title font-weight-bold text-info ">CUADRES</h4>
                    <p class="card-text text-dark text-center">Registro y control de cuadres, {{session('nombre_general_empresa')}}</p>
                    <a href="{{route('cuadres.index')}}" class="btn btn-info d-flex justify-content-center">Ir al modulo<i class="fas fa-arrow-circle-right ml-2 mt-1"></i></a>
                </div>
            </div>
        </div>
        @endcan
        @can('admin.usuarios.index')
        <div class="card m-1 offset-3" style="width: 23rem; border-radius: 6px;">
            <div class="card-body d-inline-flex m-0 p-0 border" style="border-radius: 6px;">
                <div class="row-cols">
                    <div class="bg-modulo-card1 m-0 p-0 d-flex justify-content-center align-content-center" style="width: 7rem; height: 100%; border-radius: 6px;  background url('{{asset('imagen/bg2.jpg')}}');">
                        <img src="{{asset('dist/img/configuracion.jpg')}}" class="mt-5 rounded-circle border border-warning" style="width: 92px; height: 92px;">
                        
                    </div>
                </div>
                <div class="row-col m-3">
                    <h4 class="card-title font-weight-bold text-darck float-right">CONFIGURACION DEL SISTEMA</h4>
                    <p class="card-text text-dark text-center">Contro de usuario y empresas registradas</p>
                    <a href="{{route('usuarios.index')}}" class="btn btn-dark d-flex justify-content-center">Ir al modulo<i class="fas fa-arrow-circle-right ml-2 mt-1"></i></a>
                </div>
            </div>
        </div>
        @endcan
    </div>
</div>
@endsection
