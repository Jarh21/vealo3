@extends('layouts.app')
@section('content')
    <div class="container">
        <h3>Configuracion General</h3><hr>
        <form action="{{route('guardarConfiguracionGeneral')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="container">
                <div class="row my-3">
                    <div class="col-4">
                    <label for="pago_facturas_desde_facturas_por_pagar">Nombre de la Empresa</label>
                    </div>
                    <div class="col">
                    <input type="text" name="nombre_general_empresa" class="form-control" value="{{$nombreEmpresa ?? ''}}" >               </div>
                </div>
                <div class="row my-3">
                    <div class="col-4">
                        <label for="logo_empresa">Logo de la empresa</label>
                    </div>
                    <div class="col">
                        <input type="file" name="logo_empresa" id="logo_empresa" class="form-control">
                    </div>
                    <div class="col">
                        @if(!empty($logoEmpresa))
                            <img src="{{asset($logoEmpresa)}}" alt="" style="width:40px;" >
                        @endif                        
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
            
        </form>
    </div>
@endsection