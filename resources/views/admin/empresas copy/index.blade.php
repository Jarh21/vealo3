@extends('layouts.app')
@section('content')
	<div class="container">
		<h3>Listado de Empresas <a href="{{route('empresas.create')}}"><button class="btn btn-success float-right">Agregar</button></a></h3>
		<table class="table table-striped">
		  <thead>
		    <tr>
		      <th scope="col">RIF</th>
		      <th scope="col">Nombre</th>
		      <th>Accion</th>
		      
		    </tr>
		  </thead>
		  <tbody>
		  	@foreach($empresas as $empresa)
		    <tr>
		    	<td>{{$empresa->rif}}</td>
		    	<td>{{$empresa->nombre}} @if($empresa->is_sincronizacion_remota ==1) <p class="text-success">Sincronizando con -> {{$empresa->servidor2 ?? 'no hay servidor'}}</p> @endif</td>
		    	<td>
		    		<a href="{{route('empresas.edit',$empresa->id)}}" class="btn btn-info">Modificar</a>
		    		<!-- Button trigger modal -->
					<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#exampleModal{{$empresa->id}}">
					  Eliminar
					</button>
		    	</td>
		    </tr>
		    <!-- Modal -->
			<div class="modal fade" id="exampleModal{{$empresa->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
			        <a href="{{route('empresas.delete',$empresa->id)}}"><button class="btn btn-danger">Eliminar</button></a>			        
			        
			      </div>
			    </div>
			  </div>
			</div>	<!--fin modal-->	    
		    @endforeach
		  </tbody>
		</table>
		
	</div>

@endsection