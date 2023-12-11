@extends('layouts.app')
@section('content')
    <div class="container">
        <h3>Editar La Factura Cargada</h3>
        <form action="{{route('updateFacturasPorPagar',$factura->id)}}" method="post">

            @csrf @method('put')
            <input type="hidden" name="url_de_retorno" value="{{$urlRetorno}}">
            <label for="">Proveedor</label>
             <input type="text" disabled value="{{$factura->proveedor_rif}}    {{$factura->proveedor_nombre}}" class="form-control">
             <div class="row">
                <div class="col">
                    <label for="">Factura</label>
                    <input type="text" disabled value="{{$factura->documento}}" class="form-control">
                </div>
                <div class="col">
                    <label for="">Nº Control</label>
                    <input type="text" disabled  value="{{$factura->n_control}} " class="form-control">
                </div>
             </div>
             <label for="">Fecha Factura</label>
             <input type="text" disabled  value="{{$factura->fecha_factura}}" class="form-control">          
             <div class="row">
                <div class="col">
                    <label for="">Dias de Credito</label>
                    <input type="text" name="dias_credito" value="{{$factura->dias_credito}}" class="form-control">
                </div>
                <div class="col">
                    <label for="">Porcentaje de Descuento</label>
                    <input type="text" name="porcentaje_descuento" value="{{$factura->porcentaje_descuento}}" class="form-control">
                </div>
                @can('agregar.tasadivisa.afactura')
                <div class="col">
                    <label for="">Valor Tasa Factura</label>
                   <input type="text" name="valor_tasa" value="{{$factura->moneda_secundaria}}" class="form-control" placeholder="Valor de la Tasa">
                </div>
                @endcan
             </div>             
             <div class="float-end">
                <button class="btn btn-primary float-right my-3">Actualizar</button> 
             </div>
                       
               
        </form>
    </div>
@stop