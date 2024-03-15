@extends('layouts.app')
@section('content')
    <h3>Parametros de configuracion en Retencion de iva</h3>
    
            
    <div class="row">
        <div class="col-6">
            <form action="{{route('guardarPorcentajeRetencionIva')}}" method="post">
            @csrf
                <div class="card">
                    <div class="card-body">
                        <label for="">Registre los porcentajes de retencion IVA que se aplicara a cada proveedor</label>
                        <input type="text" class="form-control" name="porcentaje" placeholder="Ej: 100,75,50,25" >
                        <label for="">Operacion por defecto de la retencion de IVA</label>
                        
                        <input type="radio" name="compra_venta" value="C" id="compra" @if($tipoOperacion=='C')checked @endif><label for="compra">Compra</label>
                        <input type="radio" name="compra_venta" value="V" id="venta"  @if($tipoOperacion=='V')checked @endif><label for="venta">Venta</label>
                        <button type="submit" class="btn btn-primary float-right my-2">Guardar</button>
                    </div>
                </div>                
    
            </form>

            <table border=1 class=" table mx-2">
                <tr>
                    <th>Porcentajes Registrados</th>
                    <th>Accion</th>
                </tr>
                @foreach($porceRetencionIva as $valor)
                <tr>
                    <td>{{$valor->porcentaje}}</td>
                    <td><a onclick="eliminarPorcentaje('{{$valor->id}}')" class="text-danger">Eliminar</a></td>
                </tr>
                @endforeach
            </table>                    
        </div>
    </div>
   
        

@endsection
@section('js')
<script type="text/javascript">
    function eliminarPorcentaje(id){
        let valor = confirm("Desea eliminar el porcentaje seleccionado?");
        if(valor){
            location.href="eliminar-porce-retencioniva/"+id;
        }
    } 
</script>
@endsection