@extends('layouts.app')
@section('content')
	<div class="container">
		<h3>Contribuyentes<a href="{{URL::previous()}}" class="btn btn-warning float-right">Regresar</a></h3>
		<form action="{{route('contribuyente.save')}}" method="POST">
				@csrf
				<div class="form-group">
					<label>Cargo</label>
					<input type="text" class="form-control" name="nombre" placeholder="Ejemplo: PRESIDENTE, GERENTE, DIRECTOR ...">
				</div>
				<div class="form-group">
					<label>Codigo</label>
					<input type="text" class="form-control" name="codigo" value="001">
				</div>
				<div class="form-group">
					<label>Porcentaje Que declara</label>
					<input class="form-control" type="text" name="porcentaje_retencion" placeholder=" Ej. 10, 33,33, 0">
				</div>
				

				<button type="submit" class="btn btn-primary float-right">Guardar</button>
			</form>

		
	</div>
@endsection