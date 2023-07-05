@extends('layouts.app')

@section('content')

	<div class="container">
		<h3>Reporte Cuentas Pagadas por Banco </h3><hr>
		<div class="alert ">
			<form action="{{route('buscar.reportecuentaspagadas')}}" method="post">
				@csrf
				<div class="row">
					<div class="col">
						<label class="text-secondary ">Empresa</label>
						<select name="empresa_rif" class="form-control" >
				    		<option value="">-- Seleccione Empresa --</option>
				    		@foreach($empresas as $empresa)
				    			<option value="{{$empresa->rif}}|{{$empresa->basedata}}" @if(isset($datosEmpresa)) @if($datosEmpresa[0]==$empresa->rif)selected @endif @endif>{{$empresa->nombre}}
				    			</option>
				    		@endforeach
				    	</select>
					</div>
					<div class="col">
						<label  class="text-secondary">Banco</label>
						<select name="banco_id" id="banco_id" class="form-control">
							<option value="">- Selecciones -</option>
							@foreach($bancos as $banco)
							<option value="{{$banco->id}}" @if(isset($datosBanco)) @if($datosBanco==$banco->id)selected @endif @endif>{{$banco->nombre}}</option>
							@endforeach
						</select>
					</div>
					<div class="col">
						<label  class="text-secondary">Fechas desde</label>
						<input type="date" name="fechaini" class="form-control" value={{$fechaini ?? ''}} />
					</div>
					<div class="col">
						<label  class="text-secondary">Fechas hasta</label>
						<input type="date" name="fechafin" class="form-control" value={{$fechafin ?? ''}} />
					</div>	
						
					<div>
						<button class="btn btn-warning d-print-none mx-2" type="submit" title="Buscar"><i class="fa fa-search"></i></button>
						<button type="button" class="btn btn-success d-print-none float-right" title="Imprimir" onclick="javascript:window.print()">
							<i class="fa fa-print" aria-hidden="true"></i>
						</button>
					</div>
					
				</div>
			</form>
		</div>
		@if(isset($listadoPagos))

		<table id="pagos" data-page-length='25' style="font-size:13px"; class="table">
			<thead>
				<tr>					
					<th>Empresa</th>
					<th>Bancos</th>
					<th>Proveedor</th>
					
					<th>FechaPago</th>
					<th>Facturas</th>
					<th>Montos</th>
					<th>Referencia</th>
				</tr>
			</thead>
			<tbody>
				@foreach($listadoPagos as $pagos)
				<tr>					
					<td>{{$pagos['empresa_rif']}}</td>
					<td>{{$pagos['banco_nombre']}}</td>
					<td>{{$pagos['proveedor_nombre']}}</td>					
					<td>{{$pagos['fecha_pago']}}</td>
					<td>
						
							@foreach($pagos['facturas'] as $factura)
							@if($factura->documento == null)
								{{$pagos['concepto_descripcion']}}
							@else
							{{$factura->documento.','}}
							@endif
							@endforeach
							
					</td>
					<td>{{number_format($pagos['pago'],2,'.',',')}}</td>
					<td>{{$pagos['referencia_pago']}}</td>
				</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
            <th colspan="5" style="text-align:right">Total </th>
            <th></th><td></td>
        </tr>
			</tfoot>
		</table>
		@endif
	</div>

	
@endsection
@section('js')

<!-- <script src="{{asset('js/moment.min.js')}}"></script> -->

<script>
$(function() {
  $('input[name="daterange"]').daterangepicker({
    opens: 'left'
  }, function(start, end, label) {
    console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + 'to' + end.format('YYYY-MM-DD'));
  });
});
</script>
<script type="text/javascript">

	$(document).ready(function() {	
		
		$('#pagos').DataTable({
	   /* scrollY: 400,*/
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
            total = api
                .column( 5 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            pageTotal = api
                .column( 5, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( 5 ).footer() ).html(
                new Intl.NumberFormat("de-DE").format(pageTotal)
            );
            
        },
	    select: true,
	    paging: false,
	    searching: true,
		ordering:  true
		});   	

	} );
</script>



@endsection
