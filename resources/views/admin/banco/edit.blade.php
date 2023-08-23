@extends('layouts.app')
@section('content')
	
<div class="container">
		<h3>Bancos<a href="{{url('/bancos')}}" class="btn btn-warning float-right">Regresar</a></h3>
		<hr>
		<form action="{{route('banco.update',$banco->id)}}" method="post">
			@csrf
			@method('put')
			
				<div class="form-group">
					<input type="text" class="form-control" name="nombre" placeholder="Nombre del Banco" value="{{$banco->nombre}}" required>
				</div>
				<div class="form-group">
					<input type="text" class="form-control" name="nombre_corto" placeholder="Nombre corto del banco" value="{{$banco->nombre_corto}}" required>
				</div>
				<div class="form-group">
					<label for="lista_bancos">Agregar a la lista de todos los bancos</label><input type="checkbox" id="lista_bancos" class="mx-2" name="is_bank_list" @if($banco->is_bank_list==1)checked @endif>
					<button class="btn btn-success float-right" type="submit">Actualizar</button>
				</div>				
				
			
		</form>
		
	</div>
@endsection