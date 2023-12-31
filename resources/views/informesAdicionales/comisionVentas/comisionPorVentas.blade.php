@extends('layouts.app')

@section('content')
<style>
    .alternar:hover{ background-color:#C1BDBC;cursor: pointer}
</style>
<div class="container-fluid">
<a href="#" data-toggle="modal" data-target="#modalCambioSucursal" class="btn btn-outline-primary my-2 float-right float-end">Seleccione sucursal ->{{session('empresaRif')}} {{session('empresaNombre') ?? 'No hay sucursal seleccionada'}}</a>

<h3>Comisión Por Ventas</h3>
<div class="my-3">
    
    <a href="{{route('empleadosComisionEspecial')}}" >Definir Porcentajes % para el calculo de comisión</a>
</div>

@if(empty(session('empresaRif')))
<div class="alert alert-danger">
<a href="#" data-toggle="modal" data-target="#modalCambioSucursal" class="link-decorative-none"> No hay sucursal seleccionada presione aquí para asignar una</a>
</div>
@endif
<form action="{{route('buscarComisionPorVentas')}}" method='post'>
    <div class="row d-print-none">
   
        @csrf
        <div class="col-4">
            
            <label for="fechaini">Fecha desde</label>
            <input type="date" name="fechaini" id="fechaini" class="form-control" value="{{$fechaini ?? ''}}" required>
        </div>
        <div class="col-4">
            <label for="fechafin">Hasta</label> 
            <input type="date" class="form-control " name="fechafin" id="fechafin" value="{{$fechafin ?? ''}}" required>
            <label for="">Mostar Los Datos</label>
            <input type="radio" name="tipo_resultado" id=""  value="lista" @if(isset($tipoResultado)) @if($tipoResultado=='lista') checked @endif @endif>Lista
            <input type="radio" name="tipo_resultado" checked value="tabla" @if(isset($tipoResultado)) @if($tipoResultado=='tabla') checked @endif @endif>Tabla      
        </div>
            
        <div class="col">
                <button type="submit" class="btn btn-info mt-4 float-heith" onclick="mostrar()">Buscar</button>
            </div>
        </div>
        
    </div>   
</form>
@if(Session::has('message'))
    <div class="alert alert-success">{{Session::get('message')}}</div>
@endif
<div class="d-flex justify-content-center">
    <div class="">
        
        <div id="alerta" style="display: none;" class="spinner-border ml-auto" role="status" aria-hidden="false"></div>
    </div>
</div>
    
    <div class="container-fluid mt-3">
        
        @if(isset($vendedores))
            @if($tipoResultado == 'lista')        
            
                
                @foreach($vendedores as $vendedor)
                <?php $sumaComision=0; $sumaCobrado=0; ?>
                    <!-- Modal -->
                    <div class="modal fade" id="Modal{{$vendedor['codVendedor']}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                
                    <div class="modal-dialog  modal-dialog-scrollable modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="staticBackdropLabel">{{$vendedor['nomVendedor']}}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">

                                @if(!empty($vendedor['ventas']))
                                <table class="table">
                                    <thead>
                                        <tr>
                                            
                                            <th>Cliente</th>
                                            <th>Fecha Cobro</th>                                            
                                            <th>Comprobante</th>
                                            <th>Monto Cobrado</th>
                                            <th title="Porcentaje de Descuento">%Des</th>
                                            <th>Monto Para Comision</th>                                        
                                            <th title="Porcentaje de Comision">%Com</th>
                                            <th>Comision</th>
                                            
                                        
                                        </tr>
                                    </thead>  
                                    <tbody>
                                        @foreach($vendedor['ventas'] as $resultado)
                                            @if(isset($resultado->cliente))
                                            <tr >
                                            
                                                <td>{{$resultado->cliente}}</td>
                                                <td>{{$resultado->fCobro}}</td>                                                
                                                <td>{{$resultado->comprobante}}<span class="badge badge-primary">{{$resultado->npagos}} Registros de pago</span></td>
                                                <td>{{$resultado->montoCobrado}}</td>
                                                <td>
                                                    @if($resultado->porcentajeDescuento > 0)
                                                    <b class="text-danger">-{{$resultado->porcentajeDescuento}}%</b>
                                                    @endif
                                                </td>
                                                <td>{{number_format($resultado->montoParaComision,2)}}</td>                                            
                                                <td>{{$resultado->porcentajeComision}}%</td>
                                                <td>
                                                    {{number_format($resultado->comision,2)}}
                                                    
                                                </td>
                                                
                                            </tr>
                                            <?php 
                                                $sumaComision+= $resultado->comision;
                                                $sumaCobrado+= $resultado->montoParaComision;
                                            ?>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                                @endif
                            </div>
                            <div class="modal-footer">
                            {{'Monto Cobrado '.number_format($sumaCobrado,2)}} {{'| Monto Comision '.number_format($sumaComision,2)}}<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>                               
                            </div>
                        </div>
                    </div>
                </div><!--fin Modal-->
                <div class="row">
                    <div class="col-6">
                    
                        <a >
                        {{$vendedor['codVendedor']}} - {{$vendedor['nomVendedor']}} 
                        </a>
                        
                    </div>
                    <div class="col-3">
                        {{'Monto Cobrado '.number_format($sumaCobrado,2)}}
                    </div>
                    <div col-3>
                        {{'Monto Comision '.number_format($sumaComision,2)}}
                        <a href="#" class="h4"  data-toggle="modal" data-target="#Modal{{$vendedor['codVendedor']}}">
                        <i class="fas fa-search-plus" title="Ver Detalles"></i>
                        </a>
                    </div>
                        
                    
                        
                        
                        
                    </div><hr>
                @endforeach
                
            </div>        
            
            @else <!-- si selecciono tabla -->
                <table class="table" id="ventas">
                    <thead>
                        <tr>
                            <th>Vendedor</th>
                            <th>Cliente</th>
                            <th>Fecha Cobro</th>                            
                            <th>Comprobante</th>
                            <th>Monto Cobrado</th>
                            <th title="Porcentaje de Descuento">%Des</th>
                            <th>Monto Para Comision</th>                                        
                            <th title="Porcentaje de Comision">%Com</th>
                            <th>Comision</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($vendedores as $vendedor)
                        @foreach($vendedor['ventas'] as $resultado)
                            @if(isset($resultado->cliente))
                            <tr>
                                <td>{{$vendedor['nomVendedor']}}</td>
                                <td>{{$resultado->cliente}}</td>
                                <td>{{$resultado->fCobro}}</td>                                
                                <td>{{$resultado->comprobante}}</td>
                                <td>{{$resultado->montoCobrado}}</td>
                                <td>
                                    @if($resultado->porcentajeDescuento > 0)
                                    <b class="text-danger">-{{$resultado->porcentajeDescuento}}%</b>
                                    @endif
                                </td>
                                <td>{{number_format($resultado->montoParaComision,2)}}</td>                                            
                                <td>{{$resultado->porcentajeComision}}%</td>
                                <td>
                                    {{number_format($resultado->comision,2)}}
                                    
                                </td>
                                
                            </tr>
                            
                            @endif
                        @endforeach
                    @endforeach   
                    </tbody>
                    <tfoot>
                        <tr>
                        <th colspan="9" style="text-align:right">Total </th>
                        </tr>
                    </tfoot>

                </table>
            @endif

        @endif
        
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
function mostrar() {
    var x = document.getElementById('alerta');
    if (x.style.display === 'none') {
        x.style.display = 'block';
    } else {
        x.style.display = 'none';
    }
}
</script>
<script type="text/javascript">
	


	$(document).ready(function() {
		//hacer focus en el campo nfacturas del modal
				
		//data table
		$('#ventas').DataTable({	    
	    select: true,
	    paging: false,
	    searching: true,
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
	                .column( 8, { page: 'current'} )
	                .data()
	                .reduce( function (a, b) {
	                    return intVal(a) + intVal(b);
	                }, 0 );
	 
	            // Update footer
	            $( api.column( 2 ).footer() ).html(
	                'Suma total en Comisiones: '+new Intl.NumberFormat("de-DE").format(pageTotal)
	            );
	            
	        },
		
		});   	

	} );
</script>
<script type="text/javascript">
    function eliminarTodaLaLista(fechaini,fechafin){
        const eliminar = confirm("¿Confirma que desea eliminar el listado del sistema vealo para traer informacion actualizada del siace?");
        if(eliminar){
            window.location="/vealo3/public/informes-eliminar-todas-las-comisiones/"+fechaini+"/"+fechafin; 
        }
    }
</script>
@endsection