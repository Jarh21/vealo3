@extends('layouts.app')
@section('content')
	<div class="container">
		<h3>Editar Registro de unidad Tributaria Anual</h3>
		<div class="row">
			<div class="col-sm-6">
				<form action="{{route('ut.update',$utEdit->id)}}" method="POST">
					@method('PUT')
					@csrf
				  <div class="form-group">
				    <label for="anio">Año</label>
				    <input type="date" value="{{$utEdit->anio}}" class="form-control" name="anio" required>				    
				  </div>
				  <div class="form-group">
				    <label for="monto">Monto</label>
				    <input type="text" value="{{$utEdit->monto}}" class="form-control" name="monto" required >
				  </div>  
				  <div class="form-group">
				    <label for="observacion">Observación</label>
				    <input type="text" value="{{$utEdit->observacion}}" class="form-control" name="observacion" >
				  </div>
				 
				 
				  <button type="submit" class="btn btn-primary">Actualizar</button>
				  <button type="reset" class="btn btn-danger">Borrar</button>
				</form>
			</div>
		</div>
	</div>
@endsection