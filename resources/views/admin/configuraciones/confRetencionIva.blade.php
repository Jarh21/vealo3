@extends('layouts.app')
@section('content')
    <h3>Parametros de configuracion en Retencion de iva</h3>
    
            
    <form action="{{route('guardarPorcentajeRetencionIva')}}" method="post">
        @csrf
        <div class="card">
            <div class="card-header">
                <h4>Porcentajes de Retencion IVA</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-4">    
                        <label for="">Registre los porcentajes de retencion IVA que se aplicara a cada proveedor</label>
                        <input type="text" class="form-control" name="porcentaje" placeholder="Ej: 100,75,50,25" >
                        <label for="">Operacion por defecto de la retencion de IVA</label>                        
                        <input type="radio" name="compra_venta" value="C" id="compra" @if($tipoOperacion=='C')checked @endif><label for="compra">Compra</label>
                        <input type="radio" name="compra_venta" value="V" id="venta"  @if($tipoOperacion=='V')checked @endif><label for="venta">Venta</label>                        
                    </div>
                    <div class="col-4">
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
        <div class="card">           
            <div class="card-header">
                Comprobante de retencion
            </div>
            <div class="card-body">
                <input type="checkbox" id="direccion_proveedor_en_comprobante_retencioniva" name="direccion_proveedor_en_comprobante_retencioniva" @if($direccion_proveedor_en_comprobante_retencioniva=='on')checked @endif>{{$direccion_proveedor_en_comprobante_retencioniva}}
                <label for="direccion_proveedor_en_comprobante_retencioniva">Mostrar la direccion fiscal del proveedor en el comprobante de retención</label><br>
                <input type="checkbox" name="total_a_cancelar_en_comprobante_retencioniva" id="total_a_cancelar_en_comprobante_retencioniva" @if($total_a_cancelar_en_comprobante_retencioniva=='on')checked @endif>{{$total_a_cancelar_en_comprobante_retencioniva}}
                <label for="total_a_cancelar_en_comprobante_retencioniva">Mostrar el total a cancelar en el comprobante de retencion</label>
            </div>            
        </div>
        <button type="submit" class="btn btn-primary float-right my-2">Guardar</button>
    </form>
        

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