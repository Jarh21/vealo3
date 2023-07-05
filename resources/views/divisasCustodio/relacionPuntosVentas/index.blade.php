@extends('layouts.app')
@section('content')
	<div class="container">
		<h3>Relacion Porcentual de Puntos de Ventas</h3><hr>
		<form action="{{route('mostart.porcentaje.puntosventas')}}" method="post">
			@csrf
			<div class="form-row">
	            <div class="form-group col-6">
	            	<label>Empresa</label>
	            	<select name="conexion" class="form-control" required>	            		
	            		<option value="">-- Seleccione Empresa --</option>
	            		@foreach($empresas as $empresa)
	            			<option value="{{$empresa->basedata}}" @if(isset($conexion)) @if($conexion==$empresa->basedata)selected @endif @endif>{{$empresa->nombre}}
	            			</option>
	            		@endforeach
	            	</select>
	                <label for="fecha" class="font-weight-bolder">Fecha a buscar</label>
	                <input type="date" required class="form-control" value="{{$fecha ?? date('Y-m-d')}}" id="fecha" name="fecha">
	            </div>
			</div>
			<button type="submit" class="btn btn-primary">Buscar</button>
		</form>
		@if(isset($registros))
			<table class="table">
				
					<tr>
						<td>Entidad</td>
						<td>Detallado del dia</td>
					</tr>					
				
				<tbody>
					@foreach($registros as $registro)
					<tr>
						<td>{{$registro->entidad}}</td>
						<td>{{$registro->detallado_del_dia}}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		@endif
	</div>
@endsection