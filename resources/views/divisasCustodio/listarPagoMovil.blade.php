@extends('layouts.app')
@section('content')
<div class="container">
	<h3>Procesar Pago Movil de Operaciones con Divisas</h3><hr>
	<form class="text-left" action="{{route('buscar.listar.pago.movil')}}" method="post">
    	@csrf
        <div class="form-row">
            <div class="form-group col-6">
            	<label>Empresa</label>
	            	<select name="conexion" class="form-control" required>
	            		<option value="">-- Seleccione Empresa --</option>
	            		@foreach($empresas as $empresa)
	            			<option value="{{$empresa->basedata}}" @if(isset($datosEmpresa)) @if($datosEmpresa[0]->conexion==$empresa->basedata)selected @endif @endif>{{$empresa->nombre}}
	            			</option>
	            		@endforeach
	            	</select>
                <label for="fecha" class="font-weight-bolder">Fecha a buscar</label>
                <input type="date" class="form-control" id="fecha" value="{{$fecha ?? date('Y-m-d')}}"name="fecha">
            </div>
		</div>
		<button type="submit" class="btn btn-primary">Buscar</button>
	</form>
	@if(isset($pagosMoviles))
	<h3>{{$datosEmpresa[0]->nombre_empresa}}</h3>
	<table class="table">
		<thead>
			<th>Cotizacion</th>
			<th>Factura</th>
			<th>Asesor</th>
			<th>Monto Pago Movil</th>
			<th>Referencia</th>
			<th>Tlf Cliente</th>
			<th>Entidad Financiera</th>
			<th>Fecha</th>
			<th>Acción</th>
		</thead>
		<tbody>
			@foreach($pagosMoviles as $pagomovil)
			<tr>
				<td>{{number_format($pagomovil->cotizacion,2,',','.')}}</td>
				<td>{{$pagomovil->numero_factura}}</td>
				<td>{{$pagomovil->usuario}}</td>
				<td>{{number_format($pagomovil->monto_para_cambio_en_pago_movil,2,',','.')}}</td>
				<td>{{$pagomovil->referencia_del_pago_movil}}</td>
				<td>{{$pagomovil->tlf_cliente}}</td>
				<td>{{$pagomovil->entidad}}</td>
				<td>{{$pagomovil->registrado}}</td>
				<td>

					@if(empty($pagomovil->referencia_del_pago_movil))

					<a href="{{route('procesar.pago.movil',[$conexion,$pagomovil->keycodigo])}}" class="btn btn-primary btn-sm">Pagar</a>
					<!-- Button trigger modal -->
						<button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#exampleModal{{$pagomovil->keycodigo}}">
						  Anular
						</button>
					@endif
				</td>
			</tr>
			<!-- Modal -->
				<div class="modal fade" id="exampleModal{{$pagomovil->keycodigo}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
				  <div class="modal-dialog">
				    <div class="modal-content">
				      <div class="modal-header">
				        <h5 class="modal-title" id="exampleModalLabel">Eliminar</h5>
				        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				          <span aria-hidden="true">&times;</span>
				        </button>
				      </div>
				      <div class="modal-body">
				       ¿Confirma que desea ANULAR el registro del pago movil? del asesor {{$pagomovil->usuario}}, con un monto de {{number_format($pagomovil->monto_para_cambio_en_pago_movil,2,',','.')}}, una vez realizado no hay vuelta a tras.
				      
				      </div>
				      <div class="modal-footer">
				        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
				        <a href="{{route('anular.pago.movil',[$conexion,$pagomovil->keycodigo])}}"><button class="btn btn-danger">Anular</button></a>			        
				        
				      </div>
				    </div>
				  </div>
				</div>	
			<!--fin modal-->	
			@endforeach
		</tbody>
	</table>
	@endif
</div>
@endsection