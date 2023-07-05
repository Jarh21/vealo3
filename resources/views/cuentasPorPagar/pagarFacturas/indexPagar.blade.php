@extends('layouts.app')
@section('content')
	<div class="container">
		<h3>Pagar Facturas {{session('empresaNombre')}} {{session('empresaRif')}} <!-- <a href="{{--route('cuentasporpagar.facturasPorPagar')--}}" class="btn btn-warning btn-sm float-right"><i class="fa fa-step-backward"></i> Regresar</a> --></h3><hr>
		<p>Tipo Moneda 
			@if(session('modoPago') == 'bolivares')
				<span class="right badge badge-primary">
					{{session('modoPago')}}</td>
				</span>
			@else
				<span class="right badge badge-success">
					{{session('modoPago')}}</td>
				</span>
			@endif
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
				<input type="hidden" name="idFacturasPorPagar[]" value="{{$id_factura}}">
				@endforeach
			@endif
			<input type="hidden" name="codigo_relacion_pago" value="{{$codigo_relacion_pago}}">
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
						<th>Divisas</th>						
						<th>Opciones</th>
						
					</tr>
				</thead>				
				<tbody>
					<?php $sumaDebito=0;$sumaCredito=0; $tasa=1; $totalFacturas=0; $totalBs=0; $totalFacturasDivisa=0; ?>

					@foreach($cuentas as $cuenta)
						
						@foreach($cuenta['cxp'] as $registro)
						<?php if($cuenta['tasa']>0){ $tasa=$cuenta['tasa']; } ?>
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
								</td>
								<td><!--Concepto -->
									{{$registro->concepto}}
								</td>							
								<td><!--Debitos -->
									@if($registro->debitos > 0.00)
										{{$registro->debitos}}
									@endif
								</td> 
								<td class="text-danger"><!--Creditos -->
									@if($registro->creditos > 0.00)
										-{{$registro->creditos}}
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
								@if(session('modoPago')<>'bolivares')
									@if($registro->concepto=='CAN')
												
											@if($registro->tasa >0.00)
												@if($registro->monto_divisa <= 0.00)
													{{'- '.round($registro->creditos/$registro->tasa,2).'$'}}
												@else	
												{{'-'.$registro->monto_divisa.'$'}}
												@endif
											@else
											{{'- '.round($registro->creditos/$tasa,2).'$'}}
											@endif	
										
									@endif	
								
									@if($registro->concepto=='FAC' or $registro->concepto=='NDEB')
									{{round($registro->debitos/$tasa,2).'$'}}
									@endif
									@if($registro->concepto=='RIVA' or $registro->concepto=='RISLR' or $registro->concepto=='DESC')
									{{'-'.round($registro->creditos/$tasa,2).'$'}}
									@endif
								@endif
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
											<!--	<input type="text" name="id_cuentas_por_pagar" value="{{$registro->id}}">
												@if($registro->debitos > 0.00)
												<input type="number" name="monto_registro" value="{{$registro->debitos}}">
												<input type="text" name="tipo_registro" value="dibitos">
												@endif
												@if($registro->creditos > 0.00)
												<input type="number" name="monto_registro" value="{{$registro->creditos}}">
												<input type="text" name="tipo_registro" value="creditos">
												@endif
												<input type="text" name="codigo_relacion_pago" value="{{$registro->codigo_relacion_pago}}">		-->										
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

						<?php $totalBs = $cuenta['restaTotal'];  ?>
						<?php 
							if($totalBs >0.00){
								$totalDivisa = ($totalBs/$cuenta['tasa']);
							}else{
								$totalDivisa = 0;
							} 						

						?>
						<?php $totalDivisaFormato = number_format($totalDivisa,3) ?>
						<tr>
							<td colspan="3">Total Factura--</td>
							<td>{{--$cuenta['sumaDebito']--}}</td>
							<td class="text-danger">{{--$cuenta['sumaCredito']--}}</td>
							<td>Bs. {{$totalBs}}</td>							
							@if(session('modoPago')<>'bolivares')
							<td>{{$cuenta['tasa']}}</td>
							<td class="text-success">Divisa$ {{number_format($totalDivisa,3) ?? 0}}</td>
							@endif
							
							<input type="hidden" name="datosPagoFactura[]" value="{{$totalBs}}|{{$cuenta['tasa']}}|{{$totalDivisa}}|{{$cuenta['proveedor_rif']}}|{{$cuenta['documento']}}|{{$cuenta['n_control']}}|{{$cuenta['igtf']}}|{{$cuenta['factura_id']}}">
							

							
						</tr>
						<?php 
							$totalFacturas += $totalBs;
						    $totalFacturasDivisa += $totalDivisa;
						    $sumaDebito=0; 
						    $sumaCredito=0;
							
						    $totalBs=0;
						    $tasaDelDia =$cuenta['tasaDelDia']; 
						  ?>	
					@endforeach
					<tr>
						<td colspan="5"><b>Pendiente Por Cancelar</b></td>
						<td><b>Bs. {{number_format($totalFacturas,2)}}</b></td>
						@if(session('modoPago')<>'bolivares')
							<td></td>
							<td class="text-success"><b>Divisa$ {{number_format($totalFacturasDivisa,3)}}</b></td>
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
				      					<option value="dolares" @if($modoPagoSelect == 'dolares')selected @endif >Dolares</option>
				      					<option value="zelle"  @if($modoPagoSelect == 'zelle')selected @endif >Zelle</option>
				      					<option value="otros">Otros</option>
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
									<label>Fecha de Pago</label>
									<input type="date" name="fecha_pago" class="form-control" >
								</div>
							</div>
							<div class="col">						
								<div class="form-group">
									
									<label>Valor Tasa</label>
									<select class="form-control" name="tipo_tasa" id="tipo_tasa" disabled>
																	
										<option value="{{$cuenta['tasa']}}|tasaFactura" @if(session('modoPago')=='bolivares')selected @endif>Tasa de la Factura {{$cuenta['tasa']}}</option>								
										<option value="{{$cuenta['tasaDelDia']}}|tasaActual" @if(session('modoPago')<>'bolivares')selected @endif>Tasa Actual {{$cuenta['tasaDelDia']}}</option>								
									</select>
									
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
									<input type="hidden" name="total_facturas" value="{{$totalFacturas}}">								
								</div>						
							</div>
							
							
							<div class="col">

								<button type="submit" class="btn btn-primary float-right"><i class="fa fa-plus mx-1" aria-hidden="true"></i>Guardar</button>
							</div>				
						</div>
								
					</div>
				@else
					<a href="{{route('reciboPagoFacturas',$codigo_relacion_pago)}}"><i class="fa fa-print"></i>Reporte de las facturas canceladas {{$codigo_relacion_pago}}</a>	
				@endif <!-- fin if($totalFacturas > 0.00)-->
			@endif	<!-- fin totalFacturas -->
		</form>
	</div>
	@endsection
	@section('js')
	<script type="text/javascript">
		/*funcion que formatea el valor numerico al de moneda*/
        $("#moneda1").on({
            "focus": function (event) {
                $(event.target).select();
            },
        /*    "keyup": function (event) {
                $(event.target).val(function (index, value ) {
                    return value.replace(/\D/g, "")
                                .replace(/([0-9])([0-9]{2})$/, '$1,$2')
                                .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
                });
            }*/
        });


        /*esta funcion habilita y deshabilita los campos segun la operacion seleccionanda en tipo de registro*/
        $( "#Select_id").change(function () {
        	$("#banco_id").prop('disabled', true);
		    $("#referencia_pago").prop('disabled', true);
		  var modopago =  $("#modo_pago").val();
		  var selector = $("#Select_id  option:selected").val();		  
		  
		  
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

		    case "CAN":
		      $("#modo_pago").prop('disabled',false)	
		      if(modopago=='bolivares'){
		      	$("#banco_id").prop('disabled', false);
		      	$("#referencia_pago").prop('disabled', false);
				$("#nfactura_nota").prop('disabled',true);
				$("#nfactura_nota").prop('required',false);
		      	document.getElementById('moneda1').value= <?php echo round($totalFacturas,3)?>;
		      }else{
		      	 document.getElementById('moneda1').value= <?php echo round($totalFacturasDivisa,3)?>;
		      }
		      break;
		  }
		});

		$("#modo_pago").change(function(){
			var modopago = $("#modo_pago  option:selected").val();
			var tipoTasa = $("#tipo_tasa option:selected").val();
			var seleccion = tipoTasa.split('|');
			switch(modopago){
		    
		    case "bolivares":
		      $("#banco_id").prop('disabled', false);
		      $("#referencia_pago").prop('disabled', false);
		      $("#tipo_tasa").prop('disabled',false);
		      $("#tipo_tasa").prop('required', true);
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
		      $("#banco_id").prop('disabled', true);
		      $("#referencia_pago").prop('disabled', true);
		      $("#banco_id").prop('required', false);
		      $("#referencia_pago").prop('required', false);
		      $("#tipo_tasa").prop('disabled',false);
		      $("#tipo_tasa").prop('required',false)		      
		      document.getElementById('moneda1').value= <?php echo round($totalFacturasDivisa,3)?>;
		      break;
		  }
		});

		$("#tipo_tasa").change(function(){
			var tipoTasa = $("#tipo_tasa option:selected").val();
			var seleccion = tipoTasa.split('|');

			switch(seleccion[1]){
				case "tasaFactura":
					document.getElementById('moneda1').value= <?php echo round($totalFacturas,3)?>;
				break;

				case "tasaActual":
					document.getElementById('moneda1').value= <?php echo round($totalFacturasDivisa*$tasaDelDia,3) ?>;
				break;
			}
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