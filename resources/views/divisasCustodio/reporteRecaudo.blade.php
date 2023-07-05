@extends('layouts.app')
@section('content')
	<div class="container">
		<h3>Reporte Operaciones Divisas Recaudo</h3>
		<form class="text-left" action="{{route('divisa.buscar.reporte.recaudo')}}" method="post">
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
	                <input type="date" required class="form-control" value="{{$fecha ?? date('Y-m-d')}}" id="fecha" name="fecha">
	            </div>
			</div>
			<button type="submit" class="btn btn-primary">Buscar</button>
		</form>
		@if(isset($recaudos))
		<h3>{{$datosEmpresa[0]->nombre_empresa}}</h3>
		<table class="table">
			<thead>
				<th>Fecha</th>
				<th>Usuario</th>
				
			</thead>
			<tbody>
				@foreach($recaudos as $recaudo)
				<tr>
					<td>{{$recaudo['fecha']}}</td>
					<td>{{$recaudo['usuario']}} <!-- Button trigger modal -->
						<button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#exampleModal{{$recaudo['id']}}">
					  	MovPagos Siace
						</button>
						<!-- Modal -->
						<div class="modal fade" id="exampleModal{{$recaudo['id']}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
						  <div class="modal-dialog modal-dialog-scrollable">
						    <div class="modal-content">
						      <div class="modal-header">
						        <h5 class="modal-title" id="exampleModalLabel">{{$recaudo['usuario']}}</h5>
						        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						          <span aria-hidden="true">&times;</span>
						        </button>
						      </div>
						      <div class="modal-body">
						       <table class="table">
						       		<thead>
						       		<tr>	
						       			<th>Cliente</th>
						       			<th>Divisa</th>
						       			<th>Monto Bs.</th>
						       		</tr>
						       		</thead>
						       		<tbody>
						       			<?php $divisa=0;$monto=0; ?>
						       		@foreach($recaudo['mov_pagos'] as $movpago)
						       		<tr>						       			
						       			<td>{{$movpago->cliente}}</td>
						       			<td>{{$movpago->monto_moneda}}</td>
						       			<td>{{number_format($movpago->monto,2,',','.')}}</td>
						       			<?php  
						       				$divisa = $divisa + $movpago->monto_moneda;
						       				$monto = $monto + $movpago->monto;
						       			?>
						       		</tr>
						       		@endforeach
						       		</tbody>
						       </table>
						       
						      </div>
						      <div class="modal-footer">
						      	<p>Total Divisa$: <b>{{$divisa}}</b> &nbsp; &nbsp; Total Bs: <b>{{number_format($monto,2,',','.')}}</b></p>
						        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
						        	        
						        
						      </div>
						    </div>
						  </div>
						</div>	<!--fin modal-->
					</td>
					<td colspan="6">
						<table>
							<thead>
								<th>Hora</th>
								<th>Cotizacion</th>
								<th>Divisa Consumida</th>
								<th>Bs. Divisa Consumida</th>
								<th>Divisa Recibida</th>
								<th>Vuelto En Fisico</th>
								<th>Pago Movil</th>
							</thead>
							<tbody>
							@foreach($recaudo['cotizacion'] as $detalle)
							<tr>
								<td>{{date('H:i:s',strtotime($detalle->registrado))}}</td>
								<td>{{number_format($detalle->cotizacion,2,',','.')}}</td>
								<td>{{number_format($detalle->divisa_consumida,2,',','.')}}</td>
								<td>{{number_format($detalle->monto_en_bs_divisa_consumida,2,',','.')}}</td>
								<td>{{number_format($detalle->divisa_recibida,2,',','.')}}</td>
								<td>{{number_format($detalle->divisa_para_cambio_en_efectivo,2,',','.')}}</td>
								<td>{{number_format($detalle->pagomovil,2,',','.')}}</td>
							</tr>
							@endforeach
							<tr>
								<th colspan="2">Total</th>
								<th>{{number_format($recaudo['divisa_consumida'],2,',','.')}}</th>
								<th>{{number_format($recaudo['monto_en_bs_divisa_consumida'],2,',','.')}}</th>
								<th>{{number_format($recaudo['divisa_recibida'],2,',','.')}}</th>
								<th>{{number_format($recaudo['divisa_para_cambio_en_efectivo'],2,',','.')}}</th>
								<th>{{number_format($recaudo['pagomovil'],2,',','.')}}</th>
							</tr>
							</tbody>
						</table>
					</td>
					
				</tr>
				
				@endforeach
			</tbody>
		</table>
		@endif
	</div>
@endsection