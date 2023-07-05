@extends('layouts.app')
@section('content')
	<div class="container">
		<h3>Contribuyentes<a href="{{URL::previous()}}" class="btn btn-warning float-right">Regresar</a></h3>
		<form action="{{route('contribuyente.update',$contribuyente->id)}}" method="POST">
				@csrf
				@method('put')
				<div class="form-group">
					<label>Nombre</label>
					<input type="text" class="form-control" name="nombre" value="{{$contribuyente->nombre}}">
				</div>
				<div class="form-group">
					<label>Codigo</label>
					<input type="text" class="form-control" name="codigo" value="{{$contribuyente->codigo}}">
				</div>
				<div class="form-group">
					<label>% Que declara, solo numeros ej: 10,20 </label>
					<input class="form-control" type="text" name="porcentaje_retencion" value="{{$contribuyente->porcentaje_retencion}}">
				</div>
				

				<button type="submit" class="btn btn-primary float-right">Editar</button>
			</form>

		
	</div>
@endsection