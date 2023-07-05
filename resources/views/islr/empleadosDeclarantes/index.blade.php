@extends('layouts.app')
@section('content')
	<div class="container">
		<h3>Registro Contribuyentes del ISLR<a href="{{route('declarantes.create')}}"><button class="btn btn-success float-right">Agregar</button></a></h3>
		<table class="table table-striped">
		  <thead>
		    <tr>
		      <th scope="col">Rif</th>
		      <th scope="col">Nombres</th>
		      <th scope="col">Cargo</th>
		      <th scope="col">Empresa</th>
		      <th scope="col">Sueldo</th>		      
		      <th>Operaciones</th>
		    </tr>
		  </thead>
		  <tbody>
		  	   @foreach($declarantes as $declarante)
			    <tr>
			    		    	
						<td>{{$declarante->rif}}</td>
						<td>{{$declarante->nombre}}</td>
						<td><a href="{{url('/contribuyentes')}}">{{$declarante->contribuyente_id}}</a></td>
						<td>{{$declarante->empresa}}</td>
						<td>{{number_format($declarante->sueldo_base,2,',','.')}}</td>				
				    	<td>		    		
			    			<!-- Button trigger modal update -->
							<a href="{{route('declarantes.edit',$declarante->id)}}" class="btn btn-primary btn-sm" >
							  Editar
							</a>
							<!-- Button trigger modal -->
							<button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#exampleModal{{$declarante->id}}">
							  Eliminar
							</button>
			    		</td>
				    				    	
			    </tr>	    



			    <!-- Modal -->
				<div class="modal fade" id="exampleModal{{$declarante->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
				  <div class="modal-dialog">
				    <div class="modal-content">
				      <div class="modal-header btn-danger">
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
				        <a href="{{route('declarantes.delete',$declarante->id)}}"><button class="btn btn-danger">Eliminar</button></a>			        
				        
				      </div>
				    </div>
				  </div>
				</div>	<!--fin modal-->	    
		   		@endforeach<!-- fin foreach -->
		  </tbody>
		</table>
		
	</div>

@endsection