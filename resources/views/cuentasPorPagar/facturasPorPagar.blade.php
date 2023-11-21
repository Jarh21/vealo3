@extends('layouts.app')
@section('css')

<link rel="stylesheet" type="text/css" href="{{asset('css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('css/select2.min.css')}}">
@endsection
@section('content')
	<div class="container-fluid">
		<h4>Cuentas Por Pagar de {{session('empresaNombre')}} {{session('empresaRif')}}<span  class="right badge @if(session('modoPago')<>'bolivares')badge-success @else badge-primary @endif float-right" >Pagos en {{session('modoPago')}}</span></h4>
		<hr>
		@if($importar_server2_a_server1_cxp == 1)
		<div class="float-right">
			<a href="{{route('sincorinzarServidores')}}" class="btn btn-outline-info btn-sm">Optener las ultimas facturas de {{session('empresaNombre')}}</a>
			<a href="{{route('sincorinzarServidoresTodo')}}" class="btn btn-outline-warning btn-sm" title="la ultima actualizacion fue: {{$fecha_actualizacion_servidor_remoto}}">Sincronizar todas las sucursales</a>
		</div>
		@endif
		<div class="mb-3">				
			<a href="{{route('nuevafacturaporpagar.index')}}" class="btn btn-outline-success btn-sm mx-3 ">
			<i class="fas fa-file-invoice-dollar text-success" title="Ingresar Factura Manual"></i>Ingresar Factura Manual
    		</a>
			<a href="{{route('addotrospagos')}}" title="otros pagos" class="btn btn-outline-primary btn-sm"><i class="fas fa-receipt text-primary"></i>Otros Pagos</a>
		</div>
		<div class="mb-3">
    	<form action="{{route('optenerfacturasporpagar')}}" method="POST">
	      	@csrf
			
			<input type="hidden" name="empresa" value="{{session('empresaRif')}}|{{session('basedata')}}">
			<input type="hidden" name="modo_pago" value="{{session('modoPago')}}">
			Facturas del Libro de Compras del Siace.
				<div class="row">
					
					<div class="col-2">
						<div class="form-grup">						
						<input type="text" id="nfactura" name="nfactura" class="form-control-sm d-inline" placeholder="Numero de factura del siace" autofocus title="Numeros de Facturas del Siace">
						</div>						
					</div>
					
					<div class="col-5">
						<div class="form-group">
						
						<select name="proveedorRif" class="js-example-basic-single " style="width: 100%;" title="Seleccionar el proveedor de la facturas del siace" >
							<option value=""></option>
							@if(isset($proveedores))
							@foreach($proveedores as $proveedor)
								<option value="{{$proveedor->rif}}">{{$proveedor->rif}} {{$proveedor->nombre}}</option>		
							@endforeach
							@endif
						</select>
						</div>	
												
					</div>
					
					<div class="col-2">
						<div class="form-group">						
						<input type="number" name="dias_credito" class="form-control-sm" placeholder="Dias de Credito" title="Dias de Credito acordados con el vendedor">
						</div>
						
					</div>
					<div class="col">
						<button type="submit" class="btn btn-primary btn-sm d-inline" title="buscar"><i class="fa fa-search"></i></button>
						<a href="#info1" class="inf mx-2">+ Opciones</a>
					</div>	    				
				</div>
				
				<div id="info1" class="row well oculto">
					<div class="col">
						Observación
						<input type="text" name="observacion" id="observacion" placeholder='Observacion adicional' class='form-control'>
					</div>
					<div class="col-2">
						Descuento	
						<input type="number" name="porcentaje_descuento" class="form-control" placeholder="% porcentaje de descuento acordado">
					</div>
				
					<div class="col">
						Importar el Libro desde
						<input type="date" name="fecha_cierre_ini" class="form-control" >
					</div>
					<div class="col">
						Importar el Libro hasta
						<input type="date" name="fecha_cierre_fin" class="form-control" >
					</div>
					
					
				</div>   
					
		        
		</form>
		</div>
		@if(Session::has('message'))
			<div class="alert alert-danger">
				{!! Session::get('message') !!}
			</div>
   			
		@endif
    	<form action="{{route('vistaPagarFacturas')}}" method="POST" name="formCuentaPorPagar">
    	
    	@if(!empty($mensaje))
    	<div class="alert {{$mensaje['tipo']}}">
    		<h4><i class="fa fa-exclamation-triangle"></i>{{$mensaje['texto']}}</h4>
    	</div>
    	@endif	
    	<table id="articulos" class="table table-bordered" data-page-length='25'>
    		<thead>
    			<tr>    				
    				<th>Nº</th>    				
    				<th>Proveedor</th>
    				<th>Nº Factura</th>
    				<th>Debito</th>
    				<th>Creditos</th>
    				<th>Retencion</th>	
    				<th>Fecha Factura</th>
    				<th>Dias Credito</th>   				
    				  				
    				<th>Todo</th>
    			</tr>
    		</thead>
    		@if(isset($cuentas))
				@php $limitado=0;@endphp
				
				@if(Auth::user()->acceso_resultados=='limitado')
					@php $limitado=1; @endphp
				@else
					@php $limitado=0; @endphp
				
				@endif
				@php 
					$n=1;
					$usuarioActivo = Auth::user()->name; 
				@endphp    		
    			
    				@csrf
    				<tbody>
	    			@foreach($cuentas as $cuenta)
						@if(($limitado==1 and $cuenta->usuario == $usuarioActivo) or $limitado==0)    			
						<tr id="tr{{$cuenta->id}}" @if($cuenta->desapartada_pago==1) class="bg-warning" title="Factura sacada de una relacion de pagos." @endif>
							<td>
								@if(empty($cuenta->codigo_relacion_pago))
									<!-- verificar si la facturqa eta cancelada -->
									@if(isset($cuenta->estatus))
										<b class="text-danger">{{$cuenta->estatus}}</b>
									@else	
										@if(session('modoPago')=='bolivares')
										<input type="checkbox" id="check{{$cuenta->id}}" name="facturasPorPagar[]" onchange="isCheckBoxSeleccionado({{$cuenta->id}})" class="CheckedAK" value="{{$cuenta->id}}">
										@else
											@if($pago_facturas_desde_facturas_por_pagar==1)
											<input type="checkbox" id="check{{$cuenta->id}}" name="facturasPorPagar[]" onchange="isCheckBoxSeleccionado({{$cuenta->id}})" class="CheckedAK" value="{{$cuenta->id}}">
											@endif
										@endif
									@endif
								
								@endif
								{{$n++}}
							</td>   				
							
							<td>
								{{$cuenta->proveedor_nombre}}|{{$cuenta->proveedor_rif}}
								@if(!empty($cuenta->observacion))
								<span class="right badge badge-warning d-print-none">{{$cuenta->observacion}}</span>
								@endif
								@if($cuenta->porcentaje_retencion_iva==0.00) 
								<span  class="right badge badge-warning" >
									<a href="#">Error no tiene % retencion IVA</a>
								</span>
								@else
								{{$cuenta->porcentaje_retencion_iva ?? '0'}}%
								@endif
							</td>
							<td>{{$cuenta->documento}}</td>	    				   				
							<td>{{number_format($cuenta->debitos,2,',','.')}}</td>
							<td>{{number_format($cuenta->creditos,2,',','.')}}</td>
							<td>
								<a class="text-success">{{'islr '.$cuenta->retencion_islr}}</a>
								<a class="text-primary d-block">{{'iva '.$cuenta->retencion_iva}}</a>
							</td>
							<td>Fact{{$cuenta->fecha_factura}} Pago{{$cuenta->fecha_pago}}</td>
							<td>
								@if($cuenta->fecha_pago > date('Y-m-d'))
									@if($cuenta->dias_para_pago >= 15 )
										<span class="right badge badge-success">{{$cuenta->dias_para_pago}} dias Pagar</span>
									@endif
									@if($cuenta->dias_para_pago < 15 and $cuenta->dias_para_pago >= 8)
									<span class="right badge badge-primary">{{$cuenta->dias_para_pago}} dias Pagar</span>
									@endif
									@if($cuenta->dias_para_pago < 8 )
									<span class="right badge badge-warning">{{$cuenta->dias_para_pago}} proximo vencer</span>
									@endif
								@else  					
								
								<span class="right badge badge-danger">{{$cuenta->dias_para_pago}} dias Vencida</span>
								@endif

							</td>	    				
								
							<td>
								@if(empty($cuenta->codigo_relacion_pago))	    					
								<!--<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#ModalEliminar" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></button>--><!--Eliminar-->
									@if($cuenta->is_apartada_pago==0)
									<div class="row" @if($cuenta->is_factura_revisada==true) style="background-color:#CFF5E2" @endif title="Factura Revisada">
									<div class="col">Cre({{$cuenta->dias_credito}})Dias<br>Des({{$cuenta->porcentaje_descuento}})% </div>
									</div>
									<div class="row">
										<div class="col"><a href="{{route('eliminarFacturaPorPagar',[$cuenta->id,'cuentasporpagar.facturasPorPagar'])}}" class="text-danger" title="Eliminar"><i class="fa fa-trash"></i></a></div>
										
										<div class="col">@can('editarFacturasPorPagar')<a href="{{route('editarFacturasPorPagar',[$cuenta->id,'cuentasporpagar.facturasPorPagar'])}}" title="Editar"><i class="fa fa-edit"></i></a>@endcan</div>
									</div>
									
									
									@else
									<span style="font-size:11px">Apartada para pago {{$cuenta->fecha_real_pago}}</span>
									@endif
								@else
									@if($cuenta->pago_efectuado==0)
									<!-- si tienes acceso a relacionar facturas puedes ver los modulos de edicion y desvincular las facturas -->
										@can('relacionPagoFacturasIndex')
											<a href="{{route('verVistaPagarFacturas',$cuenta->codigo_relacion_pago)}}" class="text-success" title="Pago en proceso haga click para terminar">Editar<i class="fa fa-edit"></i></a>
											<a href="{{route('desvincularAsientoCuentasPorPagarBolivares',[$cuenta->id,$cuenta->codigo_relacion_pago]) }}" title="sacar de la relacion de pagos">Sacar<i class="fas fa-external-link-alt"></i></a>
										@else
											<span style="font-size:11px">Apartada para pago {{$cuenta->fecha_real_pago}}</span>
										@endcan	
									@endif		    							
								@endif
							</td>
						</tr>
						@endif
	    			@endforeach
	    			</tbody>		 
    		
    		@endif
    	</table><hr>
    		
		<div class="">
			@if(session('modoPago')=='bolivares')
				<button id="pagarcuentas" class="btn btn-primary my-2" disabled><i class="far fa-file-alt mx-2"></i>Pagar las facturas seleccionadas</button>
			@else
				@if($pago_facturas_desde_facturas_por_pagar==1)
					<button id="pagarcuentas" class="btn btn-primary my-2" disabled><i class="far fa-file-alt mx-2"></i>Pagar las facturas seleccionadas</button>
				@endif
			@endif
		</div>
    		
    	</form>
    	<div><p>Tilde el recuadro de la factura que desee pagar, posteriormente dele click al boton "Pagar la Factura seleccionada" que se encuentra al final </p></div>	
	</div>
	
	
