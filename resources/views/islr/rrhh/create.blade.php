@extends('layouts.islr')
@section('content')
	<div class="container">
		<h3>Registro de nuevos Empleados</h3><hr>
		<div class="row">
			<div class="col-sm-6">				
				
				<form action="{{route('rrhh.save')}}" method="POST">					
					@csrf
					<div class="form-group">
						<label>Rif del Empleado</label>
						<input type="text" name="rif" value="" class="form-control" required>						
					</div>
					<div class="form-group">
						<label>Nombre Completo</label>
						<input type="text" name="nombres" value="" class="form-control" required>						
					</div>	
					<div class="form-group">
						<label>Fecha Ingreso (opcional)</label>
						<input type="date" name="fecha_ingreso" value="" class="form-control">						
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
			    		<label for="anio">Sueldo Base</label>
			    		<input type="text" value="" class="form-control" name="sueldo_base" id="moneda">				    
			  		</div>
				  	 
				 
				  <button type="submit" class="btn btn-primary">Guardar</button>
				  <button type="reset" class="btn btn-secondary">Resetear valores</button>
				  
				</form>
				
			</div>
		</div>


	</div>
@endsection