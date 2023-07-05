@extends('layouts.app')
@section('content')
	
	<div class="container">
		<h3>Bancos<a href=""></a></h3>
		<hr>
		<h5>Registro de Nuevo Banco</h5>
		<form action="{{route('banco.create')}}" method="post">
			@csrf
			<div class="row">
				<div class="col">
				<input type="text" class="form-control" name="nombre" placeholder="Nombre del Banco" required></div>
				
				<div class="col">
					<input type="text" class="form-control" name="nombreCorto" placeholder="Abreviaturas Ej. BNC" required>
				</div>
				<div class="col">
					<input type="text" class="form-control" name="primeros_cuatro_digitos" placeholder="Los primero numero de la cuenta Ej. 0102">
				</div>
				<div class="col">
					<button class="btn btn-success " type="submit">Guardar</button>
				</div>
				
			</div>
		</form><hr>
		<h5 class="my-2">Listado de Bancos Registrados</h5>
		<table class="table">
			<thead>
				<tr>
					<th>Id</th>
					<th>NOMBRE BANCO</th>
					<th>ABREVIATURA</th>
					<th>CUATRO PRIMERO NUMEROS</th>
					<th>Acción</th>
				</tr>
			</thead>
			<tbody>
				@if(isset($bancos))
					@foreach($bancos as $banco)
						<tr>
							<td>{{$banco->id}}</td>
							<td>{{$banco->nombre}}</td>
							<td>{{$banco->nombre_corto}}</td>							
							<td>{{$banco->primeros_cuatro_digitos}}</td>
							<td>
								<a href="{{route('banco.edit',$banco->id)}}" class="btn btn-secondary">Editar</a>
								<button type="button" class="btn btn-danger " data-bs-toggle="modal" data-bs-target="#exampleModal{{$banco->id}}">
						 		Eliminar
								</button>

								<!-- Modal -->
								<div class="modal fade" id="exampleModal{{$banco->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
								  <div class="modal-dialog">
								    <div class="modal-content">
								      <div class="modal-header">
								        <h5 class="modal-title" id="exampleModalLabel">Eliminar Registro</h5>
								        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
								          <span aria-hidden="true">&times;</span>
								        </button>
								      </div>
								      
								      	@csrf
								      	<div class="modal-body">	      		
								       		
											Confirma eliminar el banco {{$banco->nombre}}? si lo elimina debe contactar al departamendo de sistemas para actualizar el id de la base de datos de todos los registros relacionados con bancos
								      	</div>
								      	<div class="modal-footer">
								        	<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
								        <a href="{{route('banco.delete',$banco->id)}}" class="btn btn-danger">Eliminar</a>		        
								      
								      </div>
								    </div>
								  </div>
								</div>	
								<!--fin modal-->
							</td>
						</tr>
					@endforeach
				@endif
			</tbody>
		</table>
	</div>	
@endsection