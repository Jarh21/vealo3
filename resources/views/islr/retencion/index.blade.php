@extends('layouts.islr')
@section('content')
	<div class="container">
		<h3>Determinacion de la Retención<a href="{{route('retencion.create')}}"><button class="btn btn-success float-right">Agregar Determinacion de Retenciones</button></a></h3>
		<table class="table table-striped">
		  <thead>
		    <tr>
		      <th scope="col">% Retencion</th>
		      <th scope="col">Valor U.T</th>
		      <th scope="col">Factor</th>
		      <th scope="col">Sustraendo</th>
		      <th scope="col">Monto minimo sujeto a retencion</th>
		      <th scope="col">Accion</th>
		      
		    </tr>
		  </thead>
		  <tbody>
		  	@foreach($retenciones as $retencion)
		    <tr>
		    	<td>{{$retencion->procent_retencion}} %</td>
		    	<td>
		    		@foreach($ut as $monto)
		    		{{$monto->monto}}
		    		@endforeach
		    	</td>
		    	<td>{{$retencion->factor}}</td>
		    	<td>{{$retencion->sustraendo}}</td>
		    	<td>{{$retencion->monto_min_retencion}}</td>
		    	<td>
		    		
		    		<!-- Button trigger modal -->
					<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#exampleModal{{$retencion->id}}">
					  Eliminar
					</button>
		    	</td>
		    </tr>
		    <!-- Modal -->
			<div class="modal fade" id="exampleModal{{$retencion->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
			       <p>% Retencion: {{$retencion->procent_retencion}}</p>
			       <p>Valor U.T: {{$retencion->valorUT}}</p>
			       <p>Factor: {{$retencion->factor}}</p>
			       <p>Sustraendo: {{$retencion->sustraendo}}</p>
			       <p>Monto minimo sujeto a retencion: {{$retencion->monto_min_retencion}}</p> 
			      </div>
			      <div class="modal-footer">
			        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
			        <a href="{{route('retencion.destroy',$retencion->id)}}"><button class="btn btn-danger">Eliminar</button></a>			        
			        
			      </div>
			    </div>
			  </div>
			</div>	<!--fin modal-->	    
		    @endforeach
		  </tbody>
		</table>
		
	</div>

@endsection