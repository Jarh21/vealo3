@extends('layouts.app')
@section('content')
    <h3>Parametros de configuracion en Retencion de iva</h3>
    <div class="card">
        <div class="card-body">
            <form action="{{route('guardarPorcentajeRetencionIva')}}" method="post">
                @csrf
                <div class="row">
                    <div class="col-6">
                    <label for="">Registre los porcentajes de retencion IVA que se aplicara a cada proveedor</label>
                    <input type="text" class="form-control" name="porcentaje" placeholder="Ej: 100,75,50,25" required>
                    <button type="submit" class="btn btn-primary float-right my-2">Guardar</button>
                    </div>
                </div>
                
            </form>
            <div class="row">
                <div class="col-6">
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