@extends('layouts.app')
@section('content')
<h4>Editar Retencion IVA <a href="{{route('retencion.iva.listar')}}" class='btn btn-warning btn-sm float-right'>< Regresar</a></h4><hr>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col">
                <label for="">Comprobante</label><b>{{$retencionIva->comprobante}}</b><br>
                <b>Agente</b> {{$retencionIva->rif_agente}} {{$retencionIva->nom_agente}}<br>
                <label for="">Datos del Retenido</label>
                <select name="proveedorRif"  id="proveedorRif" class="js-example-basic-single " style="width: 100%;" title="Seleccionar el proveedor de la facturas del siace" >
                <option value=""></option>
                    @if(isset($proveedores))
                        @foreach($proveedores as $proveedor)
                            <option value="{{$proveedor->rif}}|{{$proveedor->porcentaje_retener}}" @if($proveedor->rif==$retencionIva->rif_retenido)selected @endif>{{$proveedor->rif}} {{$proveedor->nombre}} ({{$proveedor->porcentaje_retener ?? 'No tiene'}}%)</option>		
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col">
                <label for="">Fecha</label>
                <input type="date" value="{{$retencionIva->fecha}}"><br>
                <label for="">Cheque o Referencia de Pago</label>
                <input type="text" value="{{$retencionIva->cheque}}"><br>
            </div>
        </div>
        
    </div>
</div>
<detalle-retencion-iva :comprobante="{{$retencionIva->comprobante}}"></detalle-retencion-iva>
<div class="row align-items-center">
    <div class="col text-center">
        <button class="btn btn-warning text-center">Actualizar Retención</button>
    </div>
    
</div>
@endsection

@section('js')
    <script type='text/javascript'>
        /****************************************************************************************************** */
			// select 2
			$('.js-example-basic-single').select2({			
				placeholder: 'Seleccione el proveedor',    	
				/* maximumSelectionLength:1, */
			});
    </script>
@endsection