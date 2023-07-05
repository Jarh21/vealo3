@extends('layouts.islr')
@section('content')
	<div class="container">
		<h3>Editar Empleado</h3><hr>
		<div class="row">
			<div class="col-sm-6">
				
				<div><h4>{{$empleado->nombres ?? ''}}</h4></div>
				<p>Empresa: {{$empresa}} | Rif: {{$empleado->empresa_rif}}</p>
				
				<form action="{{route('rrhh.update',[$empleado->id,$accion ?? '',$encabezadoId ?? '',$fechaIniFin ?? ''])}}" method="POST">
					@method('PUT')
					@csrf
					<div class="form-group">
						<label>Rif del Empleado</label>
						<input type="text" name="rif" value="{{$empleado->rif}}" class="form-control" required>
						<input type="hidden" name="empresa_rif" value="{{$empleado->empresa_rif}}">
					</div>
					<div class="form-group">
						<label>Fecha de Ingreso</label>
						<input type="date" name="fecha_ingreso" class="form-control" value="{{$empleado->fecha_ingreso}}">
					</div>	
				  <div class="form-group">
				    <label for="anio">Sueldo Base</label>
				    <input type="text" value="{{number_format($empleado->sueldo_base,2,',','.') ?? ''}}" class="form-control" name="sueldo_base">				    
				  </div>
				  	 
				 
				  <button type="submit" class="btn btn-primary">Actualizar</button>
				  <button type="reset" class="btn btn-secondary">Resetear valores</button>
				  <button type="button" class="btn btn-danger d-inline" data-toggle="modal" data-target="#exampleModal">
						  Eliminar
						</button>
				</form>
				
			</div>
		</div>


		<!-- Modal -->
				<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
				        <a href="{{route('rrhh.destroy',[$empleado->id,$empleado->empresa_rif,$accion ?? '',$encabezadoId ?? '',$fechaIniFin ?? ''])}}"><button class="btn btn-danger">Eliminar</button></a>			        
				        
				      </div>
				    </div>
				  </div>
				</div>	<!--fin modal-->

	</div>
@endsection