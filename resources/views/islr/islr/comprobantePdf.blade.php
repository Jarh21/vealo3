<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<style type="text/css">
	  html {
			margin: 10pt 25pt;
		}
	.Estilo2 {
		font-family: Verdana, Arial, Helvetica, sans-serif;
		font-size: 10px;
		font-weight: bold;
	}
	.sinBorde td {border: 0;}
	.Estilo {font-size: 10px; font-family: Verdana, Arial, Helvetica, sans-serif;}
	.titulo {font-size: 13px; font-family: Verdana, Arial, Helvetica, sans-serif;}
</style>
	<div >		
			
		@foreach($islr as $datos)
				
			<h4 class="text-center">{{$datos->empresa}}</h3>
			<h5 class="text-center">Rif. {{$datos->e_rif}}</h4>
			<div class="text-center " style="padding-left: 225px">			
				<p class="text-center  Estilo " style="width:310px" >{{$datos->e_direccion}}</p>				
				
			</div>
			
			<div class="container-fluid">
				
				<p class="float-left Estilo">Fecha: {{date('d-m-Y',strtotime($datos->fecha))}}</p>
				
				<p class="float-right Estilo">Nº De Control: {{$nControl=str_pad(($datos->nControl), 8, "0", STR_PAD_LEFT);}}</p>
				
			</div>
			<br><br><br>
			<div class="container-fluid">
				<div class="row ">
					<div class=" col-12 text-center ">
						<span class="titulo mx-3 my-2 text-center">DATOS DEL CONTRIBUYENTE</span>
					</div>
					
				</div>
				<br>
				<div class="row">
					
						<div class="col">
							<p class='Estilo'>PERSONA: {{$datos->tipo_contribuyente}} &nbsp;{{$datos->proveedor}}  &nbsp;Nº Rif: &nbsp;{{$datos->p_rif}}</p>
							
						</div>
						
					
				</div>

				
				<P  class="Estilo">DIRECCIÓN: {{$datos->p_direccion}}</P>
				<table>
					<tr>
						<td class="Estilo">CONCEPTO:</td>
						<td class="Estilo">&nbsp;&nbsp;Nº FACTURA</td>
						<td class="Estilo">&nbsp;&nbsp;Nº CONTROL</td>
						<td class="Estilo">&nbsp;&nbsp;FECHA FACTURA</td>
					</tr>
					@foreach($detalleRetenciones as $detalleRetencion)
					<tr>
						<td class="Estilo">{{$detalleRetencion->concepto}}</td>
						<td class="Estilo">&nbsp;&nbsp;{{$detalleRetencion->nFactura}}</td>
						<td class="Estilo">&nbsp;&nbsp;{{$detalleRetencion->nControl}}</td>
						<td class="Estilo">&nbsp;&nbsp;{{date('d-m-Y',strtotime($detalleRetencion->fecha_factura)) ?? ''}}</td>
					</tr>
					@endforeach

				</table>
				<p  class="Estilo">@if($datos->serie) SERIE: {{$datos->serie}} &nbsp;|&nbsp;@endif Nº DE EGRESO/CHEQUE: {{$datos->n_egreso_cheque}}</p>		
				
				<br><br>
				<div class="row ">
					<div class=" col-12 text-center ">
						<span class=" mx-3 my-4 titulo">INFORMACION DEL AGENTE DE RETENCION</span>
					</div>
					
				</div>
								

				<table class="table">
					<thead>
					<tr>
						<td  class="Estilo">MONTO PAGADO O ABONADO</td>
						<td  class="Estilo">PORCENTAJE O TARIFA</td>
						<td  class="Estilo">MONTO RETENIDO</td>
						<td  class="Estilo">SUSTRAENDO</td>
					</tr>
					<thead>
					<tbody>
						@foreach($detalleRetenciones as $detalleRetencion)
						<tr>
							<td class="text-center Estilo">{{number_format($detalleRetencion->monto,2,',','.')}}</td>
							<td class="text-center Estilo">{{$detalleRetencion->porcentaje_retencion}}%</td>
							<td class="text-center Estilo">{{number_format($detalleRetencion->monto_retenido,2,',','.')}}</td>
							<td class="text-center Estilo">{{number_format($detalleRetencion->sustraendo,2,',','.')}}</td>
						</tr>
						@endforeach
						<tr >
							<td colspan=3><p class="text-right Estilo">TOTAL IMPUESTO A RETENER</p></td>
							<td class="text-center Estilo"><b>{{number_format($datos->total_retener,2,',','.')}}</b></td>					
						</tr>
					</tbody>	
				</table>
				<table class="table sinBorde table-sm">
					<tr>
						<td><div class="col-6 Estilo"><p>Son(En letras):</p><p>{{$datos->total_letras}}</p></div></td>
						<td><div class="col-6  Estilo">AGENTE DE RETENCIÓN</div></td>
					</tr>
				</table>
				<div class="row">
					
					
				</div>
				
			</div>
			
		
			<div class="row">
				<div class="col">
					<div class="p-2 Estilo">Fecha: ________________</div>
					<div class="p-2 Estilo">Recibido Por: ______________________</div>
				</div>
				<div class="col">
					<div class="text-center Estilo" >
						@if(!empty($datos->firma))
							<img id='imagenFirma' src="{{asset($datosEmpresa->firma)}}" style="width:220px">
						@else
							____________________________
						@endif	
					</div>
					<div class=" text-center Estilo">FIRMA</div>
				</tr>
			</div>
		@endforeach	
	</div>
