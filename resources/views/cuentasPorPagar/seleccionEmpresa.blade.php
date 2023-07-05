@extends('layouts.app')

@section('content')
	<div class="container">
		<div class="card">
			<div class="card-header">
				<h4>Sucursales</h4>
			</div>
			<div class="card-body">
				<form action="{{route('cuentasporpagar.guardarSeleccionEmpresa')}}" method="post">
					@csrf
					<input type='hidden' value="{{$rutaSolicitante ?? ''}}" name='ruta_solicitante'>
					<div class="form-group">
						<label>Seleccione la sucursal a trabajar</label>
						<select name="empresa" class="form-control" required>
							<option value="">-- Seleccionar --</option>
							@foreach($empresas as $empresa)
							<option value="{{$empresa->rif}}|{{$empresa->nombre}}|{{$empresa->basedata}}">{{$empresa->rif}} {{$empresa->nombre}}</option>
							@endforeach
						</select>
						
					</div>
					<div class="form-group">
						<label>Modo de Pago</label>
						<select name="modo_pago" class="form-control" required>
							<option value="">-- Seleccione --</option>
							<option value="1|bolivares">Bolivares <b class="text-primary">Bs.</b></option>
							<option value="2|dolares">Divisas <b class="text-success">$</b></option>
						</select>
					</div>
					<button type="submit" class="btn btn-primary">Seleccionar</button>
				</form>
				
			</div>
		</div>
	</div>
@endsection
@section('js')


<script type="text/javascript">
	// select 2
	$(document).ready(function() {
	    $('.js-example-basic-single').select2({
	    	placeholder: 'Seleccione el proveedor',    	
	    	maximumSelectionLength:1,
	    });
	});

	$(document).ready(function() {	
		
		$('#articulos').DataTable({
		
	    scrollY: 400,
	    select: true,
	    searching: true,
	    paging: false
		});	
    } );
</script>
@endsection