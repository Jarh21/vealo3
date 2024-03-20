@extends('layouts.app')
@section('content')
	@php 
		//si monedaBase es nacional s calcula el valor de la divisa dividiendo el monto entre la tasa y si es extranjera se multiplica el monto por el valor de la tasa
		$monedaBase = session('monedaBase'); 
		
	@endphp
	<div class="container-fluid">
		<h3>Pagar Facturas {{session('empresaNombre')}} {{session('empresaRif')}} <!-- <a href="{{--route('cuentasporpagar.facturasPorPagar')--}}" class="btn btn-warning btn-sm float-right"><i class="fa fa-step-backward"></i> Regresar</a> --></h3><hr>
		<p>Modo de Pago 
			@if(session('modoPago') == 'bolivares')
				<span class="right badge badge-primary">
					{{session('modoPago')}}</td>
				</span>
			@else
				<span class="right badge badge-success">
					{{session('modoPago')}}</td>
				</span>
			@endif
			Tipo Moneda:<b>{{' '.$monedaBase}}</b>
		</p>
		@if(Session::has('message'))
			<div class="alert alert-danger">
				{!! Session::get('message') !!}
			</div>
   			
		@endif
		
		<form action="{{route('guardarPagarFacturas')}}" method="post">
			@csrf
			<!-- nos traemos el array de facturas para luego volve a enviarlos por un input[] -->
			@if(isset($id_facturas))
				@foreach($id_facturas as $id_factura)
			<!-- hidden -->	<input type="hidden" name="idFacturasPorPagar[]" value="{{$id_factura}}">
				@endforeach
			@endif
			<!-- hidden -->	<input type="hidden" name="codigo_relacion_pago" value="{{$codigo_relacion_pago}}">
			<table class="table" style="font-size:15px">
				<thead>
					<tr>
						<th>Facturas</th>
						<th>Proveedo</th>
						<th>Concepto</th>
						<th>Debitos</th>
						<th>Creditos</th>
						<th>Total Bs.</th>						
						<th>Tasa</th>
						@if($monedaBase=='nacional')
						<th>Divisas</th>
						@else
						<th>Bolivares</th>
						@endif
						<th>Opciones</th>
						
					</tr>
				</thead>				
				<tbody>
					<?php $sumaDebito=0;$sumaCredito=0; $tasa=1; $totalFacturas=0; $totalBs=0; $totalFacturasDivisa=0; $totalDivisa=0; $tasaDelDia=0;?>

					@foreach($cuentas as $cuenta)
						
						@foreach($cuenta['cxp'] as $registro)

						<?php 
							if($cuenta['tasa']>0){ $tasa=$cuenta['tasa']; } 
							
						?>
							<tr style="background-color: {{$cuenta['color']}}">
								<td><!--Facturas -->
									{{$registro->documento}}
								</td>
								<td><!--Proveedo -->
									@if($registro->concepto=='FAC')
										{{$registro->proveedor_nombre}}
									@else
										{{$registro->concepto_descripcion}}											
									@endif
									{{' '.$registro->observacion ?? ''}}
								</td>
								<td><!--Concepto -->
									{{$registro->concepto}}
									{{$registro->fecha_pago ?? ''}}
									{{$registro->nombre ?? ''}}
									{{'#'.$registro->referencia_pago ?? ''}}
								</td>							
								<td><!--Debitos -->
									@if($registro->debitos > 0.00)
										{{number_format($registro->debitos,2)}}
									@endif
								</td> 
								<td class="text-danger"><!--Creditos -->
									@if($registro->creditos > 0.00)
										-{{number_format($registro->creditos,2)}}
									@endif
								</td>
								<td><!-- Total Bs. -->																
								</td>
								<td><!-- Tasa -->
								
									@if(session('modoPago')<>'bolivares')
											
											@if($registro->tasa ==0.00)
												{{$tasa}}<!-- tasa de la factura -->
											@else
												{{$registro->tasa}}<!-- tasa del dia -->
											@endif
									@endif
									
								</td> 
								<td><!-- Divisas -->
								@if($monedaBase=='nacional')
									@if(session('modoPago')<>'bolivares')
										@if($registro->concepto=='CAN')
													
												@if($registro->tasa >0.00)
													@if($registro->monto_divisa <= 0.00)
														{{'- '.number_format($registro->creditos/$registro->tasa,2).'$'}}
													@else	
													{{'--'.$registro->monto_divisa.'$'}}
													@endif
												@else
												{{'---'.number_format($registro->creditos/$tasa,2).'$'}}
												@endif	
											
										@endif	
									
										@if($registro->concepto=='FAC' or $registro->concepto=='NDEB')
										{{number_format($registro->debitos/$tasa,2).'$'}}
										@endif
										@if($registro->concepto=='RIVA' or $registro->concepto=='RISLR' or $registro->concepto=='DESC')
										{{'-'.number_format($registro->creditos/$tasa,2).'$'}}
										@endif
									@endif
								@endif <!-- fin monedaBase -->
								@if($monedaBase=='extranjera')
									@if(session('modoPago')<>'bolivares')

										@if($registro->concepto=='FAC' or $registro->concepto=='NDEB')
										{{number_format($registro->debitos*$tasa,2).'Bs'}}
										<!-- asignamos a la cuenta el valor de la factura * la tasa registrada -->	
										@php $totalDivisa = $totalDivisa + $registro->debitos*$tasa;  @endphp

										@endif

										@if($registro->concepto=='CAN')
													
												@if($registro->tasa >0.00)
													@if($registro->monto_divisa <= 0.00)
														{{'- '.number_format($registro->creditos*$registro->tasa,2).'Bs'}}
													@else	
													{{'-'.number_format($registro->creditos*$registro->tasa).'Bs'}}
													@endif
												@else
												{{'-'.number_format($registro->creditos*$tasa,2).'Bs'}}
												@endif
												<!-- restamos a la cuenta el valor de la deduccion * la tasa registrada -->	
												@php $totalDivisa = $totalDivisa - $registro->creditos*$registro->tasa;  @endphp
											
										@endif	
									
										
										@if($registro->concepto=='RIVA' or $registro->concepto=='RISLR' or $registro->concepto=='DESC')
										{{'-'.number_format($registro->creditos*$registro->tasa,2).'Bs'}}
										<!-- restamos a la cuenta el valor de la deduccion * la tasa registrada -->
										@php $totalDivisa = $totalDivisa - $registro->creditos*$registro->tasa; @endphp

										@endif
									@endif
								@endif <!-- fin monedaBase -->
								</td>
								<td><!-- Opciones -->
									@if($registro->concepto != 'FAC')

									<a href="" data-toggle="modal" data-target="#exampleModal{{$registro->id}}">
										<i class="fa fa-edit"></i>{{$registro->concepto}}
									</a>

									<!-- Modal -->
									<div class="modal fade" id="exampleModal{{$registro->id}}" 
									 role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
									  <div class="modal-dialog">
									    <div class="modal-content">
									    <form id="{{$registro->id}}" name="{{$registro->id}}" action="#" method="post">
											@csrf	
									      <div class="modal-header">
									        <h5 class="modal-title" id="exampleModalLabel">Eliminar Registro</h5>
									        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									          <span aria-hidden="true">&times;</span>
									        </button>
									      </div>
									      
									      	
									      	<div class="modal-body"><h4>
									      			{{$registro->concepto_descripcion}} 
									      			@if($registro->creditos > 0.00)
									      				{{$registro->creditos}}
									      			@endif
									      			@if($registro->debitos > 0.00)
									      				{{$registro->debitos}}
									      			@endif	
									      		</h4>
																				
									      	</div>
									      	<div class="modal-footer">
									        	<!--<button type="button" class="btn btn-primary" data-dismiss="modal">Actualizar</button>-->
									        	<a href="{{route('elimarAsientoCuentasPorPagar',[$registro->id,$registro->codigo_relacion_pago])}}" class="btn btn-danger">Eliminar</a>		        
									    	</div>
									    </form>	  
									    </div>									    
									  </div>
									</div>
									<!--fin modal-->									
									@else
									<a href="" data-toggle="modal" data-target="#exampleModalFAC{{$registro->id}}">
										<i class="fa fa-edit"></i>{{$registro->concepto}}
									</a>

									<!-- Modal -->
									<div class="modal fade" id="exampleModalFAC{{$registro->id}}" 
									 role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
									  <div class="modal-dialog">
									    <div class="modal-content">
									    <form id="{{$registro->id}}" name="{{$registro->id}}" action="#" method="post">
											@csrf	
									      <div class="modal-header">
									        <h5 class="modal-title" id="exampleModalLabel">Sacar La factura de esta relacion de Pagos</h5>
									        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									          <span aria-hidden="true">&times;</span>
									        </button>
									      </div>
									      
									      	
									      	<div class="modal-body"><h4>
									      			Factura Nº {{$registro->documento}} 
									      			@if($registro->creditos > 0.00)
									      				{{'Monto '.$registro->creditos}}
									      			@endif
									      			@if($registro->debitos > 0.00)
									      				{{'Monto '.$registro->debitos}}
									      			@endif	
									      		</h4>
																				
									      	</div>
											@if(session('modoPago')=='bolivares')
									      	<div class="modal-footer">
									        	<!--<button type="button" class="btn btn-primary" data-dismiss="modal">Actualizar</button>-->
									        	<a href="{{route('desvincularAsientoCuentasPorPagarBolivares',[$registro->factura_id,$registro->codigo_relacion_pago])}}" class="btn btn-danger">Sacar</a>		        
												
									    	</div>
											@endif
									    </form>	  
									    </div>									    
									  </div>
									</div>
									<!--fin modal-->
									@endif
								</td>
							</tr>
							<?php
								
								//$sumaDebito += $registro->debitos;
								//$sumaCredito += $registro->creditos;
							?>

							
						@endforeach

						<?php  $totalBs = $cuenta['restaTotal'];  ?>
						<?php
							if($monedaBase=='nacional'){ //esta parte hace el calculo de los montos a cancelas de las facturas en divisas cuando la moneda base es bolivares
								if($totalBs >0.00){
									$totalDivisa = ($totalBs/$cuenta['tasa']);
									//$totalDivisa = 0;//valor falso
								}else{
									$totalDivisa = 0;
								}
							} 
							if($monedaBase=='extranjera'){ // esta comentado porque si la moneda base es divisas los montos quedan en 0 no tocar a menos que entiendas que hace este codigo
								if($totalBs >0.00){
									$totalDivisa = ($totalBs*$cuenta['tasa']);
									//$totalDivisa = 0;//valor falso
								}else{
									$totalDivisa = 0;
								}
							} 					

						?>
						<?php $totalDivisaFormato = number_format($totalDivisa,3) ?>
						<tr>
							@if($monedaBase=='nacional')
								<td colspan="3">Total Factura--</td>
								<td>{{--$cuenta['sumaDebito']--}}</td>
								<td class="text-danger">{{--$cuenta['sumaCredito']--}}</td>
								<td>Bs. {{number_format($totalBs,2)}}</td>							
								@if(session('modoPago')<>'bolivares')
								<td>{{--$cuenta['tasa']--}}</td>
								<td class="text-success">
									Divisa$ {{number_format($totalDivisa,3) ?? 0}}
								</td>
								@endif
							@endif <!-- fin moneda base -->
							
							@if($monedaBase=='extranjera')
							
								<td colspan="3">Total Factura--</td>
								<td>{{--$cuenta['sumaDebito']--}}</td>
								<td class="text-danger">{{--$cuenta['sumaCredito']--}}</td>
								<td>Usd. {{number_format($totalBs,2)}}</td>							
								@if(session('modoPago')<>'bolivares')
								<td>{{--$cuenta['tasa']--}}</td>
								<td class="text-success">
									{{number_format($totalDivisa,3) ?? 0}}
								</td>
								@endif
							@endif <!-- fin moneda base -->
				<!-- va hidden	 --><input type="hidden" name="datosPagoFactura[]" value="{{$totalBs}}|{{$cuenta['tasa']}}|{{$totalDivisa}}|{{$cuenta['proveedor_rif']}}|{{$cuenta['documento']}}|{{$cuenta['n_control']}}|{{$cuenta['igtf']}}|{{$cuenta['factura_id']}}">
							

							
						</tr>
						<?php
							
							$totalFacturas += $totalBs;
						    $totalFacturasDivisa += $totalDivisa;
						    $sumaDebito=0; 
						    $sumaCredito=0;
							$totalDivisa=0; //iniciamos el contador de los montos de la moneda secundaria en 0 esto es en total facturas 
						    $totalBs=0;
						    $tasaDelDia =$cuenta['tasaDelDia']; 
						  ?>	
					@endforeach
					<tr>
					@if($monedaBase=='nacional')
						<td colspan="5"><b>Pendiente Por Cancelar</b></td>
						<td><b>Bs. {{number_format($totalFacturas,2)}}</b></td>
						@if(session('modoPago')<>'bolivares')
							<td></td>
							
							<td class="text-success"><b>Divisa$ {{number_format($totalFacturasDivisa,3)}}</b></td>
							
						@endif
					@endif
					@if($monedaBase=='extranjera')					
						<td colspan="5"><b>Pendiente Por Cancelar</b></td>
						<td><b>Usd. {{number_format($totalFacturas,2)}}</b></td>
						@if(session('modoPago')<>'bolivares')
							<td></td>
							
							<td class="text-success"><b>Bs. {{number_format($totalFacturasDivisa,3)}}</b></td>
							
						@endif
					@endif
					</tr>
				</tbody>
			</table>
			@if(isset($totalFacturas))
				@if($totalFacturas > 0.01)
					<div class="container-fluid">				
					
						<div class="row">
							<div class="col">
								<div class="form-group">
									<label>Tipo de Registro</label>
									<select name="tipo_registro" class="form-control" id="Select_id">
										<option value="">- Selecciones -</option>
										<option value="NDEB">Nota de Debito</option>								
										<option value="NCP">Nota de Credito</option>
										<option value="RISLR">Retencion de ISLR</option>
										<option value="RIVA">Retencion de IVA</option>								
										<option value="DESC">Descuento</option>								
										<option value="CAN">Pago Deuda</option>
									</select>
								</div>
							</div>
							<div class="col">						
								<div class="form-group">
									<label>Modo de Pago</label>
									<select class="form-control" name="modo_pago" id="modo_pago" disabled="true">
										<option value=""> -- -- </option>
										<option value="bolivares"@if($modoPagoSelect == 'bolivares')selected @endif >Bolivares</option>
				      					<option value="dolares" @if($modoPagoSelect == 'dolares')selected @endif >Divisas</option>	
									</select>								
								</div>						
							</div>	
							<div class="col">
								<div class="form-group">
									<label id="lbanco_id" >Banco</label>
									<select name="banco_id" id="banco_id" class="form-control" disabled="true">
										<option value="">- Selecciones -</option>
										@foreach($bancos as $banco)
										<option value="{{$banco->id}}">{{$banco->nombre}}| Nº {{$banco->id}}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="col">	
								<div class="form-group">
									<label id="lreferencia_pago">Referencia Bancaria</label>
									<input type="text" name="referencia_pago" id="referencia_pago" class="form-control" disabled="true">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col">
								<div class="form-group">
									<label for="">Observación</label>
									<input type="text" name="observacion" class="form-control">
								</div>
							</div>	
							<div class="col">	
								<div class="form-group">
									<label>Fecha de Pago</label>
									<input type="date" name="fecha_pago" class="form-control" required>
								</div>
							</div>
							<div class="col">						
								<div class="form-group">
									
									<label>Valor Tasa</label>
									<select class="form-control" name="tipo_tasa" id="tipo_tasa" >																	
										<option value="{{$cuenta['tasa']}}|tasaFactura" @if(session('modoPago')=='bolivares')selected @endif>Tasa de la Factura {{$cuenta['tasa']}}</option>								
										<option value="{{$cuenta['tasaDelDia']}}|tasaActual" @if(session('modoPago')<>'bolivares')selected @endif>Tasa Actual {{$cuenta['tasaDelDia']}}</option>							
										<option value="0|tasaManual">Tasa Manual</option>	
									</select>
									<div id="mtm" class="ocultoFacturaManual">
										<input type="text" name='tasa_manual' id="tasa_manual" class="form-control" placeholder='ingrese el valor de la tasa'>
									</div>
									
								</div>
							</div>
							<div class="col">						
								<div class="form-group">
									
									<label>Factura a la que se aplica</label>
									<select class="form-control" name="nfactura_nota" id="nfactura_nota" >
										<option value="0">Todas</option>
									@foreach($cuentas as $cuenta)						
										@foreach($cuenta['cxp'] as $factura)
											@if($factura->concepto == 'FAC')
											<option value="{{$factura->factura_id}}">{{$factura->documento}}</option>
											@endif
										@endforeach							
									@endforeach									
									</select>
									
								</div>
							</div>
							<div class="col">						
								<div class="form-group">
									<label>Monto</label>
									<input type="text" name="monto" value="" class="form-control" id="moneda1" required>
						<!-- va hidden -->			<input type="hidden" id='total_facturas' name="total_facturas" value="{{$totalFacturas}}">								
								</div>						
							</div>			
											
						</div>
						<div class="row">
							<div class="col">

								<button type="submit" class="btn btn-primary float-right"><i class="fa fa-plus mx-1" aria-hidden="true"></i>Guardar</button>
							</div>
						</div>
								
					</div>
				@else
					<a href="{{route('reciboPagoFacturas',$codigo_relacion_pago)}}"><i class="fa fa-print"></i>Reporte de las facturas canceladas {{$codigo_relacion_pago}} Monto {{number_format($totalDivisa,3) ?? 0}}</a>	
				@endif <!-- fin if($totalFacturas > 0.00)-->
			@endif	<!-- fin totalFacturas -->
		</form>
	</div>
	@endsection
	@section('js')
	<script type="text/javascript">
		/*****dejar selected la tasa del dolar de la factura o el actual con dependiendo si el modo de pago es dolares o bolivares */


	jQuery(document).ready(function(){
			
		$('#mtm').hide();

		/*funcion que formatea el valor numerico al de moneda*/
        $("#moneda1").on({
            "focus": function (event) {
                $(event.target).select();
            },
        
        });


        /*esta funcion habilita y deshabilita los campos segun la operacion seleccionanda en tipo de registro*/
        $( "#Select_id").change(function () {
        	$("#banco_id").prop('disabled', true);
		    $("#referencia_pago").prop('disabled', true);
		  var modopago =  $("#modo_pago").val();
		  var selector = $("#Select_id  option:selected").val();		  
		  var isActivarBanco = <?php echo $isActivarBanco; ?>;
		  var tipoTasaSelect = document.getElementById('tipo_tasa');
		  var monedaBase = '<?php echo $monedaBase; ?>';	
		  console.log(monedaBase);
		 
		  switch(selector){
		    case "NDEB":		      
		      $("#banco_id").prop('disabled', true);
		      $("#referencia_pago").prop('disabled', true);
			  $("#nfactura_nota").prop('disabled',false);
			  $("#nfactura_nota").prop('required',true);
		      break;

		    case "NCP":
		      $("#banco_id").prop('disabled', true);
		      $("#referencia_pago").prop('disabled', true);
			  $("#nfactura_nota").prop('disabled',false);
			  $("#nfactura_nota").prop('required',true);
		      break;	

			case "DESC":
		      $("#banco_id").prop('disabled', true);
		      $("#referencia_pago").prop('disabled', true);
			  $("#nfactura_nota").prop('disabled',false);
			  $("#nfactura_nota").prop('required',true);
		      break;	

		    case "CAN":
				/* verificamos cual es el tipo de moneda si nacional o extranjera */
				if(monedaBase =='nacional'){
					$("#modo_pago").prop('disabled',false)	
					if(modopago=='bolivares'){
						$("#banco_id").prop('disabled', false);
						$("#referencia_pago").prop('disabled', false);
						$("#nfactura_nota").prop('disabled',true);
						$("#nfactura_nota").prop('required',false);
						tipoTasaSelect.selectedIndex = 1;
						document.getElementById('moneda1').value= <?php echo round($totalFacturas,3)?>;
					}else{
						/** cual tasa queda por defecto */
						tipoTasaSelect.selectedIndex = 0;
								
						if(isActivarBanco == '1'){
							$("#banco_id").prop('disabled', false);
							$("#banco_id").prop('required', true);
						}
						document.getElementById('moneda1').value= <?php echo round($totalFacturasDivisa,3)?>;
					}
				}else{ 
					//moneda Base EXTRANJERA
					$("#modo_pago").prop('disabled',false)	
					if(modopago=='bolivares'){
						$("#banco_id").prop('disabled', false);
						$("#referencia_pago").prop('disabled', false);
						$("#nfactura_nota").prop('disabled',true);
						$("#nfactura_nota").prop('required',false);
						tipoTasaSelect.selectedIndex = 1;
						document.getElementById('moneda1').value= <?php echo round($totalFacturas,3)?>;
					}else{
						/** cual tasa queda por defecto */
						tipoTasaSelect.selectedIndex = 0;
								
						if(isActivarBanco == '1'){
							$("#banco_id").prop('disabled', false);
							$("#banco_id").prop('required', true);
						}
						document.getElementById('moneda1').value= <?php echo round($totalFacturas,3)?>;
					}
				}
		      
		      break;
		  }
		});

		$("#modo_pago").change(function(){
			var tipoTasaSelect = document.getElementById('tipo_tasa');
			var modopago = $("#modo_pago  option:selected").val();
			var tipoTasa = $("#tipo_tasa option:selected").val();
			var seleccion = tipoTasa.split('|');
			var isActivarBanco = <?php echo $isActivarBanco; ?>;
			var monedaBase = '<?php echo $monedaBase; ?>';
			if(monedaBase=='nacional'){
			
				switch(modopago){
				
					case "bolivares":
					$("#banco_id").prop('disabled', false);
					$("#referencia_pago").prop('disabled', false);
					$("#tipo_tasa").prop('disabled',false);
					$("#tipo_tasa").prop('required', true);
					///despues de cambiar la seleccion optenemos el nuevo valor y seteamos
					tipoTasaSelect.selectedIndex = 1;
					tipoTasa = $("#tipo_tasa option:selected").val();
					seleccion = tipoTasa.split('|');

					if(seleccion[1] == 'tasaActual'){
						document.getElementById('moneda1').value= <?php echo round($totalFacturasDivisa*$tasaDelDia,3) ?>;
					}
					if(seleccion[1] == 'tasaFactura'){
						document.getElementById('moneda1').value= <?php echo round($totalFacturas,3)?>;
					}
					if(seleccion[1] == ''){
						document.getElementById('moneda1').value= <?php echo round($totalFacturas,3)?>;
					}				     
					
					break;		    
					case "dolares":		      	
						tipoTasaSelect.selectedIndex = 0;
					$("#referencia_pago").prop('disabled', true);
					
						$("#banco_id").prop('disabled', true);
						$("#banco_id").prop('required', false);
						if(isActivarBanco == '1'){
						$("#banco_id").prop('disabled', false);
						$("#banco_id").prop('required', true);
					}
					
					$("#referencia_pago").prop('required', false);
					$("#tipo_tasa").prop('disabled',false);
					$("#tipo_tasa").prop('required',false)		      
					document.getElementById('moneda1').value= <?php echo round($totalFacturasDivisa,3)?>;
					
					break;
				}
			}else{
				//moneda EXTRANJERA ///////$$$$$
				switch(modopago){
				
					case "bolivares":
					$("#banco_id").prop('disabled', false);
					$("#referencia_pago").prop('disabled', false);
					$("#tipo_tasa").prop('disabled',false);
					$("#tipo_tasa").prop('required', true);
					///despues de cambiar la seleccion optenemos el nuevo valor y seteamos
					tipoTasaSelect.selectedIndex = 1;
					tipoTasa = $("#tipo_tasa option:selected").val();
					seleccion = tipoTasa.split('|');

					if(seleccion[1] == 'tasaActual'){
						document.getElementById('moneda1').value= <?php echo round($totalFacturas*$tasaDelDia,3) ?>;
					}
					if(seleccion[1] == 'tasaFactura'){
						document.getElementById('moneda1').value= <?php echo round($totalFacturas*$tasaDelDia,3)?>;
					}
					if(seleccion[1] == ''){
						document.getElementById('moneda1').value= <?php echo round($totalFacturas,3)?>;
					}				     
					
					break;		    
					case "dolares":		      	
						tipoTasaSelect.selectedIndex = 0;
					$("#referencia_pago").prop('disabled', true);
					
						$("#banco_id").prop('disabled', true);
						$("#banco_id").prop('required', false);
						if(isActivarBanco == '1'){
						$("#banco_id").prop('disabled', false);
						$("#banco_id").prop('required', true);
					}
					
					$("#referencia_pago").prop('required', false);
					$("#tipo_tasa").prop('disabled',false);
					$("#tipo_tasa").prop('required',false)		      
					document.getElementById('moneda1').value= <?php echo round($totalFacturas,3)?>;
					
					break;
				}
			}
			
		});

		$("#tipo_tasa").change(function(){
			var tipoTasa = $("#tipo_tasa option:selected").val();
			var modopago = $("#modo_pago  option:selected").val();
			var monedaBase = '<?php echo $monedaBase; ?>';
			var seleccion = tipoTasa.split('|');
			var total_facturas = $("#total_facturas").val();
			
			if(modopago == 'bolivares'){
				if(monedaBase =='nacional'){
					// Moneda Nacional /////
					switch(seleccion[1]){
						case "tasaFactura":
							document.getElementById('moneda1').value= <?php echo round($totalFacturas*$tasaDelDia,3)?>;
						break;

						case "tasaActual":
							document.getElementById('moneda1').value= <?php echo round($totalFacturasDivisa*$tasaDelDia,3) ?>;
						break;
					}
				}else{
					// Moneda Extranjera ////
					switch(seleccion[1]){

						case "tasaFactura":
							
							
							$('#mtm').hide();
							document.getElementById('moneda1').value= parseFloat(total_facturas)*seleccion[0];
						break;

						case "tasaActual":
							
							$('#mtm').hide();
							document.getElementById('moneda1').value= parseFloat(total_facturas)*seleccion[0];
						break;

						case "tasaManual":
							var tasaManual = $("#tasa_manual").val();
							$('#mtm').show();
							document.getElementById('moneda1').value= parseFloat(total_facturas)*tasaManual;

							
						break;	
					}
				}
				
			}
			
		});

		$("#tasa_manual").keyup(function(){
			console.log('presionando teclas');
			/*al escribir en el campo tasa manual se calculan los datos*/
			var tasaManual = $("#tasa_manual").val();
			var modopago = $("#modo_pago  option:selected").val();
			var total_facturas = $("#total_facturas").val();
			var tipoTasa = $("#tipo_tasa option:selected").val();
			var seleccion = tipoTasa.split('|');
			console.log(seleccion[1]);
			if(seleccion[1] == 'tasaManual'){
				console.log('dentro de seleccion');
				document.getElementById('moneda1').value= parseFloat(total_facturas)*tasaManual;				
				
			}
			
		});
	});	
    </script>

    <script type="text/javascript">
	//confirmar eliminar
	function confirmarEliminar(id){
		
		var form = id;
        var nombreFormulario = document.getElementById(form);
		
		var option = confirm("Confirma que desea eliminar el registro Nº "+id);
		alert(form);
		if(option == true){
			
		nombreFormulario.submit();
		}
		
	}
</script>	
@endsection