@extends('layouts.app')

@section('content')
	<div class="container bg-white">
	<div class="row">
		<div class="col-3">
		<img src="{{ asset(session('logo_empresa'))}}" alt="AdminLTE Logo" class="" style="opacity: .8" width="100px">
            <p>{{session('nombre_general_empresa')}}</p>
		</div>
		<div class="col">
			<h3 class="d-inline"></i>Reporte de Bolivares Entregados</h3>
			<h3>Pago De Proveedores</h3>
			<h3>{{session('empresaNombre')}} {{session('empresaRif')}}</h3>
		</div>
		
	</div>
		<div class="d-print-none">			
			<div class="row">
				<div class="col-10">
					<form action="{{route('resulReportePagoBolivares')}}" method="post" id="busca">
						@csrf
						<div class="row">
							<div class="col">
								<label>Fecha Inicio</label>
								<input type="date" name="fechaIni" class="form-control">
							</div>
							<div class="col">
								<label>Fecha Final</label>
								<input type="date" name="fechaFin" class="form-control">
							</div>
							<div class="col">
								<div class=" mt-4">
									<button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-search"></i>Buscar</button>
								</div>
								
							</div>
						</div>
						
						
						
					</form>
				</div>
				<div class="col-2">
					<a href="#" onclick="javascript:window.print();"><i class="fa fa-print"></i>Imprimir</a>
				</div>
			</div>
			
		</div>
		<div class="mt-5">
			@if(isset($pagosBs))
			<table class="table">
				<thead>
					<tr>
						<td>Proveedor</td>
						<td>Fecha</td>
						<td>Factura</td>
						<td>Monto</td>	
					</tr>		
				</thead>
				<tboby>
					@foreach($pagosBs as $pago)
					<tr>
						<td>{{$pago->proveedor_nombre}} {{$pago->proveedor_rif}}</td>
						<td>{{$pago->fecha_pago}}</td>
						<td>{{$pago->documento}}</td>
						<td>{{$pago->monto_bolivares}}</td>
					</tr>
					@endforeach
					
				</tbody>	
			</table>	
			@endif
		</div>
	</div>
@endsection

@section('js')
<script type="text/javascript">
	function enviar_formulario(){
		var buscar = document.getElementById('buscar');
		document.buscar.submit();
	}
</script>
@endsection