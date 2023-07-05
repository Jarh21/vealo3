@extends('layouts.app')

@section('content')
	<div class="container">
		<h3>Registro de ISLR</h3><hr>
		@if ($errors->any())
	    <div class="alert alert-danger">
	        <ul>
	            @foreach ($errors->all() as $error)
	                <li>{{ $error }}</li>
	            @endforeach
	        </ul>
	    </div>
		@endif
		
		<div class="row">
			<div class="col-sm-6">
				<form action="{{route('islr.save')}}" method="POST" enctype="multipart/form-data">
					@csrf
					<div class="form-group">
				    	<label for="name">Nombre</label>
				    	<select name="empresa_id" class="form-control">
				    		<option value="">--Seleccione la Empresa--</option>
				    		@foreach($empresas as $empresa)
				    		<option value="{{$empresa->id}}|{{$empresa->rif}}">{{$empresa->nombre}}</option>
				    		@endforeach
				    	</select>				    			    
				  	</div>
				  	<div class="form-group">
						<label>Proveedores</label>
					  	<input type="text" readonly name="proveedor_id" id="proveedor_id" class="form-control">
					  	<button type="button" class="btn btn-success" data-toggle="modal" data-target="#exampleModal">
						 lista de proveedores
						</button>	
					</div>	
					

				  <button type="submit" class="btn btn-primary">Siguiente</button>
				  <button type="reset" class="btn btn-danger">Borrar</button>
				</form>
				@yield('detalleRetenido')
			</div>
		</div>

			<!-- Modal Proveedores-->
		<link rel="stylesheet" type="text/css" href="{{asset('css/dataTables.bootstrap4.min.css')}}">
	
		<div class="modal fade" id="exampleModal" role="dialog" >
		  <div class="modal-dialog modal-lg">
		    <div class="modal-content">
				<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">listado de Proveedores</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				  <span aria-hidden="true">&times;</span>
				</button>
				</div>	      
		      	<div class="modal-body">	      		
		       	<table id="proveedores" class="table table-bordered" data-page-length='10'>
		       		<thead>
		       			<tr>	       				
		       				<th>Rif</th>
		       				<th>Nombres</th>
		       				<th>Acción</th>
		       			</tr>
		       		</thead>
		       		<tbody>
		       			@foreach($proveedores as $proveedor)
		       			<tr>

		       				<td><input type="hidden" name="" id="proveedorId{{$proveedor->id}}" value="{{$proveedor->id}}">{{$proveedor->rif}}</td>
		       				<td><input type="hidden" id="proveedorRif{{$proveedor->id}}" value="{{$proveedor->rif}}" >
		       					<input type="hidden" id="proveedorNom{{$proveedor->id}}" value="{{$proveedor->nombre}}" >{{$proveedor->nombre}}</td>
		       				<td><button type="button" class="btn btn-success btn-sm" onclick="copiarProveedor({{$proveedor->id}})" data-dismiss="modal">Seleccionar</button></td>
		       			</tr>
		       			@endforeach
		       		</tbody>	       		
		       	</table>	    		
		       		
		      	</div>
		      	<div class="modal-footer">
		        	<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
		       	</div>
		    </div>
		  </div>
		</div>	
		<!--fin modal-->
	</div>
@endsection	
@section('js')


<script type="text/javascript">

	$(document).ready(function() {	
		
			$('#proveedores').DataTable({
		//    scrollY: 200,
		    select: true,
		    paging: true,
		    searching: true,
    		ordering:  true
			});			
  

	} );
</script>
<script type="text/javascript">
	function copiarProveedor(id){
		 
		var pId = 'proveedorId'+id;
		var pRif = 'proveedorRif'+id;
		var pNom = 'proveedorNom'+id;		
		var proveedorId = document.getElementById(pId).value;
		var proveedorRif = document.getElementById(pRif).value;
		var proveedorNom = document.getElementById(pNom).value;
		document.getElementById('proveedor_id').value = proveedorId+'|'+proveedorRif+'|'+proveedorNom;
		
	}
</script>
				
@endsection
