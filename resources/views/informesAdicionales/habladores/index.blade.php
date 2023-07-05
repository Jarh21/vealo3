@extends('layouts.app')

@section('content')
<div class="container">
<a href="#" data-toggle="modal" data-target="#modalCambioSucursal" class="btn btn-outline-primary my-2">Seleccione sucursal ->{{session('empresaRif')}} {{session('empresaNombre') ?? 'No hay sucursal seleccionada'}}</a>
	<div class="row">
		<div class="col-8">
			<div class="card">
				<div class="card-header">
					Listas de Habladores<a href="{{route('habladores.crearLista')}}" class="btn btn-primary btn-sm float-end float-right"> + Nueva Lista Habladores</a>
				</div>
				<div class="card-body">
					<table class="table">
						<thead>
							<tr>
								<th>lista</th>
								<th class="text-right">Accion</th>
							</tr>
						</thead>
						<tbody>
							@if(isset($listasPersonalizada))
								@foreach($listasPersonalizada as $lista)
								<tr>
									<td >{{$lista->listado}}</td>
									<td class="text-right">
										<a href="{{route('listarHabladores',$lista->listado)}}" class="btn btn-secondary btn-sm">Ver</a>
										<a  onclick="eliminarLista('{{$lista->listado}}')" class="btn btn-danger btn-sm ml-2 text-white">Eliminar</a>
									</td>
								</tr>
								@endforeach
							@endif
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="col">
			<a href="{{url('/informes/habladores/manual')}}">Manual de Ayuda</a>
		</div>
	</div>
<!-- Modal sucursal -->
	<div class="modal fade" id="modalCambioSucursal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
			<h5 class="modal-title" id="exampleModalLabel">Sucursales</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
			</div>
			<div class="modal-body">
			@if(isset($empresas))
				Seleccione la sucursal
				@foreach($empresas as $empresa)
					<!-- <option value="{{--$empresa->rif--}}|{{--$empresa->nombre--}}|{{--$empresa->basedata--}}">{{--$empresa->rif--}} {{--$empresa->nombre--}}</option> -->
					<a href="{{route('seleccionSucursal',$empresa->rif)}}" class="dropdown-item dropdown-footer">{{$empresa->rif}} {{$empresa->nombre}}</a>
					<div class="dropdown-divider"></div>
				@endforeach
			@endif
			</div>
			<div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>				        		        
			
			</div>
		</div>
		</div>
	</div>	<!--fin modal-->	
</div>
@endsection
@section('js')

<script type="text/javascript">
	$(document).ready(function() {
		//hacer focus en el campo nfacturas del modal
				
		//data table
		$('#ventas').DataTable({	    
	    select: true,
	    paging: false,
	    searching: false,
		ordering:  true,
		language:{
			"search": "Buscar dentro del listado:"			
		},
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
	                .column( 10, { page: 'current'} )
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

	function eliminarLista(lista){
		let eliminar = confirm("Desea eliminar la lista "+lista);
		if(eliminar){
			window.location="/vealo/public/informes/eliminar-lista-habladores/"+lista;
		}
	}
</script>
@endsection