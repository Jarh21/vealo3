@extends('layouts.app')
@section('content')
	<div class="container">
		<h3>Registrar de unidad Tributaria Anual</h3>
		<div class="row">
			<div class="col-sm-6">		
				@if (session('status'))
			    <div class="alert alert-success">
			        {{ session('status') }}
			    </div>
				@endif
					
					<form action="{{route('ut.save')}}" method="POST">
						@csrf
						
					  <div class="form-group">
					    <label for="anio">Año</label>
					    <input type="date" class="form-control" name="anio" required value="">				    
					  </div>
					  <div class="form-group">
					    <label for="monto">Monto</label>
					    <input type="text" class="form-control" name="monto" required value="">
					  </div>  
					  <div class="form-group">
					    <label for="observacion">Observación</label>
					    <input type="text" class="form-control" name="observacion" value="">
					  </div>					 	
					
					  <button type="submit" class="btn btn-primary">Guardar</button>
					  <button type="reset" class="btn btn-danger">Borrar</button>
					</form>
								
			</div>
			
		</div>
	</div>
@endsection