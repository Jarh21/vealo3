@extends('layouts.app')

<!-- <link rel="stylesheet" type="text/css" href="{{asset('css/daterangepicker.css')}}"> -->

@section('content')
	@php 
		//si monedaBase es nacional s calcula el valor de la divisa dividiendo el monto entre la tasa y si es extranjera se multiplica el monto por el valor de la tasa
		$monedaBase = session('monedaBase'); 
		
	@endphp
<div class="container-fluid mt-5 bg-white">
	<div class="row">
		<div class="col-3">
		<img src="{{ asset(session('logo_empresa'))}}" alt="AdminLTE Logo" class="" style="opacity: .8" width="100px">
            <p>{{session('nombre_general_empresa')}}</p>
		</div>
		<div class="col">
			<h3 class="d-inline"><i class="fa fa-calculator nav-icon mr-2"></i>Relacion Pago de Facturas </h3>
			<h3>{{session('empresaNombre')}} {{session('empresaRif')}}</h3>
		</div>
		
	</div>
	
	<a href="#" onclick="javascript:window.print();" class="float-right d-print-none"><i class="fas fa-print mx-1"></i>Imprimir</a>
	
	<p class="d-print-none">Modo de Pago 
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
	<div class="d-print-none">
		<form action="{{route('seleccionarRangoFechaFacturasCalculadas')}}" method="post">
			@csrf
			<div class="row">
				<div class="col">
					<label>Semana de Facturas relacionadas</label>
					<div class="row">
						<div class="col">
							<label  class="text-secondary">Fechas desde</label>
							<input type="date" name="fechaini" class="form-control" required value={{$fechaini ?? ''}} />
						</div>
						<div class="col">
							<label  class="text-secondary">Fechas hasta</label>
							<input type="date" name="fechafin" class="form-control" required value={{$fechafin ?? ''}} />
						</div>
						<div class="col mt-4">
							<button type="submit" class="btn btn-primary btn-sm" title="Buscar"><i class="fa fa-search"></i></button>
						</div>
						<div class="col">
						<input type="checkbox" id="checkbox" onchange="toggleEtiquetas()"><label for="checkbox">Mostrar Bolivares en pago total del proveedor</label>
						</div>
					</div>
					
					
				</div>
				
			</div>
			
		</form>
	</div>
	<div class="text-center">
		<?php $desde = date('d-m-Y',strtotime($fechaini)); ?>
		<?php $hasta = date('d-m-Y',strtotime($fechafin)); ?>
		@if($desde == '01-01-1970')
		<div class="alert alert-warning my-3"><i class="fas fa-exclamation-circle"></i> Debe seleccionar el rango de fechas en el cual desea trabajar</div>
		@else
		<h4>Relación de Facturas desde {{$desde ?? 'no definida'}} hasta {{$hasta ?? 'no definida'}}</h4>
		@endif
	</div>
	<form action="{{route('vistaPagarFacturas')}}" method="post" name="formCuentaPorPagar">
		@csrf
		
		<table id="articulos" border='1' style="width:100%;" >
			<thead>
				
				<tr>    				
					<th class="d-print-none">N</th>
					<!--<th>Empresa</th>-->
					<th>Proveedor</th>
					<th>N Factura</th>					
					<th>FECH Factura</th>
					<th class="d-print-none">Monto</th>							
					<th class="d-print-none">RetIva</th>
					<th class="d-print-none">RetISLR</th>					
					<th class="d-print-none">Monto Fac.</th>				
					<th class="d-print-none">Monto pagar.</th>				
					<th>Tasa</th>
					<th>IGTF</th>							
					<th>Divisas</th>
					<th>Proveedor</th>													
					<th class="d-print-none">Opciones</th>
					

				</tr>

			</thead>
			@if(isset($fechaFacturas))
			@php
			 $n=1; 
			 $sumaTotalDivisas=0; 
			 $sumaPagoTotalFecha=0; 
			 $descuento=0; 
			 $pagoBolivaresMenosDescuento=0;
			 $sumaNotasDebitos=0;
			 $banderaProveedor=0; 
			 $sumaPorProveedor=0;
			 $sumaPagoMonedaNacional = 0;
			 $proveedor='';
			 $contadorFila=0;
			@endphp		
					
			<tbody>
				@if(isset($fechaFacturas))
				@foreach($fechaFacturas as $fechaFactura)
					
					@foreach($fechaFactura->facturas as $cuenta)  			
						<tr id="tr{{$cuenta->id}}" style="height: 5px;" @if($cuenta->desapartada_pago==1) class="bg-warning" title="Factura sacada de una relacion de pagos anterior." @endif>
							<td class="d-print-none"><!-- NÂº -->
								
								@if(empty($cuenta->codigo_relacion_pago))
									<!-- verificar si la facturqa eta cancelada -->
									@if(isset($cuenta->estatus))
										<b class="text-danger">Pago</b>
									@else								
									<input type="checkbox" id="check{{$cuenta->id}}" name="facturasPorPagar[]" onchange="isCheckBoxSeleccionado({{$cuenta->id}});" class="CheckedAK" value="{{$cuenta->id}}">
									@endif
								
								@endif
								{{$n++}}
							</td>
							
							
							<td><!-- PROVEEDOR -->
								{{$cuenta->proveedor_nombre}}|{{$cuenta->proveedor_rif}}
								@if(!empty($cuenta->observacion))
								<span class="right badge badge-warning d-print-none">{{$cuenta->observacion}}</span>
								@endif
							</td>
							<td>{{$cuenta->documento}}</td><!-- NRO FACTURA -->		    				   				
							<td>{{$cuenta->fecha_factura}}</td>	<!-- FECHA FACTURA -->    				
							<td class="d-print-none">
								{{number_format($cuenta->debitos,2,',','.')}}
								
							</td> <!-- MONTO -->						
							<td class="d-print-none">{{$cuenta->retencion_iva}}</td><!-- RETENCION IVA -->
							<td class="d-print-none">{{$cuenta->retencion_islr}}</td><!-- RETENCION ISLR -->
																
							<td class="d-print-none"><!-- PAGO BOLIVARES -->
								<?php 
									$pagoBolivares = $cuenta->debitos-$cuenta->retencion_iva-$cuenta->retencion_islr;
									if($cuenta->porcentaje_descuento > 0.00){
										$descuento = ($pagoBolivares * $cuenta->porcentaje_descuento)/100;
									}
									$pagoBolivaresMenosDescuento = $pagoBolivares-$descuento;
									
									$monedaSecundaria = 1;
									if(floatval($cuenta->moneda_secundaria) > 0.0){
										$monedaSecundaria = $cuenta->moneda_secundaria;
									}
									//validamos si la moneda usada es nacional o extranjera
									if($monedaBase=='nacional'){
										$pagoTotal = (floatval($cuenta->resto)/$monedaSecundaria);
									}else{
										//en caso que la moneda base sea extranjera
										$pagoTotal = (floatval($cuenta->resto));
										
									}
									
									$sumaPagoTotalFecha += $pagoTotal;
									$sumaNotasDebitos +=floatval($cuenta->ndebAumentoTasa); 

									//revisamos en cada vuelta que las fascturas si pertenecen al mismo proveedor
									//si de ser asi se suman para obtener el total por proveedor
									if($banderaProveedor==0){//si la bandera es 0 estamos iniciado la primera factura
										$proveedor = $cuenta->proveedor_rif;//asignamos el rif del proveedor al comparador
										$sumaPagoMonedaNacional = $sumaPagoMonedaNacional + $pagoBolivaresMenosDescuento;
										$sumaPorProveedor = $sumaPorProveedor +	$pagoTotal;//sumamos el total porque estamos iniciado
										$banderaProveedor=1;//cambiamos la bandera del proveddor porque ya no es el primer registro
										$contadorFila = $contadorFila +1;
									}else{//cuando ya no es el primer registro
										if($proveedor == $cuenta->proveedor_rif){//verificamos si el proximo es del mismo proveedor
											$sumaPagoMonedaNacional = $sumaPagoMonedaNacional + $pagoBolivaresMenosDescuento;
											$sumaPorProveedor = $sumaPorProveedor +	$pagoTotal; //de ser asi sumamos
											$contadorFila = $contadorFila +1;
										}else{  //de lo contrario reniciamos el contador y asignamos el nuevo proveedor para reiniciar la suma
											$sumaPorProveedor=0;
											$sumaPagoMonedaNacional=0;
											$contadorFila=1;
											$proveedor = $cuenta->proveedor_rif;
											$sumaPagoMonedaNacional = $sumaPagoMonedaNacional + $pagoBolivaresMenosDescuento;
											$sumaPorProveedor = $sumaPorProveedor +	$pagoTotal;
										}
									}
								?>
								
								@if($cuenta->porcentaje_descuento > 0.00)
								<span  class="d-print-none">{{number_format($pagoBolivares,2,'.',',').'Bs.'}}</span>
								<span  class="d-print-none">{{'-'.$cuenta->porcentaje_descuento.'%'}}</span>
								<span>{{number_format($pagoBolivaresMenosDescuento,2).'Bs'}}</span>
								@else
								<span>{{number_format($pagoBolivares,2,'.',',').'Bs.'}}</span>
								@endif
								
							</td>
							<td class="d-print-none">{{number_format($cuenta->resto,2)}}</td>						
							<td>{{$cuenta->moneda_secundaria}}</td><!-- TASA -->
							<td>{{number_format($cuenta->igtf,2,'.',',')}}</td><!-- IGTF -->
							<td>{{number_format($pagoTotal,2)}}</td> <!-- pago en Divisas -->

							@if($cuenta->totalFactutrasPorProveedor == $contadorFila)
							<td style="border-top: none; ">{{number_format($sumaPorProveedor,2)}}$<br><span class="etiqueta" hidden> {{number_format($sumaPagoMonedaNacional,2).'Bs'}}</span></td><!--total por proveedor-->
							@else
							<td style="border-top:none;  border-bottom:none"></td>
							@endif

							<td class="d-print-none"><!-- X -->
								@if(empty($cuenta->codigo_relacion_pago))
								<a href="{{route('eliminaFacturaCalculada',$cuenta->id)}}"><i class="fa fa-trash text-danger" aria-hidden="true" title="Sacar de esta relacion de pagos"></i></a>
								@endif
								@if($cuenta->pago_efectuado==0 and !empty($cuenta->codigo_relacion_pago))
	    							<a href="{{route('verVistaPagarFacturas',$cuenta->codigo_relacion_pago)}}"  title="Factura en proceso de pago"><i class="fa fa-edit " aria-hidden="true"></i></a>
	    							<a href="{{route('desvincularAsientoCuentasPorPagar',[$cuenta->id,$cuenta->codigo_relacion_pago])}}"><i class="far fa-calendar-times text-danger" aria-hidden="true" title="Sacar Factura del proceso de pago"></i></a>
	    						@endif
	    						@if($cuenta->pago_efectuado==1)
	    						<a href="{{route('reciboPagoFacturas',$cuenta->codigo_relacion_pago)}}">
	    							<span class="right badge badge-success">Pagado</span>
	    						</a>
								<a href="{{route('verVistaPagarFacturas',$cuenta->codigo_relacion_pago)}}" title="Pago en proceso haga click para terminar"><i class="fa fa-edit"></i></a>	
	    						@endif
								
								@if($cuenta->ndebAumentoTasa > 0.00)								
									<span class="d-print-none" title="+ Nota de debito por diferencia de tasa ">{{'+' .$cuenta->ndebAumentoTasa .'Bs'}}</span>
								@endif
								
							</td>
							
						</tr>
					@endforeach	
					<tr style="height:30px;" class="">
						<?php
						
							$dia = date('D',strtotime($fechaFactura->fechaPagoAcordado));
							switch ($dia) {
						    	case 'Mon':
						    		$dia='Lunes';
						    		break;
						    	case 'Tue':
						    		$dia='Martes';
						    		break;
						    	case 'Wed':
						    		$dia='Miercoles';
						    		break;
						    	case 'Thu':
						    		$dia='Jueves';
						    		break;
						    	case 'Fri':
						    		$dia='Viernes';
						    		break;
						    	case 'Sat':
						    		$dia='Sabado';
						    		break;
						    	case 'Sun':
						    		$dia='Domingo';
						    		break;				    	
						    }
						    $sumaTotalDivisas = $sumaTotalDivisas + $sumaPagoTotalFecha;
						?>
						
						<td colspan="13">
							<b class="float-right">{{$dia}} {{date('d-m-Y',strtotime($fechaFactura->fechaPagoAcordado))}} Pago Total {{number_format($sumaPagoTotalFecha,2)}}</b>
							@if($sumaNotasDebitos > 0.00)
								<p class="float-right text-danger d-print-none">Excedente en Bs por Aumento Tasa {{number_format($sumaNotasDebitos,2).'Bs. '}}</p>
							@endif
						</td>
						<td  style="display: none;"></td>
						<td  style="display: none;"></td>
						<td  style="display: none;"></td>
						<td  style="display: none;"></td>
						<td  style="display: none;"></td>
						<td  style="display: none;"></td>
						<td  style="display: none;"></td>
						<td  style="display: none;"></td>
						<td  style="display: none;"></td>
						<td  style="display: none;"></td>
						<td  style="display: none;"></td>
						<td  style="display: none;"></td>
						<td  style="display: none;"></td>
						
					</tr>
					<!-- reiniciamos contador sumaPagoTotalFecha -->
					@php $sumaPagoTotalFecha=0; $sumaNotasDebitos=0; @endphp
				@endforeach
				@endif
				<!--<tr>
					<td colspan="12"></td>
				</tr>-->
			</tbody>
			<tfoot>
	            <tr>
	                <th colspan="13" style="text-align:right">Total </th>
	               
	            </tr>
    		</tfoot>
					 
	    	@endif
	    </table>
	    <div>
	    
	    <div class="fixed-bottom d-flex justify-content-end">
			<button id="pagarcuentas" class="btn btn-primary my-2 d-print-none" disabled><i class="far fa-file-alt mx-2"></i>Pagar las facturas seleccionadas</button>
		</div>
		</div>
	</form>
