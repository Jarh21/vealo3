@extends('layouts.app')
@section('content')
	<div class="container">
		<h3>Contribuyentes<a href="{{route('contribuyente.create')}}"><button class="btn btn-success float-right">Agregar Contribuyente</button></a></h3>
		<p>En esta area se registran los direfentes tipos de directivos o cargos gerenciales que posee la empresa, por ejemplo: Director, Gerente,Coordinador, Presidente, etc. Solo son los tipo y a su vez el % que declaran, estos datos deben ser registrados previo al registro de los contribuyentes ya que en ellos se especifica los datos personales y el cargo que ocupa.</p>
		<table class="table table-striped">
		  <thead>
		    <tr>
		      <th scope="col">Cargo</th>
		      <th scope="col">Codigo</th>
		      <th scope="col">Porcentaje que declarar</th>		      
		      <th>Operaciones</th>
		    </tr>
		  </thead>
		  <tbody>
		  	@foreach($contribuyentes as $contribuyente)		  
			    <tr>		    	
					<td>{{$contribuyente->nombre}}</td>
					<td>{{$contribuyente->codigo}}</td>
					<td>{{$contribuyente->porcentaje_retencion}}%</td>				
			    	<td>
			    		<a href="{{route('contribuyente.edit',$contribuyente->id)}}" class="btn btn-secondary">Editar</a>		    		
			    		<!-- Button trigger modal -->
						<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#exampleModal{{$contribuyente->id}}">
						  Eliminar
						</button>
			    	</td>
			    </tr>
			    <!-- Modal -->
				<div class="modal fade" id="exampleModal{{$contribuyente->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
				  <div class="modal-dialog">
				    <div class="modal-content">
				      <div class="modal-header">
				        <h5 class="modal-title" id="exampleModalLabel">Eliminar</h5>
				        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				          <span aria-hidden="true">&times;</span>
				        </button>
				      </div>
				      <div class="modal-body">
				       ¿Confirma que desea eliminar los registros seleccionados?
				      
				      </div>
				      <div class="modal-footer">
				        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
				        <a href="{{route('contribuyente.destroy',$contribuyente->id)}}"><button class="btn btn-danger">Eliminar</button></a>			        
				        
				      </div>
				    </div>
				  </div>
				</div>	<!--fin modal-->	    
		    @endforeach
		  </tbody>
		</table>
		
	</div>

@endsection