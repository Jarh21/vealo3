@extends('layouts.app')
@section('content')
<div class="container">
	<h3>Detalle Reporte Operaciones Con Divisas</h3>
	<h3>{{$datosEmpresa[0]->nombre_empresa}}</h3>
	<h4>Fecha: {{date('d-m-Y  H:i:s',strtotime($registros[0]->registrado))}}</h4>
	<h4>Tasa cotizada: {{number_format($registros[0]->cotizacion,2,',','.')}}</h4>
	<table border="1">
		<thead>
			<th>Asesor</th>
			<th>Divisa a consumir</th>
			<th>Bs. a consumir</th>
			<th>Divisa recibida</th>
			<th>Vuelto en Fisico</th>
			<th>Divisas por PagoMovil</th>
			<th>Bs.por PagoMovil</th>
			<th>Referencia PagoMovil</th>
			<th>Nº Factura</th>
			<th>Entidad</th>
		</thead>
		<tbody>
			<?php 
				$divisaConsumir=0;
				$bsConsumir =0;
				$divisaRecibida =0;
				$vueltoDivisa=0;
				$vueltoFisico=0;
				$pagoMovil=0;
				$restanteDivisa=0;
			?>
			@foreach($registros as $registro)
			<?php
				 
				$restanteDivisa=$registro->monto_para_cambio_en_pago_movil/$registro->cotizacion;
			?>
			<tr>
				<td>{{$registro->usuario}}</td>
				<td>{{number_format($registro->divisa_a_consumir,2,',','.')}}</td>
				<td>{{number_format($registro->monto_divisa_a_consumir_en_bolivares,2,',','.')}}</td>
				<td>{{number_format($registro->divisa_a_recibir,2,',','.')}}</td>
				<td>{{number_format($registro->divisa_para_cambio_en_efectivo,2,',','.')}}</td>
				<td>{{number_format($restanteDivisa,2,',','.')}}</td>
				<td>{{number_format($registro->monto_para_cambio_en_pago_movil,2,',','.')}}</td>
				<td>{{$registro->referencia_del_pago_movil}}</td>
				<td>{{$registro->numero_factura}}</td>
				<td>{{$registro->entidad}}</td>
			</tr>
			<?php 
					$divisaConsumir = $divisaConsumir+$registro->divisa_a_consumir;
					$bsConsumir = $bsConsumir+$registro->monto_divisa_a_consumir_en_bolivares;
					$divisaRecibida = $divisaRecibida +$registro->divisa_a_recibir;
					$vueltoFisico =$vueltoFisico +$registro->divisa_para_cambio_en_efectivo;
					$vueltoDivisa = $vueltoDivisa +$restanteDivisa;
					$pagoMovil=$pagoMovil + $registro->monto_para_cambio_en_pago_movil;
					$restanteDivisa=0;
			?>
			@endforeach
			<tr>
				<th >Total</th>
				<th>{{number_format($divisaConsumir,2,',','.')}}</th>
				<th>{{number_format($bsConsumir,2,',','.')}}</th>
				<th>{{number_format($divisaRecibida,2,',','.')}}</th>
				<th>{{number_format($vueltoFisico,2,',','.')}}</th>
				<th>{{number_format($vueltoDivisa,2,',','.')}}</th>
				<th>{{number_format($pagoMovil,2,',','.')}}</th>
			</tr>
		</tbody>
	</table>
</div>
@endsection