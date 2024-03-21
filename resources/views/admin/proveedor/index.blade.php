@extends('layouts.app')

@section('content')
	<div class="container-fluid">
		<h3>Listado de proveedores <a href="{{route('proveedor.create','admin.proveedor.create')}}"><button class="btn btn-success float-right">Agregar Proveedor</button></a></h3>
		<a href="http://contribuyente.seniat.gob.ve/BuscaRif/BuscaRif.jsp">Haz Click aqui para consultar el rif en el SENIAT</a>
		<table id="proveedores" class="table table-striped">
		  <thead>
		    <tr>
		      <th scope="col">#Id</th>
		      <th scope="col-4">Rif</th>
		      <th scope="col">Proveedor</th>
			  <th scope="col">%Islr</th>
		      <th scope="col">Codigo Fiscal</th>
		      <th scope="col">Acción</th>
		      
		    </tr>
		  </thead>
		  <tbody>
		  	@foreach($proveedores as $proveedor)
		    <tr>
		      <th scope="row"  width=50px>{{$proveedor->id}}</th>
		      <td width=120px>{{$proveedor->rif}}</td>
			  <td width=300px>{{$proveedor->nombre}}</td>
		      <td  width=50px>{{$proveedor->ultimo_porcentaje_retener_islr}}</td>
		      <td width=100px>{{$proveedor->codigoFiscal}}</td>
		      <td  width=150px>
		      	<a href="{{route('proveedor.ver',$proveedor->id)}}" class="btn btn-secondary btn-sm">Ver</a>
		      	<a href="{{route('proveedor.edit',$proveedor->rif)}}" class="btn btn-primary btn-sm">Editar</a>
				<a href="#" class="btn btn-danger btn-sm eliminar" onclick="eliminar('{{$proveedor->id}}')">Eliminar</a>
		      </td>		      
		    </tr>		    
		    @endforeach
		  </tbody>
		</table>
		
	</div>
@endsection
@section('js')

<script type="text/javascript">

	$(document).ready(function() {	
		
			$('#proveedores').DataTable({
		    scrollY: 500,
		    select: true,
		    paging: true,
		    searching: true,
    		ordering:  true
			});
			
			

	} );
	function eliminar(id){
				let confirmar = confirm('Desea eliminar el proveedor seleccionado?');
				if(confirmar){
					location.href="proveedor/eliminar/"+id;
				}
			}
</script>
@endsection