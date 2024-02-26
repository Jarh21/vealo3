@extends('layouts.app')

@section('content')
	<div class="container-fluid">
		<h3>Cuentas Pagadas <span class='d-print-none'>de {{session('empresaNombre')}} {{session('empresaRif')}}<span></h3><hr>
		<div class="d-print-none @if(session('modoPago')<>'bolivares')bg-success @else bg-primary @endif border border-5">
			<h4>Pagos en {{session('modoPago')}}</h4>
		</div>    	
		<!-- <ul class="nav nav-tabs">
		  <li class="nav-item">
		    <a class="nav-link " href="{{--route('cuentasporpagar.facturasPorPagar')--}}">Facturas por Pagar</a>
		  </li>
		  <li class="nav-item">
		    <a class="nav-link active" href="{{--route('cuentasporpagar.facturasPagadas')--}}">Facturas Pagadas</a>
		  </li>		  
		</ul> -->

		
		<form action="{{route('cuentaspagadas.buscar')}}" method="POST">
			@csrf
							  		   	
			<div class="form-group d-print-none">
				<div>
					<p>Busqueda segun empresa, fechas, proveedor o numero de factura</p>
				</div>

				<div class="row">
					
					<div class="col">
						<label>Empresa</label>
						<select name="empresa_rif" class="form-control" required>
			    		<option value="">-- Seleccione Empresa --</option>
			    		@foreach($empresas as $empresa)
			    			<option value="{{$empresa->rif}}|{{$empresa->basedata}}" @if(isset($empresaRif)) @if($empresaRif==$empresa->rif)selected @endif @endif>{{$empresa->nombre}}
			    			</option>
			    		@endforeach
			    		</select>
					</div>
					<div class="col">
						<label>Fecha desde</label>
						<input type="date" name="fecha_desde" class="form-control" @if(isset($fechaDesde)) value="{{$fechaDesde}}" @endif >
						
					</div>
					<div class="col">
						<label>Fecha hasta</label>
						<input type="date" name="fecha_hasta" class="form-control" @if(isset($fechaHasta)) value="{{$fechaHasta}}" @endif >
						
					</div>
				</div>
				<div class="row">	
					<div class="col">
						<label>Nro Factura</label>
						<input type="text" name="n_factura" placeholder="Ej. 001520,001522" class="form-control" @if(isset($nFactura)) value="{{$nFactura}}" @endif >
						
					</div>
					<div class="col">
					<label for="">Nombre Proveedor</label>
						<input type="text" name="proveedor" class="form-control">
					</div>
					<div class="d-flex">
						<button type="submit" class="btn btn-secondary mt-auto p-2">
							<i class="fa fa-search" aria-hidden="true"></i>
							Buscar
						</button>
						@if(isset($cuentas))
						<button type="button" class="btn btn-success mt-auto p-2" onclick="javascript:window.print()">
							<i class="fa fa-print" aria-hidden="true"></i>Imprimir
						</button>
						@endif
					</div>
	    		</div>
			</div>
						
    	</form>    	
    	 <h4>{{$empresaDatos->rif ?? 'Seleccione algunos filtros y haga click en buscar para mostrar resultados'}}	{{$empresaDatos->nombre ?? ''}}</h4>
    	<table id="articulos" class="" border=1 data-page-length='100'>
    		<thead>
    			<tr>    				
    				<th>Nº</th>
    				
					<th>Metodo<br>de Pago</td>
    				<th>Proveedor</th>
    				<th>Nº Factura</th>
    				<th>Pagos</th>	
    				<th>Fecha Pago</th>
    				<th class="d-print-none">Acción</th>
    			</tr>
    		</thead>
    		@if(isset($cuentas))
    		@php $n=1; @endphp    		
    			
    				@csrf
    				<tbody>
	    			@foreach($cuentas as $cuenta)    			
	    			<tr id="tr{{$cuenta->id}}">
	    				<td>
	    				
	    					{{$n++}}
	    				</td>	    				
	    				
						<td>
							@if($cuenta->modo_pago == 'bolivares')
							<span class="text-black">{{$cuenta->modo_pago}}</stpan>
							@else
							<span class="text-success">{{$cuenta->modo_pago}}</stpan>
							@endif
						</td>
	    				<td>
							{{$cuenta->proveedor_nombre}}|{{$cuenta->proveedor_rif}}
							@if(!empty($cuenta->observacion))
								<span class="right badge badge-warning d-print-none">{{$cuenta->observacion}}</span>
							@endif
						</td>
	    				<td>{{$cuenta->documento ?? $cuenta->concepto_descripcion}}</td>	    				   				
	    				<td>{{number_format($cuenta->creditos,2)}}</td>
	    				<td>{{$cuenta->fecha_real_pago}}</td>
	    				<td class="d-print-none">
						@can('relacionPagoFacturasIndex')
	    					<a href="{{route('verVistaPagarFacturas',$cuenta->codigo_relacion_pago)}}" class="text-decorative-none" title="Pago en proceso haga click para terminar">Ver</a>
						@endcan	
	    				</td>
	    			</tr>
	    			
	    			@endforeach
	    			</tbody>
					<tfoot>
						<tr>
							<th colspan="5" style="text-align:right">Total </th>
							<th colspan="2"></th>
						</tr>
					</tfoot>    		
    		@endif	
    	</table><hr>    		
    	    		
    		  		
	</div>	
	
@endsection
@section('js')


<script type="text/javascript">
	// select 2
	$(document).ready(function() {
	    $('.js-example-basic-single').select2({
	    	placeholder: 'Seleccione el proveedor',    	
	    	maximumSelectionLength:1,
	    });
	});

	$(document).ready(function() {	
		
		$('#articulos').DataTable({
			fixedColumns:   {
            	heightMatch: 'none'
        	},
			"columnDefs": [
            { "orderable": false, "targets": 0 }
        ],
		"order": [
            [ 5, "desc" ]
        ],
	    select: true,
	    searching: false,
	    paging: false,
		"footerCallback": function ( row, data, start, end, display ) {
	            var api = this.api(), data;
	 
	            // Remove the formatting to get integer data for summation
	            var intVal = function ( i ) {
	                return typeof i === 'string' ?
	                    i.replace(/[\$,]/g, '')*1 :
	                    typeof i === 'number' ?
	                        i : 0;
	            };	 
	        
	            // Total over this page
	            pageTotal = api
	                .column( 4, { page: 'current'} )
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
@endsection