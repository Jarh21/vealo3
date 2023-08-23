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
				<th>Dinero Entregadas</th>
				<th>Concepto</th>				
				
			</tr>
			<?php $monto=0;$suma=0; ?>
			@foreach($datosDelPago as $datos)
				@foreach($datos['cxp'] as $cxp)
				@if($cxp->concepto=='CAN')
				<tr>
					<td  class="mx-3">{{$cxp->documento}}</td><!--Factura-->				
					<td><!--Divisa Entregadas-->
						@if($cxp->monto_divisa > 0.00)
						<?php $monto = $cxp->monto_divisa; ?>
						{{$cxp->monto_divisa.'$'}}
						@else
							
							<?php 
								if($cxp->monto_bolivares > 0.00){
									
								
									$monto = $cxp->monto_bolivares/$cxp->tasa;
								}else{
									$monto = $cxp->creditos;
								}							
							?>
							<?php //$divisaCambio = $cxp->monto_bolivares/$cxp->tasa; ?>
							{{number_format($cxp->monto_bolivares,2)}} Bs. tasa({{$cxp->tasa }}) = {{number_format($monto,2).'$'}}
							
						@endif
					</td> 
					<td>
						{{$cxp->concepto_descripcion}}
					</td>					
					
					<?php $suma += $monto; ?>
					
				</tr>
				@endif
				@endforeach

			@endforeach
			<tr>
				<td colspan="1"><b>Totla </b></td>
				<td><b>{{number_format($suma,2)}}</b></td>
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