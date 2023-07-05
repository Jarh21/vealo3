@extends('layouts.app')
@section('content')
	<div class="container">
		<h3>Pagar Varias Cuentas <a href="{{route('cuentasporpagar.facturasPorPagar')}}" class="btn btn-warning float-right">Regresar</a></h3><hr>
		@if(!empty($error))
			<div class="alert alert-danger">
        		<ul>
        			<li>{{$error}}</li>
        		</ul>
        	</div>		
		@endif
		<form action="{{route('pagarvariascuentas.save')}}" method="post">
			@csrf
			<div class="card">
			<div class="card-body">	
			<table class="table">
				<thead>
					<tr>
						<th>Facturas</th>
						<th>Proveedo</th>
						<th>TipoPago</th>
						<th>IVA Factura</th>												
						<th>Excento</th>
						<th>Debitos</th>
						<th>Creditos</th>
					</tr>
				</thead>				
				<tbody>
					<br>

					<input type="hidden" name="empresa_rif" value="{{$empresa[0]->rif ?? ''}}">
					<input type="hidden" name="empresa_nombre" value="{{$empresa[0]->nombre ?? ''}}">
					<input type="hidden" name="proveedor_rif" value="{{$proveedor->rif ?? ''}}">
					<input type="hidden" name="proveedor_nombre" value="{{$cuentas[0]->proveedor_nombre ?? ''}}">				
					<input type="hidden" name="pagadasOPorPagar" value="{{$pagadasOPorPagar ?? ''}}">
					<input type="hidden" name="porcentaje_retencion_iva" value="{{$cuentas[0]->porcentaje_retencion_iva ?? '0'}}">
					
					<h3>{{$empresa[0]->nombre ?? ''}} {{$empresa[0]->rif ?? ''}} </h3>
					<?php $sumDebitos=0; $sumCreditos=0; $resto=0; $sumIva=0; $total=0;?>
					@foreach($cuentas as $cuenta)
					<tr>
						<td>{{$cuenta->documento}}</td>
						<td>
							<a href="#" data-toggle="modal" data-target="#modalProveedor{{$cuenta->id}}" class="text-dark">{{$cuenta->proveedor_nombre}}</a>
							{{$cuenta->proveedor_rif}} {{$cuenta->concepto_descripcion ?? ''}}
							
							@if(isset($cuenta->proveedor_rif))						
	    					<input type="text" name="porcentaje_iva" id="porcentaje_iva" value="{{$cuenta->porcentaje_retencion_iva ?? '0'}}" style="width:60px";>% Iva
	    					@endif
							<!-- Modal -->
							<div class="modal fade" id="modalProveedor{{$cuenta->id}}" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
							  <div class="modal-dialog modal-lg">
							    <div class="modal-content">
							    	<div class="modal-header">
							        <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5>
							        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
							          <span aria-hidden="true">&times;</span>
							        </button>
							      </div>
							      <div class="modal-body">
							      	@if(isset($cuenta->proveedor_rif))
							      	<iframe src="{{route('proveedor.edit',$cuenta->proveedor_rif)}}" width="760px" height="500px"></iframe>
							      	@endif
							      </div>  
							       
							      </div>
							    </div>
							  </div>
							</div>	
							<!--fin modal-->	
						</td>
						<td>{{$cuenta->modo_pago ?? ''}} tasa {{$cuenta->moneda_secundaria ?? ''}}</td>
						<td>{{$cuenta->montoiva ?? 0}}</td>						
						<td>{{$cuenta->excento}}</td>
						<td>{{$cuenta->debitos}}</td>
						<td>{{$cuenta->creditos}}</td>
						<td>
							@if($cuenta->concepto<>'FAC')

							<!-- Modal eliminar Registro-->
							<div class="modal fade" id="exampleModal{{$cuenta->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
							  <div class="modal-dialog">
							    <div class="modal-content">
							      <div class="modal-header">
							        <h5 class="modal-title" id="exampleModalLabel">Eliminar Registro</h5>
							        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
							          <span aria-hidden="true">&times;</span>
							        </button>
							      </div>
							      
							      	@csrf
							      	<div class="modal-body">	      		
							       		
										Confirma eliminar el registro {{$cuenta->proveedor_nombre}}?
							      	</div>
							      	<div class="modal-footer">
							        	<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
							        	        
							      
							      </div>
							    </div>
							  </div>
							</div>	
							<!--fin modal-->


							<button type="button" class="btn btn-danger d-inline " data-toggle="modal" data-target="#exampleModal{{$cuenta->id}}" data-toggle="tooltip" data-placement="top" title="Eliminar Registro">
						 		<i class="fa fa-trash" aria-hidden="true"></i>
							</button>
							@else
								@if(isset($desvincular))
									<a href="{{route('desvincular.cuentasporpagar',[$cuenta->id,$cuentas[0]->codigo_relacion_pago,$empresa[0]->rif])}}" data-toggle="tooltip" data-placement="top" title="Desvincular Factura"><i class="fa fa-window-restore" aria-hidden="true"></i></a>
								@endif							
							@endif
						</td>
						<!--contiene el id de cada factura seleccionada-->
						<input type="hidden" name="id[]" value="{{$cuenta->cuentas_por_pagar_id}}">
						<!--contiene el id de cada factura en cuentas por pagar-->
						

					</tr>
						<?php 
							$sumDebitos +=$cuenta->debitos; 
							$sumCreditos+=$cuenta->creditos;
							$sumIva += $cuenta->montoiva;
						?>					
					@endforeach
					<tr>
						<td colspan="4">Sub Total</td>
						<td>{{number_format($sumDebitos,2,',','.')}}</td>
						<td>{{number_format($sumCreditos,2,',','.')}}</td>
						<td>
							@if(isset($desvincular))
							<button type="button" class="btn btn-danger d-inline " data-toggle="modal" data-target="#ModalDesvincularTodo" data-toggle="tooltip" data-placement="top" title="Eliminar Registro">
						 		<i class="fa fa-trash" aria-hidden="true"></i>Eliminar todo
							</button>								
							@endif
						</td>
					</tr>
					<tr>
						<?php 
							$total= $sumDebitos-$sumCreditos;
							if($tasaMonedaSecundaria>0){
								$divisa = $total/$tasaMonedaSecundaria;
							}else{
								$divisa = 0;
							}
							
						?>
						<td colspan="4">Total</td>
						@if($total<1)
						<td class="alert alert-success">Cancelada</td>
						@else
						<td colspan="3">
							@if($modoPagoSelect <>'bolivares')
							<b class="text-success ">Tasa {{$tasaMonedaSecundaria}} $ </b><b class="">{{number_format($total,2,',','.')}} Bs. </b><b class="text-success ">{{number_format($divisa,2,',','.')}} $</b>
							@else
							{{number_format($total,2,',','.')}}
							@endif
						</td>
						@endif
					</tr>
				</tbody>
			</table>
			</div> <!-- fin cord-body -->
			</div> <!-- fin card -->
			<div class="container-fluid">				
									
				<input type="hidden" name="codigo_relacion" class="form-control" value="{{$cuentas[0]->codigo_relacion_pago}}">
				@if($total >=1)	
				<div class="row">
					<div class="col">
						<div class="form-group">
							<label>Tipo de Registro</label>
							<select name="tipo_registro" class="form-control" id="Select_id">
								<option value="">- Selecciones -</option>
								<option value="NDEB">Nota de Debito</option>
								<option value="RIVA">Retención IVA</option>
								<option value="NCRE">Nota de Credito</option>
								<option value="RISLR">Retención ISLR</option>
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
							<input type="date" name="fecha_pago" class="form-control" value="{{date('Y-m-d')}}">
						</div>
					</div>
					
					<div class="col">						
						<div class="form-group">
							<label>Monto</label>
							<input type="text" name="monto" value="" class="form-control" id="moneda1" onchange="formatoMoneda();" >								
						</div>						
					</div>
					
					<input type="hidden" name="tasa_moneda_secundaria" value="{{$tasaMonedaSecundaria}}">
					<div class="col">

						<button type="submit" class="btn btn-primary float-right"><i class="fa fa-plus mx-1" aria-hidden="true"></i>Guardar</button>
					</div>				
				</div>
				@endif			
			</div>
		</form>
	</div>
	<!-- Modal -->
	<div class="modal fade" id="ModalDesvincularTodo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="exampleModalLabel">Eliminar Registro</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      
	      	@csrf
	      	<div class="modal-body">	      		
	       		
				Confirma eliminar todos los registro relacionados a este pago?
	      	</div>
	      	<div class="modal-footer">
	        	<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
	        <a href="{{route('eliminarTodasPorPagar',[$cuentas[0]->codigo_relacion_pago])}}" class="btn btn-danger">Eliminar</a>		        
	      
	      </div>
	    </div>
	  </div>
	</div>	
	<!--fin modal-->
