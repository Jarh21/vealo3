@extends('layouts.app')
@section('content')
<h4>Editar Retencion IVA <a href="{{route('retencion.iva.listar')}}" class='btn btn-warning btn-sm float-right'>< Regresar</a></h4><hr>
<form action="{{route('retencion.iva.update_retencion')}}" method="post">
    @csrf
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-6">

                    <label for="">Comprobante</label><b>{{$retencionIva->comprobante}}</b>
                    <input type="hidden" name="comprobante" value="{{$retencionIva->comprobante}}">
                    <b>Agente</b> {{$retencionIva->rif_agente}} {{$retencionIva->nom_agente}}
                    <label for="">Datos del Retenido</label>                    
                    <select name="proveedorRif"  id="proveedorRif" class="js-example-basic-single " style="width: 100%;" title="Seleccionar el proveedor de la facturas del siace" >
                    <option value=""></option>
                        @if(isset($proveedores))
                            @foreach($proveedores as $proveedor)
                                <option value="{{$proveedor->rif}}|{{$proveedor->porcentaje_retener}}" @if($proveedor->rif==$retencionIva->rif_retenido)selected @endif>{{$proveedor->rif}} {{$proveedor->nombre}} ({{$proveedor->porcentaje_retener ?? 'No tiene'}}%)</option>		
                            @endforeach
                        @endif
                    </select>
                
                    <label for="">Fecha</label>
                    <input type="date" name="fecha" class="form-control" value="{{$retencionIva->fecha}}" required>
                    <label for="">Cheque o Referencia de Pago</label>
                    <input type="text" name="cheque" class="form-control" value="{{$retencionIva->cheque}}">
                </div>
                <div class="col">
                    <div class="row align-items-center">
                        <div class="col text-center">
                            <button class="btn btn-primary text-center" type="submit">Actualizar Retención</button>
                        </div>
                        
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    <detalle-retencion-iva :comprobante="{{$retencionIva->comprobante}}"></detalle-retencion-iva>
    
    
</form>    
@endsection

@section('js')
    <script type='text/javascript'>
        /****************************************************************************************************** */
			// select 2
			$('.js-example-basic-single').select2({			
				placeholder: 'Seleccione el proveedor',    	
				/* maximumSelectionLength:1, */
			});
            window.baseUrl = '{{ url('/') }}';
    </script>
@endsection