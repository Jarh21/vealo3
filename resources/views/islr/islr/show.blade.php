@extends('layouts.app')
@section('content')
	<div class="content">		
		<button type="button" class="btn btn-success d-print-none" onclick="javascript:window.print()">
			<i class="fa fa-print" aria-hidden="true"></i>Imprimir
		</button>
		<span class="d-print-none">Firma Digital</span>
		<input class="d-print-none" type="checkbox" name="firma" id="firma"  onclick="seleccionado();">
		<a href="{{url('/regisretenciones')}}" class="btn btn-primary d-print-none float-right"><i class="fa fa-home" aria-hidden="true"></i>Inicio</a>
		<hr class="d-print-none">
		<BR>
		@foreach($islr as $datos)
				
			<h3 class="text-center">{{$datos->empresa}}</h3>
			<h4 class="text-center">Rif. {{$datos->e_rif}}</h4>
			<div class="col-12 d-flex justify-content-center">
				<p class="text-center" style="width: 400px;text-align: center">{{$datos->e_direccion}}</p>
			</div>
			
			<div class="container-fluid">
				<?php 
					$fecha = date('d-m-Y',strtotime($datos->fecha));

				?>
				<p class="float-left">Fecha: {{$fecha}}</p>
				<?php
					//completa con ceros(0) en numero de control 
		    		$nControl=str_pad(($datos->nControl), 8, "0", STR_PAD_LEFT);
		    	?>
				<p class="float-right">Nº De Control: {{$nControl}}</p>
				
			</div>
			<br><br><br>
			<div class="container-fluid">
				<div class="row ">
					<div class=" col-12 d-flex justify-content-center ">
						<h4 class=" mx-3 my-2 ">DATOS DEL CONTRIBUYENTE</h4>
					</div>
					
				</div>
				<br>
				<div class="row">
					
						<div class="col">
							<p>PERSONA: {{$datos->tipo_contribuyente}} &nbsp;{{$datos->proveedor}}  &nbsp;Nº Rif: &nbsp;{{$datos->p_rif}}</p>
							
						</div>
						
					
				</div>

				<?php 
					//dar formato de moneda al monto
		    		$monto= number_format($datos->monto,2,',','.');
		    		//$monto_retenido= number_format($datos->monto_retenido,2,',','.');
		    		$sustraendo= number_format($datos->sustraendo,2,',','.');
		    		$total_retener= number_format($datos->total_retener,2,',','.');


		    		/*si numero de factura y de control van en blanco coloca N/A */
					if (isset($datos->nFactura)) {
						$nFactura = $datos->nFactura;
					}else{
						$nFactura='N/A';
					}

					if(isset($datos->nControlFactura)){
						$nControlFactura = $datos->nControlFactura;
					}else{
						$nControlFactura='N/A';
					}
				?>

				<P>DIRECCIÓN: {{$datos->p_direccion}}</P>
				<table>
					<tr>
						<td>CONCEPTO:</td>
						<td>&nbsp;&nbsp;Nº FACTURA</td>
						<td>&nbsp;&nbsp;Nº CONTROL</td>
						<td>&nbsp;&nbsp;FECHA FACTURA</td>
					</tr>
					@foreach($detalleRetenciones as $detalleRetencion)
					<tr>
						<td>{{$detalleRetencion->concepto}}</td>
						<td>&nbsp;&nbsp;{{$detalleRetencion->nFactura}}</td>
						<td>&nbsp;&nbsp;{{$detalleRetencion->nControl}}</td>
						<td>&nbsp;&nbsp;{{date('d-m-Y',strtotime($detalleRetencion->fecha_factura)) ?? ''}}</td>
					</tr>
					@endforeach

				</table>
				<p>@if($datos->serie) SERIE: {{$datos->serie}} &nbsp;|&nbsp;@endif Nº DE EGRESO/CHEQUE: {{$datos->n_egreso_cheque}}</p>		
				
				<br><br><br>
				<div class="row ">
					<div class=" col-12 d-flex justify-content-center ">
						<h4 class=" mx-3 my-4 ">INFORMACION DEL AGENTE DE RETENCION</h4>
					</div>
					
				</div>
								

				<table class="table">
					<thead>
					<tr>
						<td>MONTO PAGADO O ABONADO</td>
						<td>PORCENTAJE O TARIFA</td>
						<td>MONTO RETENIDO</td>
						<td>SUSTRAENDO</td>
					</tr>
					<thead>
					<tbody>
						@foreach($detalleRetenciones as $detalleRetencion)
						<tr>
							<td class="text-center">{{number_format($detalleRetencion->monto,2,',','.')}}</td>
							<td class="text-center">{{$detalleRetencion->porcentaje_retencion}}%</td>
							<td class="text-center">{{number_format($detalleRetencion->monto_retenido,2,',','.')}}</td>
							<td class="text-center">{{number_format($detalleRetencion->sustraendo,2,',','.')}}</td>
						</tr>
						@endforeach
						<tr >
							<td colspan=3><p class="text-right">TOTAL IMPUESTO A RETENER</p></td>
							<td class="text-center"><b>{{$total_retener}}</b></td>					
						</tr>
					</tbody>	
				</table>
				<div class="row">
					<div class="col"><p>Son(En letras):</p><p>{{$datos->total_letras}}</p></div>
					<div class="col text-center">AGENTE DE RETENCIÓN</div>
				</div>
				
			</div>
			
		@endforeach
		<div class="row">
			<div class="col">
				<div class="p-4">Fecha: ________________</div>
				<div class="p-4">Recibido Por: ______________________</div>
			</div>
			<div class="col">
				<div class="text-center" >
					@if(!empty($datos->firma))
						<img id='imagenFirma' src="{{asset($datos->firma)}}" style="visibility: hidden;">
					@else
						____________________________
					@endif	
				</div>
				<div class="p-4 text-center">FIRMA</div>
			</tr>
		</div>
	</div>
@endsection
@section('js')
<script type="text/javascript">
	function seleccionado(){
		var imagen = document.getElementById('imagenFirma');
		if(document.getElementById('firma').checked==true){
			imagen.style.visibility='visible';
			
		}else{
			
			imagen.style.visibility='hidden'; 
		}
	}
</script>
@endsection