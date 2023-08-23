@extends('layouts.app')

@section('content')
<div class="container-fluid">
	<div class="alert alert-info">
		<h3 class="d-inline"><i class="fab fa-buffer nav-icon text-info mr-2"></i></h3>
		<h3 class="d-inline bg-info rounded w-75">Relacion Pago de Facturas {{session('empresaNombre')}} {{session('empresaRif')}}</h3>
	</div>	<hr>
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
	<form action="{{route('calculoDeDeudasPorFacturas')}}" method="post">
			@csrf
		<div class="pb-5">	
		<table id="articulos" class="table table-bordered " data-page-length='25'>
    		<thead>
    			<tr>    				
    				<th>Nº</th>
    				<!--<th>Empresa</th>-->
    				<th>Proveedor</th>
    				<th>Nº Factura</th>
    				<th>Debito</th>	
    				<th>Fecha Factura</th>
    				<th>Iva</th>
    				<th>Islr</th>    				
    				<th>Credito</th>
    				<th>Fecha Pago</th>    				    				
    				<th>IGTF</th>
					<th>Op</th>
    				
    			</tr>
    		</thead>
    		@if(isset($cuentas))
    		@php $n=1; @endphp   			
    				
    				<tbody>
	    			@foreach($cuentas as $cuenta)
	    			@if($cuenta->concepto=='FAC')

	    			<tr id="tr{{$cuenta->id}}" @if($cuenta->desapartada_pago==1) class="bg-warning" title="Factura sacada de una relacion de pagos." @endif @if($cuenta->banderaFacturaSiaceEncontrada==0) class="bg-danger" title='Esta Factura fue eliminada del sistema Siace' @endif>
	    				<td>
	    					@if($cuenta->is_apartada_pago == 1)
	    						<!-- verificar si la facturqa eta cancelada -->
	    						
	    						<i class="fa fa-calendar-plus" aria-hidden="true" title="Factura apartada para pagos"></i>
	    					@else	
	    					
	    						<input type="checkbox" id="check{{$cuenta->id}}" name="facturasPorPagar[]" onchange="isCheckBoxSeleccionado({{$cuenta->id}});" class="CheckedAK" value="{{$cuenta->id}}">	    						
	    					
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
	    						Error no tiene % retencion IVA
	    					</span>
	    					@else
	    					{{$cuenta->porcentaje_retencion_iva ?? '0'}}%
	    					@endif
	    				</td>
	    				<td>{{$cuenta->documento}}</td>    					    				   				
	    				<td>{{number_format($cuenta->debitos,2,',','.')}}</td>	    				
	    				<td>{{$cuenta->fecha_factura}}</td>
	    				<td>
	    					F.{{$cuenta->montoiva}} 
	    					<span class="text-danger">R.{{$cuenta->retencion_iva}}</span>
	    				</td>
	    				<td>
	    					@if($cuenta->is_apartada_pago == 0)
	    						@if($cuenta->is_retencion_islr == 1)
	    							{{$cuenta->retencion_islr}}
	    						@else
	    							<input type="checkbox" name="islr[]" value="{{$cuenta->id}}">
	    						@endif
	    					@else	    						
	    						{{$cuenta->retencion_islr}}
	    					@endif

	    				</td>	    				
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
	    				<td>{{$cuenta->fecha_real_pago ?? 'no definida'}}</td>
	    				
	    				<td>
	    					@if($cuenta->is_apartada_pago == 1)
	    					 	<input type="checkbox" name="igtf[]" value="{{$cuenta->id}}" @if($cuenta->igtf > 0.00)checked @endif disabled>
	    					@else
	    						<input type="checkbox" name="igtf[]" value="{{$cuenta->id}}">
	    					@endif	
	    				</td>
						<td>
							@if($cuenta->is_apartada_pago==0)
							<a href="{{route('eliminarFacturaPorPagar',[$cuenta->id,'relacionPagoFacturasIndex'])}}" class="text-danger"><i class="fa fa-trash"></i></a>
							@endif
						</td>
	    				
	    			</tr>
	    			@endif
	    			@endforeach

	    			</tbody>		 
    		
    		@endif
    	</table>
    	</div>
		
		<div class="footer fixed-bottom float-right pt-4">
			
				
				<div class="row bg-gradient-light py-2 border-top">
					<div class="col text-right">
						<label for="fecha_real_pago">Dia a Cancelar</label>
					</div>
					<div class="col">
						<input type="date" class="form-control" name="fecha_real_pago" required>
					</div>
					<div class="col">
						<button type="submit" id="enviarFacturas" class="btn btn-primary" disabled>Enviar</button>
					</div>
				</div>
			
			
		</div>

			
		
    	
    </form>
    
</div>
@endsection

@section('js')

<script type="text/javascript">
	$(document).ready(function() {	
		
		$('#articulos').DataTable({
	    
	    select: true,
	    paging: false,
	    searching: true,
		ordering:  true
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
            			document.getElementById("enviarFacturas").disabled = false;
            		}else{
            			document.getElementById("enviarFacturas").disabled = true;
            		}
            		           		
            		//agregamos la clase bg-primary al <tr>
            		document.getElementById(trId).className  += " bg-primary";
            	}else{

            		if(checked >=1){
            			document.getElementById("enviarFacturas").disabled = false;
            		}else{
            			document.getElementById("enviarFacturas").disabled = true;
            		}
            		//al deseleccionar borramos la clase bg-primary            		
            		document.getElementById(trId).className = document.getElementById(trId).className
    .replace(new RegExp('(?:^|\\s)'+ 'bg-primary' + '(?:\\s|$)'), '');
            	}
            	
            } 
            
	</script>


@endsection