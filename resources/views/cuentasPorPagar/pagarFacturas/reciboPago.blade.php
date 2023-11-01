@extends('layouts.app')
@section('content')

<div class="content">
	<div>
		<a href="" class="btn btn-warning btn-sm float-right d-print-none" onclick="javascript:window.print();" >Imprimir</a>
	</div>
	<table class="ml-5" style="width: 800px; height: 50px;">
		<tr>
			<td>
				<img src="{{ asset(session('logo_empresa'))}}" alt="AdminLTE Logo" class="" style="opacity: .8" width="100px">
            </td>
			<td>
				<p>{{session('nombre_general_empresa').' '.date('d-m-Y')}}</p>
			</td>
		</tr>
	</table>
	
	<h4 class="my-3 mx-5">{{session('empresaNombre')}} {{session('empresaRif')}} </h4>
	<div class="my-3 mx-5">
		{{$datosDelPago[0]['cxp'][0]->proveedor_nombre}} {{$datosDelPago[0]['cxp'][0]->proveedor_rif}}
	</div>	
	<div class="row">
		<div class="col-6">
		<table border="1" class="ml-5" style="width: 800px; height: 50px;">
			
			<tr>
				<th>Factura</th>
				<th>Debitos</th>
				<th>Creditos</th>			
				<th>Divisas</th>				
				<th>Bolivares</th>
				<th>Tasa</th>
				<th>Concepto</th>				
				
			</tr>
			<?php $monto=0;$suma=0; $sumaDivisa=0;?>
			@foreach($datosDelPago as $datos)
				<tr>
					<td></td>
				</tr>
				@foreach($datos['cxp'] as $cxp)
				
				<tr>
					<td  class="mx-3">{{$cxp->documento}}</td><!--Factura-->
					<td>{{$cxp->debitos ?? 0}}</td>
					<td>{{$cxp->creditos ?? 0}}</td>				
					<td><!--Divisa Entregadas-->
						@if($cxp->monto_divisa > 0.00)
							
							{{$cxp->monto_divisa.'Monto Divisa'}}
													
						@endif
					</td>
					
					<td>
						@if($cxp->monto_bolivares > 0.00)
							{{$cxp->monto_bolivares}}		
						@endif
					</td>
					<td>
					{{$cxp->tasa}}
					</td> 
					<td>
						@if($cxp->concepto=='FAC')
						{{"MONTO FACTURA"}}		
						@else
						{{$cxp->concepto_descripcion}}
						@endif
					</td>					
					
					<?php
						if($cxp->concepto=='CAN') 
						$suma += $cxp->creditos; 
						$sumaDivisa += $cxp->monto_divisa;
					?>
					
				</tr>
				
				@endforeach

			@endforeach
			<tr>
				<td colspan="2"><b>Total </b></td>
				<td><b> {{number_format($suma,2).'Bs'}} </b></td>
				<td><b class="text-success" style="font-size:25px">{{number_format($sumaDivisa,2).'$' }}<b></td>
			</tr>
		</table>
		<br><br>
		<table  style="width: 800px; height: 100px;" class=" ml-5">
			<tr>
				<td style="text-align: center;">___________________</td>
				<td style="text-align: center;">___________________</td>
			</tr>
			<tr>
				<td style="text-align: center;">Firma del Proveedor</td>
				<td style="text-align: center;">Firma del Comprador</td>
			</tr>
		</table>
		</div>
	</div>
	
</div>
@endsection