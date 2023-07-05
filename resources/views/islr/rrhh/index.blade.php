@extends('layouts.islr')
@section('css')

<!--<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />-->
<link rel="stylesheet" type="text/css" href="{{asset('css/daterangepicker.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('css/dataTables.bootstrap4.min.css')}}">
@endsection
<?php
	if(!isset($empresa_seleccionada)){
		$empresa_seleccionada='';
	}  
?>
@section('content')
	<div class="row">
		<div class="col">
			<h3>Departamento de Talento Humano </h3><p>Registro de los empleados para la retención del impuesto sobre la renta.</p>
		</div>
		<div class="col">
			<form action="{{route('rrhh.postindex')}}" method="post">
			@csrf	
			<div class="row">				
				<div class="col-6">	
					<select class="form-control d-print-none" name="empresa" required>
						<option value="">-Empresa-</option>
						@foreach($empresas as $empresa)
						<option value="{{$empresa->rif}}|{{$empresa->nombre}}" @if($empresa_seleccionada==$empresa->rif)selected @endif>{{$empresa->nombre}}</option>
						@endforeach
					</select>
				</div>
				<div class="col-2">	
					<button class="btn btn-success d-print-none">Seleccionar</button> 
				</div>	
			</div>
			</form>
		</div>
			
	</div>	
	<hr>
	<div>
	<button type="button" class="btn btn-info btn-sm d-inline mb-3 d-print-none" onclick="javascript:window.print()">
			<i class="fa fa-print" aria-hidden="true"></i> Imprimir
	</button>
	<button type="button" class="btn btn-secondary btn-sm d-inline mb-3 d-print-none" data-toggle="modal" data-target="#exampleModalimportar"><i class="fas fa-arrow-down"></i> Importar Empleados</button>
	<a href="{{route('rrhh.create')}}" class="btn btn-primary btn-sm mb-3 d-print-none"><i class="fa fa-plus" aria-hidden="true"></i>Nuevo Empleado</a>
	<a href="{{route('rrhh.export',$empresa_seleccionada)}}" class="btn btn-success btn-sm mb-3 d-print-none"><i class="far fa-file-excel"></i>Exportar Excel</a>
	</div>
	@if(isset($messageBueno))
		<div class="alert alert-success"><p>{{$messageBueno}}</p></div>
	@endif
	@if(isset($messageMalo))
		<div class="alert alert-danger"><p>{{$messageMalo}}</p></div>
	@endif

	<form id="update_masivo" name="update_masivo" action="{{route('updateMasivo')}}" method="post">
	@csrf	
	<table id="regirtros" class="table" data-page-length='50'>
		<thead>
			<tr>
				<th>Nº</th>
				<th>Nombres</th>
				<th>Fecha Ingreso</th>
				<th>Empresa Rif</th>
				<th>Sueldo</th>
				<th>Rif</th>
				<th class="d-print-none">Acción</th>
			</tr>
		</thead>
		<tbody>
				
			<?php $n=1; ?>
			@foreach($empleados as $empleado)
				<?php 
					$rif=str_replace("-", "", $empleado->rif);
					$nCadena= strlen ($rif);
				?>
				<tr >
					<td>{{$n++}}</td>
					<td>{{$empleado->nombres}}</td>
					<td>{{$empleado->fecha_ingreso}}
						
					</td>
					<td @if($nCadena==10) bgcolor="{{$empleado->color}}" @else bgcolor="red" @endif>{{$empleado->empresa_nombre}}@if($nCadena!=10)<b class="text-white bg-dark"> El rif esta malo</b>@endif</td>
					<td width='120px'>
						<input type="text" name="sueldo_nuevo[]" value="{{round($empleado->sueldo_base,2)}}" class="form-control ">						
						<input type="hidden" name="id[]" value="{{$empleado->id}}">
					</td>
					<td style="width: 130px" >{{$empleado->rif}}</td>
					<td class="d-print-none">
						<span class="d-inline"><a href="{{route('rrhh.edit',[$empleado->id,$empleado->empresa_nombre])}}" class="btn btn-secondary btn-sm d-inline d-print-none" title="Editar"><i class="fas fa-edit"></i></a></span>
						<!-- Button trigger modal -->
						<span class="d-inline">
						<button type="button" class="btn btn-danger btn-sm d-inline d-print-none" data-toggle="modal" data-target="#exampleModal{{$empleado->id}}" title="Eliminar"><i class="fas fa-trash"></i>
						 
						</button>	
						</span>
						
					</td>
				</tr>

				<!-- Modal eliminar empleados -->
				<div class="modal fade" id="exampleModal{{$empleado->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
				  <div class="modal-dialog">
				    <div class="modal-content">
				      <div class="modal-header">
				        <h5 class="modal-title" id="exampleModalLabel">Eliminar</h5>
				        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				          <span aria-hidden="true">&times;</span>
				        </button>
				      </div>
				      <div class="modal-body">
				       ¿Confirma que desea eliminar al siguiente empleado de los registros?
				       <p>Nombre: {{$empleado->nombres}}</p>
				       <p>Rif: {{$empleado->rif}}</p>
				        
				      </div>
				      <div class="modal-footer">
				        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
				        <a href="{{route('rrhh.destroy',$empleado->id)}}"><button class="btn btn-danger">Eliminar</button></a>			        
				        
				      </div>
				    </div>
				  </div>
				</div>	<!--fin modal-->

			@endforeach
			
				<div class="fixed-bottom d-flex justify-content-end bg-info">
					Para aplicar todos los cambios haga click en el siguiente boton ----->
					<button type="submit" class="btn btn-secondary my-2 mr-2 d-print-none"><i class="fas fa-save mx-2"></i>Aplicar Todos los cambios</button>
				</div>
				
			
		</tbody>
	</table>
	</form>
	<!-- Modal -->
		<div class="modal fade" id="exampleModalimportar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title" id="exampleModalLabel">Importar</h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">
		      	<form action="{{route('import-list-excel')}}" method="post" enctype="multipart/form-data">
					@csrf
					
					
					<label>Seleccione el archivo a Importar</label>
					
					<input class="form-control" id="formFileSm" type="file" name="excel" required>
				
					<button type="submit" class="btn btn-primary">Importar</button>
				
					
						
				</form> 
		        
		      </div>
		      <div class="modal-footer">
		      	<a href="{{asset('importar_empleados_.xlsx')}}" class="float-right">Descarga aquí el formato vacío para llenarlo con los datos de los empleados</a>
		        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>		        		        
		        
		      </div>
		    </div>
		  </div>
		</div>	<!--fin modal-->

@endsection

@section('js')
<!--<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>-->
<script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('js/dataTable.bootstrap4.min.js')}}"></script>

<script type="text/javascript">

	$(document).ready(function() {	
		
		$('#regirtros').DataTable({
	   /* scrollY: 400,*/
	    select: true,
	    paging: false,
	    searching: true,
		ordering:  true
		});   	

	} );
</script>
@endsection