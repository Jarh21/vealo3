@extends('layouts.app')
@section('content')
	<div class="container">
		<h3>Valor de unidad Tributaria Anual <a href="{{route('ut.create')}}" class="btn btn-success float-right">Crear</a></h3>
		<div class="row">
			
			@if(!isset($uts[0]->id))
				<div class="alert alert-warning">
					Debe registrar el valor de la Unidad Tributaria antes de continuar...
				</div>
				
			@endif
			<table class="table">
				<thead>
					<tr>
						<th>Año</th>
						<th>Monto</th>
						<th>Observacion</th>
						<th>Operaciones</th>
					</tr>	
				</thead>
				<tbody>
					@foreach($uts as $ut)
					<tr>
						<td>{{$ut->anio}}</td>
						<td>{{$ut->monto}}</td>
						<td>{{$ut->observacion}}</td>
						<td>
							<a href="{{route('ut.edit',$ut->id)}}" class="btn btn-primary">Modificar</a>
						</td>
					</tr>
					@endforeach
				</tbody>
			</table>
			
		</div>
	</div>
@endsection