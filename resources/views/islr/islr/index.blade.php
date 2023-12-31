@extends('layouts.app')
@section('js')

<script type="text/javascript">

	$(document).ready(function() {	
		
			$('#retenciones').DataTable({
		    scrollY: 500,
		    select: true,
		    paging: true,
		    searching: true,
    		ordering:  false
			});			
    	

	} );
</script>
@endsection
@section('content')
	<div class="container-fluid">
		<h3>Listado de ISLR
			
			<a href="{{route('islr.create')}}">			
                
			<button class="btn btn-success float-right"><i class="fa fa-plus mx-1" aria-hidden="true"></i>Agregar Documento</button></a>
		</h3>
		<!-- SEARCH FORM -->
			<b>Buscar Retención</b><br>
			<form class="form-inline  my-2" action="{{route('islr.filtrar')}}" method="post">
				@csrf	
							
                <div class="input-group input-group-sm">

                	<span class="text-danger">*</span>
                	<select class="form-control form-control-navbar" name="empresa" required>
                		<option value="">--Seleccione Empresa--</option>
                		@foreach($empresas as $empresa)
                		<option value="{{$empresa->rif}}">{{$empresa->nombre}}</option>
                		@endforeach
                	</select>
                    <input class="form-control form-control-navbar mx-1" name="proveedor" type="search" placeholder="proveedor" aria-label="Search">
                    
                    <label class="mx-1"><span class="text-danger">*</span>desde</label>
                    <input class="form-control form-control-navbar" type="date" name="fecha1" required>
                    
                    <label class="mx-1"><span class="text-danger">*</span>hasta</label>
                    <input class="form-control form-control-navbar" type="date" name="fecha2" required>
                    <div class="input-group-append">
                        <button class="btn btn-navbar" type="submit">
                        	<i class="fa fa-search fa-2x" aria-hidden="true"></i>Buscar
                            
                        </button>
                    </div>
                </div>
            </form>
		<table id="retenciones" class="table table-striped">
		  <thead>
		    <tr>
		      <th>Nº</th>
		      <th>Fecha</th>
		      <th>Empresa</th>
		      <th>Proveedor</th>
		      <th>Nº Factura</th>		      
		      <th>Total Retener</th>
		      <th>Accion</th>
		    </tr>
		  </thead>
		  <tbody>		  	
		  	@foreach($islrs as $islr)
		    <tr>
		      <td scope="row">{{$islr['nControl']}}</td>
		      <td width=10%>{{$islr['fecha']}}</td>
		      <td>{{$islr['nom_corto']}}</td>
		      <td width=50%>{{$islr['proveedor']}}
		      	@if($islr['tipo_contribuyente'] == 'Natural') 
		      		<span class="right badge badge-primary">{{$islr['tipo_contribuyente']}}</span>
		      	@else
		      		<span class="right badge badge-success">{{$islr['tipo_contribuyente']}}</span>
		      	@endif	
		      </td>
		      <td class="text-left">
		      	@foreach($islr['detalleRetencion'] as $detalleRetencion)
		      		<p class="m-0">{{$detalleRetencion->nFactura}}</p>
		      	@endforeach
		      </td>		      
		      <td>
		      	@if ($islr['total_retener'] == '0,00')
		      		<span class="right badge badge-danger">Retención Anulado {{$islr['total_retener']}}</span>
		      	@else
		      	{{$islr['total_retener']}}
		      	@endif
		      </td>		      
		      <td width=10%>
		      	<a href="{{route('islr.view',$islr['id'])}}" class="btn btn-secondary btn-sm"title="Ver"><i class="fa fa-search" aria-hidden="true"></i></a>
		      	<a href="{{route('islr.edit',[$islr['id'],'edit'])}}" class="btn btn-primary btn-sm" title="Editar"><i class="fa fa-pencil-alt" aria-hidden="true"></i></a>
		      	
		      </td>		      
		    </tr>
		    @endforeach
		    
		    
		  </tbody>
		</table>
		{{--$paginacion->links()--}}
		

	</div>
@endsection
