@extends('layouts.app')
@section('content')
	<div class="container">
		<h3>Detalle de la Cuenta Por Pagar</h3>
		<form action="{{route('cuentasporpagar.view')}}" method="post">
			@csrf
			<input type="hidden" name="empresa_rif" value="{{$detalleCuentas[0]->empresa_rif}}|conexion}}">
			<input type="hidden" name="fecha_desde" value="{{$detalleCuentas[0]->cierre}}">
			<input type="hidden" name="fecha_hasta" value="{{$detalleCuentas[0]->cierre}}">
			<input type="hidden" name="proveedor_nombre" value="">
			<button type="submit" class="btn btn-success float-right">Regresar</button>
		</form>
		<hr>
		<p>{{$detalleCuentas[0]->empresa_rif}} {{$detalleCuentas[0]->nombre}}</p>
		<p>Nº Factura {{$detalleCuentas[0]->documento}}</p>
		<p>Nº Control {{$detalleCuentas[0]->n_control}}</p>
		<table class="table">
			<thead>
				<tr>					
					<th>Proveedor</th>					
					<th>Fecha Ingreso</th>
					<th>Tipo asiento</th>
					<th>Debitos</th>
					<th>Creditos</th>
					<th>Fecha Pago</th>
					<th>Concepto</th>
				</tr>
			</thead>
			<tbody>
				@php $debitos=0; $creditos=0; @endphp
				@foreach($detalleCuentas as $detalleCuenta)
				<tr>					
					<td>{{$detalleCuenta->proveedor_nombre}}</td>				
					<td>{{$detalleCuenta->cierre}}</td>
					<td>{{$detalleCuenta->concepto_descripcion}}</td>
					<td>{{number_format($detalleCuenta->debitos,2,',','.')}}</td>
					<td>{{number_format($detalleCuenta->creditos,2,',','.')}}</td>
					<td>{{$detalleCuenta->fecha_pago}}</td>
					<td>{{$detalleCuenta->observacion}}</td>
				</tr>
				@php 
					$debitos = $debitos+$detalleCuenta->debitos;
					$creditos = $creditos+$detalleCuenta->creditos; 
				@endphp
				@endforeach
				<tr>
					<th colspan="3">Total</th>
					<th>{{number_format($debitos,2,',','.')}}</th>
					<th>{{number_format($creditos,2,',','.')}}</th>
				</tr>
			</tbody>
			<tfooter>
				<tr>
	                <th colspan="2" style="text-align:right">Total </th>
	                <th></th>
	            </tr>
			</tfooter>	
		</table>
	</div>
@endsection	