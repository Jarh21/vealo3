@extends('layouts.app')

@section('content')
<div class="content">
<h3>Cambio de Vendedor</h3>
    @if(isset($datosFactura))
    <div class="card">
    <div class="card-body">
    
        <form action="{{route('guardarCambioVendedor')}}" method="post">
            @csrf
            @foreach($datosFactura as $datos)
                <label for="">Fecha</label>  {{$datos->fecha}}<br>
                <label for="">Cliente</label>  {{$datos->cliente}} <br>
                <label for="">Vendedor(a)</label>  {{$datos->nombreVendedor}}
                <label for="">--> Nuevo Vendedor Asignado</label>
                <input type="hidden" name="id" value="{{$datos->id}}">
                <input type="hidden" name='fechaini' value='{{$fechaini}}'>
                <input type="hidden" name='fechafin' value='{{$fechafin}}'>
                <input type="hidden" name='cod_vendedor_antiguo' value='{{$datos->codigoVendedor}}'>
                <input type="hidden" name='nombre_vendedor_antiguo' value='{{$datos->nombreVendedor}}'>
                <select name="nuevo_vendedor" id="" class='' required>
                    <option value="">--Seleccione--</option>
                    @foreach($listaVendedores as $vendedor)
                    <option value="{{$vendedor->codusua}}|{{$vendedor->usuario}}">{{$vendedor->usuario}}</option>
                    @endforeach
                </select>                
            @endforeach
            <button type="submit" class="btn btn-primary float-right ">Guardar</button>
        </form>
    </div>
    </div>
    @endif

</div>
@endsection