@endsection
@section('js')
	<script type="text/javascript">
		$(function () {
			//data table
			$('#articulos').DataTable({
			scrollY: 400,
			select: true,
			paging: false,
			searching: true,
			ordering:  true,
			language:{
				"search": "Buscar dentro del listado de facturas cargadas al sistema:"			
			}
			
			});
		});
	</script>
	<script type="text/javascript">
		/*function abrirModal(){
			
			$('#exampleModal').modal('show');
			
		}*/
		//mostart y ocultar div
		jQuery(document).ready(function(){
		$(".oculto").hide();              
			$(".inf").click(function(){
				var nodo = $(this).attr("href");  
		
				if ($(nodo).is(":visible")){
					$(nodo).hide();
					return false;
				}else{
				$(".oculto").hide("slow");                             
				$(nodo).fadeToggle("fast");
				return false;
				}
			});
		}); 
	</script>

	<script type="text/javascript">
	


	$(document).ready(function() {
		//hacer focus en el campo nfacturas del modal
		
		$('body').on('shown.bs.modal', '#exampleModal', function () {
    		//$(this).find(":input:not(:button):visible:enabled:not([readonly]):first").focus();
			document.getElementById("nfactura").focus();
		});
		
		// select 2
		$('.js-example-basic-single').select2({			
	    	placeholder: 'Seleccione el proveedor',    	
	    	/* maximumSelectionLength:1, */
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
            		document.getElementById(trId).className  += " bg-primary";
            	}else{

            		if(checked >=1){
            			document.getElementById("pagarcuentas").disabled = false;
            		}else{
            			document.getElementById("pagarcuentas").disabled = true;
            		}
            		//al deseleccionar borramos la clase bg-primary            		
            		document.getElementById(trId).className = document.getElementById(trId).className
    .replace(new RegExp('(?:^|\\s)'+ 'bg-primary' + '(?:\\s|$)'), '');
            	}
            	
            } 
            
	</script>
@endsection