@endsection
@section('js')
	<script type="text/javascript">
		/*funcion que formatea el valor numerico al de moneda*/
        $("#moneda1").on({
            "focus": function (event) {
                $(event.target).select();
            },
            "keyup": function (event) {
                $(event.target).val(function (index, value ) {
                    return value.replace(/\D/g, "")
                                .replace(/([0-9])([0-9]{2})$/, '$1,$2')
                                .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
                });
            }
        });


        /*esta funcion habilita y deshabilita los campos segun la operacion seleccionanda en tipo de registro*/
        $( "#Select_id").change(function () {
        	$("#banco_id").prop('disabled', true);
		    $("#referencia_pago").prop('disabled', true);
		    $("#modo_pago").prop('disabled',true);
		  var selector = $("#Select_id  option:selected").val();		  
		  var valorIva = document.getElementById('porcentaje_iva').value;
		  var montoIva = <?php echo $sumIva;?>;
		  if(valorIva=='' || valorIva==0 || valorIva>100){
		  	valorIva=100;
		  }
		  var totalIva = (montoIva*valorIva)/100; 
		  switch(selector){
		    case "NDEB":		      
		      $("#banco_id").prop('disabled', true);
		      $("#referencia_pago").prop('disabled', true);
		      break;
		    case "RIVA":
		      $("#banco_id").prop('disabled', true);
		      $("#referencia_pago").prop('disabled', true);
		      document.getElementById('moneda1').value= (new Intl.NumberFormat("de-DE").format(totalIva));
		      break;
		    case "NCRE":
		      $("#banco_id").prop('disabled', true);
		      $("#referencia_pago").prop('disabled', true);
		      break;
		    case "RISLR":
		      $("#banco_id").prop('disabled', true);
		      $("#referencia_pago").prop('disabled', true);
		      break;
		    case "CAN":
		      $("#modo_pago").prop('disabled',false)	
		      
		      break;
		  }
		});

		$("#modo_pago").change(function(){
			var modopago = $("#modo_pago  option:selected").val();
			switch(modopago){
		    
		    case "bolivares":
		      $("#banco_id").prop('disabled', false);
		      $("#referencia_pago").prop('disabled', false);		     
		      document.getElementById('moneda1').value= (new Intl.NumberFormat("de-DE").format(<?php echo $total;?>));
		      break;		    
		    case "dolares":		      	
		      $("#banco_id").prop('disabled', true);
		      $("#referencia_pago").prop('disabled', true);
		      $("#banco_id").prop('required', false);
		      $("#referencia_pago").prop('required', false);
		      document.getElementById('moneda1').value= (new Intl.NumberFormat("de-DE").format(<?php echo $divisa;?>));
		      break;
		  }
		});
    </script>	
@endsection