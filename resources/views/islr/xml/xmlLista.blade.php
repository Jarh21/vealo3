@extends('layouts.app')
@section('content')
<div class="container-fluid">
		<!-- Modal sucursal -->
		<div class="modal fade" id="modalCambioSucursal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Sucursales</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				</div>
				<div class="modal-body">
				@if(isset($empresas))
					Seleccione la sucursal
					@foreach($empresas as $empresa)
						<!-- <option value="{{--$empresa->rif--}}|{{--$empresa->nombre--}}|{{--$empresa->basedata--}}">{{--$empresa->rif--}} {{--$empresa->nombre--}}</option> -->
						<a href="{{route('seleccionSucursal',[$empresa->rif,'islr.xml.listar'])}}" class="dropdown-item dropdown-footer">{{$empresa->rif}} {{$empresa->nombre}}</a>
						<div class="dropdown-divider"></div>
					@endforeach
				@endif
				</div>
				<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>				        		        
				
				</div>
			</div>
			</div>
		</div>	<!--fin modal-->
			
			<h3>Listado de XML <a href="#" data-toggle="modal" data-target="#modalCambioSucursal" class="btn btn-outline-primary my-2">Seleccione sucursal ->{{session('empresaRif')}} {{session('empresaNombre') ?? 'No hay sucursal seleccionada'}}</a><a class="btn btn-success float-right" href="{{route('islr.xmlCrear')}}">Crear XML</a></h3><hr>
	<div class="row">
		<div class="col-12">
			<table class="table" id="xmls">
				<thead>
					<th>Id</th>
					<th>Rif</th>
					<th>Empresa</th>
					<th>Periodo Fiscal</th>
					<th>Creado</th>
					<th>Observacion</th>
					<th>Acción</th>
				</thead>
				<tbody>
					@foreach($listadoxml as $valorxml)
					<tr @if($valorxml->activo==0) class='bg-warning' @endif>
						<td>{{$valorxml->id}}</td>
						<td width="130px"><p style="font-size: 14px;">{{$valorxml->rif_empresa}}</p></td>
						<td><p style="font-size: 14px;">{{$valorxml->nombre_empresa}}</p></td>
						<td width="140px"><p style="font-size: 14px;">{{date('d-m-Y',strtotime($valorxml->periodo_fiscal))}}</p></td>
						<td><p style="font-size: 13px;">{{$valorxml->created_at}}</p></td>
						<td><p style="font-size: 13px;">{{$valorxml->observacion}}</p></td>
						<td width="130px">
							@if($valorxml->activo==1)
								<a href="{{route('islr.xml.ver',[$valorxml->periodo_fiscal,$valorxml->rif_empresa,$valorxml->id,$valorxml->fechas_periodo_fiscal])}}" class="btn btn-primary btn-sm">Ver
								</a>
								@can('xml.delete')
								<!-- Button trigger modal -->
								<button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#exampleModal{{$valorxml->id}}">
						  		Eliminar
								</button>
								@endcan
							@else
								@can('xml.update')
								<a href="{{route('islr.xml.ver',[$valorxml->periodo_fiscal,$valorxml->rif_empresa,$valorxml->id,$valorxml->fechas_periodo_fiscal])}}" class="btn btn-primary btn-sm">Ver
								</a>
								@endcan
								<!-- Button trigger modal -->
								<button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#exampleModal{{$valorxml->id}}">
							  		Eliminar
								</button>
							@endif
						</td>
					</tr>
					 <!-- Modal -->
				<div class="modal fade" id="exampleModal{{$valorxml->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
				  <div class="modal-dialog">
				    <div class="modal-content">
				      <div class="modal-header">
				        <h5 class="modal-title" id="exampleModalLabel">Eliminar</h5>
				        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				          <span aria-hidden="true">&times;</span>
				        </button>
				      </div>
				      <div class="modal-body">
				       ¿Confirma que desea eliminar los registro del xml de la empresa{{$valorxml->rif_empresa}}, {{$valorxml->nombre_empresa}} elaborado el {{$valorxml->created_at}}?
				      
				      </div>
				      <div class="modal-footer">
				        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
				        <a href="{{route('xml.delete',$valorxml->id)}}"><button class="btn btn-danger">Eliminar</button></a>			        
				        
				      </div>
				    </div>
				  </div>
				</div>	<!--fin modal-->
					@endforeach
				</tbody>

			</table>
		</div>
		{{-- $listadoxml->links() --}}	
	</div>
	
</div>
@endsection
@section('js')

<script type="text/javascript">

	$(document).ready(function() {	
		
			$('#xmls').DataTable({
		    scrollY: 500,
		    select: true,
		    paging: true,
		    searching: true,
    		ordering:  false
			});			
    	

	} );
</script>
<script type="text/javascript">
	function mensaje(){
		alert('confirma que desea eliminar el registro seleccionado?');
	}
</script>
@endsection
