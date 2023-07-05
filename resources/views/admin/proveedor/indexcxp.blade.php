@extends('layouts.app')


@section('content')
	<div class="container">
		<h3>Listado de proveedores <a href="{{route('proveedor.create','proveedor.createcxp')}}"><button class="btn btn-success float-right">Agregar Proveedor</button></a></h3>
		<a href="http://contribuyente.seniat.gob.ve/BuscaRif/BuscaRif.jsp">Haz Click aqui para consultar el rif en el SENIAT</a>

		<table id="proveedores" class="table table-striped">
		<thead>
		    <tr>
		      <th scope="col">#Id</th>
		      <th scope="col-4">Rif</th>
		      <th scope="col">Proveedor</th>
			  <th scope="col">%Islr</th>
		      <th scope="col">Codigo Fiscal</th>
		      <th scope="col" width=100px>Acción</th>
		      
		    </tr>
		  </thead>
		  <tbody>
		  	@foreach($proveedores as $proveedor)
		    <tr>
		      <th scope="row">{{$proveedor->id}}</th>
		      <td width=100px>{{$proveedor->rif}}</td>
			  <td>{{$proveedor->nombre}}</td>
		      <td>{{$proveedor->ultimo_porcentaje_retencion_islr}}</td>
		      <td>{{$proveedor->codigoFiscal}}</td>
		      <td>
		      	<a href="{{route('proveedor.ver',$proveedor->id)}}" class="btn-secondary btn-sm">Ver</a>
		      	<a href="{{route('proveedor.edit',$proveedor->rif)}}" class="btn-primary btn-sm">Editar</a>
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
		    scrollY: 600,
		    select: true,
		    paging: true,
		    searching: true,
    		ordering:  true,
    		"language": {
	            "lengthMenu": "Mostrar _MENU_ registros por pagina",
	            "zeroRecords": "Nada encontrado - disculpa",
	            "info": "Mostrando la pagina _PAGE_ de _PAGES_",
	            "infoEmpty": "No records available",
	            "infoFiltered": "(filtrado de _MAX_ registros totales)",
	            "search": "Buscar",
	            "paginate":{
	            	"next": "Siguiente",
	            	"previous": "Previo"
	            }
	        }
			});
			
    	

	} );
</script>
@endsection