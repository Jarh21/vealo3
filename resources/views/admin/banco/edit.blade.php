@extends('layouts.app')
@section('content')
	
	<div class="container">
		<h3>Bancos<a href="{{url('/bancos')}}" class="btn btn-warning float-right">Regresar</a></h3>
		<hr>
		<form action="{{route('banco.update',$banco->id)}}" method="post">
			@csrf
			@method('put')
			<div class="row">
				<div class="col">
					<input type="text" class="form-control" name="nombre" placeholder="Nombre del Banco" value="{{$banco->nombre}}" required>
				</div>
				<div class="col">
					<input type="text" class="form-control" name="nombre_corto" placeholder="Nombre corto del banco" value="{{$banco->nombre_corto}}" required>
				</div>
				<div class="col">
					<input type="text" class="form-control" name="primeros_cuatro_digitos" placeholder="Nombre corto del banco" value="{{$banco->primeros_cuatro_digitos}}" required>
				</div>
				<div class="col">
					<button class="btn btn-success " type="submit">Actualizar</button>
				</div>
				
			</div>
		</form>
		
	</div>	
@endsection