</div>
@endsection
@section('js')
	<script type="text/javascript">
		function toggleEtiquetas() {
			var checkbox = document.getElementById("checkbox");
			var etiquetas = document.getElementsByClassName("etiqueta");

			for (var i = 0; i < etiquetas.length; i++) {
				if (checkbox.checked) {
				etiquetas[i].removeAttribute("hidden"); // Mostrar la etiqueta si el checkbox está seleccionado
				} else {
				etiquetas[i].setAttribute("hidden", true); // Ocultar la etiqueta si el checkbox no está seleccionado
				}
			}
		}
	</script>

	<script type="text/javascript">
		$(document).ready(function() {	
			
			$('#articulos').DataTable({
				
		    fixedColumns:   {
            	heightMatch: 'none'
        	},
		    select: true,
		    paging: false,
		    searching: true,
			ordering:  false,
			"footerCallback": function ( row, data, start, end, display ) {
	            var api = this.api(), data;
	 
	            // Remove the formatting to get integer data for summation
	            var intVal = function ( i ) {
	                return typeof i === 'string' ?
	                    i.replace(/[\$,]/g, '')*1 :
	                    typeof i === 'number' ?
	                        i : 0;
	            };
	 
	             // Total Ventas
	        /*    total = api
	                .column( 2 )
	                .data()
	                .reduce( function (a, b) {
	                    return intVal(a) + intVal(b);
	                }, 0 );*/
	 
	            // Total over this page
	            pageTotal = api
	                .column( 11, { page: 'current'} )
	                .data()
	                .reduce( function (a, b) {
	                    return intVal(a) + intVal(b);
	                }, 0 );
	 
	            // Update footer
	            $( api.column( 2 ).footer() ).html(
	                'Suma total en divisa: '+new Intl.NumberFormat("de-DE").format(pageTotal)
	            );
	            
	        },
			});   	

		} );
	</script>

	<script type="text/javascript">
            $(".marcar").click(function() { 
                    $("input[type=checkbox]").prop("checked",true);
            })

            function isCheckBoxSeleccionado(id){
            	var checked = $(".CheckedAK:checked").length;/*optener la longitud de los check*/          	
            	var cuentaId = id;
            	var checkedId = 'check'+cuentaId;
            	var trId ='tr'+cuentaId;
            	if(document.getElementById(checkedId).checked == true){
            		/*contamnos la cantidad de elementos en el arreglo del checkbox 
            		si es mayor a 1 activamos el boton de pagar varias facturas*/
            		if(checked >=1){
            			document.getElementById("pagarcuentas").disabled = false;
            		}else{
            			document.getElementById("pagarcuentas").disabled = true;
            		}
            		           		
            		//agregamos la clase bg-primary al <tr>
            		document.getElementById(trId).className  += " bg-info";
            	}else{

            		if(checked >=1){
            			document.getElementById("pagarcuentas").disabled = false;
            		}else{
            			document.getElementById("pagarcuentas").disabled = true;
            		}
            		//al deseleccionar borramos la clase bg-primary            		
            		document.getElementById(trId).className = document.getElementById(trId).className
    .replace(new RegExp('(?:^|\\s)'+ 'bg-info' + '(?:\\s|$)'), '');
            	}
            	
            } 
            
	</script>

@endsection   
