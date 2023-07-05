@extends('layouts.app')

@section('content')
	<div class="container-fluid">
		<h3>Cuentas Pagadas de {{session('empresaNombre')}} {{session('empresaRif')}}</h3><hr>
		<div class="@if(session('modoPago')<>'bolivares')bg-success @else bg-primary @endif border border-5">
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
			<div class="alert alert-success">
				  		   	
			<div class="form-group ">
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
					<div class="col">
						<label>Nro Factura</label>
						<input type="text" name="n_factura" class="form-control" @if(isset($nFactura)) value="{{$nFactura}}" @endif >
						
					</div>
					<div class="d-flex">
						<button type="submit" class="btn btn-secondary mt-auto p-2">
							<i class="fa fa-search" aria-hidden="true"></i>
							Buscar
						</button>
					</div>
	    		</div>
			</div>
			</div>			
    	</form>    	
    		
    	<table id="articulos" class="table table-bordered" data-page-length='100'>
    		<thead>
    			<tr>    				
    				<th>Nº</th>
    				<th>Empresa</th>
					<th>Tipo Moneda</td>
    				<th>Proveedor</th>
    				<th>Nº Factura</th>
    				<th>Pagos</th>	
    				<th>Fecha Pago</th>
    				<th>Acción</th>
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
	    				<td>{{$cuenta->empresa_rif}}</td>
						<td>
							@if($cuenta->modo_pago == 'bolivares')
							<span class="right badge badge-primary">{{$cuenta->modo_pago}}</stpan>
							@else
							<span class="right badge badge-success">{{$cuenta->modo_pago}}</stpan>
							@endif
						</td>
	    				<td>
							{{$cuenta->proveedor_nombre}}|{{$cuenta->proveedor_rif}}
							@if(!empty($cuenta->observacion))
								<span class="right badge badge-warning d-print-none">{{$cuenta->observacion}}</span>
							@endif
						</td>
	    				<td>{{$cuenta->documento ?? $cuenta->concepto_descripcion}}</td>	    				   				
	    				<td>{{number_format($cuenta->creditos,2,',','.')}}</td>
	    				<td>{{$cuenta->fecha_real_pago}}</td>
	    				<td>
						@can('acceso','relacionPagoFacturasIndex')
	    					<a href="{{route('verVistaPagarFacturas',$cuenta->codigo_relacion_pago)}}" class="btn-success btn-sm" title="Pago en proceso haga click para terminar">Ver</a>
						@endcan	
	    				</td>
	    			</tr>
	    			
	    			@endforeach
	    			</tbody>    		
    		@endif	
    	</table><hr>    		
    	    		
    		<div>{{--$cuentas->links()--}}</div>    		
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
		
	    scrollY: 400,
	    select: true,
	    searching: true,
	    paging: false
		});	
    } );
</script>
@endsection