@extends('layouts.app')
@section('content')
<div class="container">
	<h3>Reporte General de pagos con Divisas</h3>
	<form class="text-left" action="{{route('divisas.reporte.general.buscar')}}" method="post">
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
                <input type="date" class="form-control" value="{{$fecha ?? date('Y-m-d')}}" id="fecha" name="fecha">
            </div>
		</div>
		<button type="submit" class="btn btn-primary">Buscar</button>
	</form>
	@if(isset($registrosDiario))
	<h3>{{$datosEmpresa[0]->nombre_empresa}}</h3>
	<table border="1" class="mt-2">
		<thead>
			<tr>				
    			<th>Hora</th>
    			<th>Cotización</th>
    			<th>$Divisas a consumir</th>
    			<th>Bs. a consumir</th>
    			
    			<th>Vuelto en $</th>
    			<th>Vuelto Pago Movil</th>   			
    			
    			<th>Fecha</th>
    			
			</tr>
		</thead>
		 
		<tbody>
			<?php 

				$divisaConsumir=0;
				$bsConsumir =0;				
				$vueltoDivisa=0;
				$pagoMovil=0;
				$restanteDivisa=0;
			?>
			@foreach($registrosDiario as $registroDiario)

			<?php
				 if($registroDiario->cotizacion==0){
				 	$cotizacionNula='Existe un registro sin el valor de la cotizacion, comunicar al departamento de sistemas';
				 	continue;
				 }else{
				 	$restanteDivisa=$registroDiario->monto_para_cambio_en_pago_movil/$registroDiario->cotizacion;
				 }
				
			?>
			
			<tr>
				<td>{{date('H:i:s',strtotime($registroDiario->registrado))}}</td>
				<td><a href="{{route('divisa.reporte.detallado.gerencia',[$conexion,$registroDiario->cotizacion,$registroDiario->fecha])}}">{{number_format($registroDiario->cotizacion,2,',','.')}}</a></td>
				<td>{{number_format($registroDiario->divisa_a_consumir,2,',','.')}}</td>
				<td>{{number_format($registroDiario->monto_divisa_a_consumir_en_bolivares,2,',','.')}}</td>			
				<td>{{number_format($restanteDivisa,2,',','.')}}</td>
				<td>{{number_format($registroDiario->monto_para_cambio_en_pago_movil,2,',','.')}}</td>				
				<td>{{date('d-m-Y',strtotime($registroDiario->fecha))}}</td>
				<?php 
					$divisaConsumir = $divisaConsumir+$registroDiario->divisa_a_consumir;
					$bsConsumir = $bsConsumir+$registroDiario->monto_divisa_a_consumir_en_bolivares;				
					$vueltoDivisa = $vueltoDivisa +$restanteDivisa;
					$pagoMovil=$pagoMovil + $registroDiario->monto_para_cambio_en_pago_movil;
					$restanteDivisa=0;
				?>
			</tr>
			@endforeach
			<tr>
				<th colspan="2">Total</th>
				<th>{{number_format($divisaConsumir,2,',','.')}}</th>
				<th>{{number_format($bsConsumir,2,',','.')}}</th>				
				<th>{{number_format($vueltoDivisa,2,',','.')}}</th>
				<th>{{number_format($pagoMovil,2,',','.')}}</th>
			</tr>	
		</tbody>
		
	</table>
	@if(isset($cotizacionNula))
			<div class="alert alert-danger">
				<p>{{$cotizacionNula}}</p>
			</div>
				
			@endif
	@endif
</div>
@endsection