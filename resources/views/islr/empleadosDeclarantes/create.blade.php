@extends('layouts.app')
@section('content')

	<div class="container">
		<h3>Registro Contribuyentes del ISLR <a href="{{URL::previous()}}" class="btn btn-warning float-right">Regresar</a></h3><hr>
		<div class="row">
			<div class="col-6">
				<form action="{{route('declarantes.save')}}" method="POST">
				@csrf
				
						<div class="form-group">
							<label>Rif</label><span class="text-danger">*</span>
							<input type="text" class="form-control" name="rif" required placeholder="el rif debe tener el siguiente formato V-00000000-0">
						</div>
						<div class="form-group">
							<label>Nombres</label><span class="text-danger">*</span>
							<input type="text" class="form-control" name="nombre" required>
						</div>
						<div class="form-group">
							<label>Cargo</label><span class="text-danger">*</span>
							<select class="form-control" name="tipoDirectivo" required>
								<option value="">-Seleccione-</option>
								@foreach($tiposDirectivos as $tipo)
								<option value="{{$tipo->id}}">{{$tipo->nombre}}</option>
								
								@endforeach

							</select>
						</div>
						<div class="form-group">
							<label>Empresa</label><span class="text-danger">*</span>
							<select class="form-control" name="empresa" required>
								<option value="">-Seleccione-</option>
								@foreach($empresas as $empresa)
								<option value="{{$empresa->rif}}|{{$empresa->nombre}}">{{$empresa->nombre}}</option>
								@endforeach
							</select>
						</div>
						<div class="form-group">
							<label>Sueldo a declarar</label>
							<span class="text-danger">*</span><input type="text" id="creditos" name="sueldo" required class="form-control">
						</div>
						<div class="form-group">
							<label>Fecha de Ingreso</label>
							<input type="date" name="fecha" class="form-control">
						</div>
						
					@if(empty($tiposDirectivos))
						
						<div class="alert alert-danger">
							<p>El campo Cargo se encuentra vacio ya que se debe registrar previamente en el sistema, dirijase al siguiente enlace  <a href="{{route('contribuyente.index')}}">Contribuyentes</a> para registrarlos y luego continuar con esta transacción, dicho enlace tambien se puede encontrar en el menu configuración. Esto se debe, porque se deben registrar los tipos de directivos como Presidente,Gerentes, etc.. para luego identificar quienes son cada uno de ellos ya que se requiere al momento de generar el archivo XML el cual se importara en el portal del SENIAT
		 					</p>
						</div>
					@endif
					<button type="submit" class="btn btn-primary float-right">Guardar</button>
				</form>
			</div>
		</div>		
		
	</div>
	<script type="text/javascript">
		/*funcion que formatea el valor numerico al de moneda*/
		$(document).ready(function() {
			$("#creditos").on({
				"focus": function (event) {
					$(event.target).select();
				},
				"keyup": function (event) {
					$(event.target).val(function (index, value ) {
						return value.replace(/\D/g, "")
									.replace(/([0-9])([0-9]{2})$/, '$1,$2')
									.replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
					});
				}
			});
		} );    
	</script>
@endsection