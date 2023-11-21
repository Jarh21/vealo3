@extends('layouts.app')
@section('content')
	<div class="container">
		<h3>Reporte Operaciones Divisas Recaudo SIACE</h3>
		<form class="text-left" action="{{route('buscar.reporte.recaudo.movpagos')}}" method="post">
	    	@csrf
	        <div class="form-row">
	            <div class="form-group col-6">
	            	<label>Empresa</label>
	            	<select name="conexion" class="form-control" required>
	            		<option value="">-- Seleccione Empresa --</option>
	            		@foreach($empresas as $empresa)
	            			<option value="{{$empresa->basedata}}" @if(isset($datosEmpresa)) @if($datosEmpresa[0]->conexion==$empresa->basedata)selected @endif @endif>{{$empresa->nombre}}
	            			</option>
	            		@endforeach
	            	</select>
	                <label for="fecha" class="font-weight-bolder">Fecha a buscar</label>
	                <input type="date" required class="form-control" value="{{$fecha ?? date('Y-m-d')}}" id="fecha" name="fecha">
	            </div>
			</div>
			<button type="submit" class="btn btn-primary">Buscar</button>
		</form>
		@if(isset($recaudos))
		<h3>{{--$datosEmpresa[0]->nombre_empresa--}}</h3>
		<table class="table" id="recaudos">
			<thead>
				<th>Fecha</th>
				<th>codusua</th>
				<th>Usuario</th>
				<th>Dolares</th>
				<th>Tasa</th>
				<th>Bolivares</th>
				<th>Cod Arqueo</th>
			</thead>
			<tbody>
				@foreach($recaudos as $recaudo)
				<tr>
					@foreach($recaudo as $datos)
						<td>{{$datos}}</td>
					@endforeach
					
				</tr>
				
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<th colspan="3" style="text-align:right"></th>
					<th></th><th></th><th></th><th></th>
				</tr>
			</tfoot>
		</table>
		@endif
	</div>
@endsection
@section('js')
<script type="text/javascript">
	$(document).ready(function() {	
		
		$('#recaudos').DataTable({
			fixedColumns:   {
            	heightMatch: 'none'
        	},
			"columnDefs": [
            { "orderable": false, "targets": 0 }
        ],
		"order": [
            [ 6, "desc" ]
        ],
	    select: true,
	    searching: true,
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
	                .column( 3, { page: 'current'} )
	                .data()
	                .reduce( function (a, b) {
	                    return intVal(a) + intVal(b);
	                }, 0 );

					
				// Total over this page
	            pageBs = api
	                .column( 5, { page: 'current'} )
	                .data()
	                .reduce( function (a, b) {
	                    return intVal(a) + intVal(b);
	                }, 0 );
					
	            // Update footer
				$( api.column( 3 ).footer() ).html(
	                'Divisas: '+new Intl.NumberFormat("de-DE").format(pageTotal)
	            );
	            $( api.column( 5 ).footer() ).html(
	                'Bolivares: '+new Intl.NumberFormat("de-DE").format(pageBs)
	            );
	        },
		});	
    } );
</script>	
@endsection