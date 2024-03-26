@extends('layouts.app')
@section('css')


@endsection
@section('content')
<h4>Listado Retencion IVA <a href="#" data-toggle="modal" data-target="#modalCambioSucursal" class="btn btn-outline-primary my-2">Seleccione sucursal ->{{session('empresaRif')}} {{session('empresaNombre') ?? 'No hay sucursal seleccionada'}}</a> <a href="{{route('retencion.iva.index')}}" class='btn btn-warning btn-sm float-right'>< Regresar</a></h4><hr>
    <div class="card">
        <div class="card-body">
            <form action="{{route('retencion.iva.buscar_retencion')}}" method="post">
                @csrf
                Buscar Retencion <br>
                Comprobante
                <input type="text" name="comprobante" id="" placeholder='Nº de Comprobante'>
                <input type="text" name="proveedor" placeholder='Nombre del proveedor'>
                <input type="date" name="fecha_desde" title='fecha desde'>
                <input type="date" name="fecha_hasta" title='fecha hasta'>
                <input type="text" name="documento" placeholder='Documento ej:1234,555' title="Numero de documentos en caso de ser varios separar con coma(555,444)">
                <button type="submit">Buscar</button>
            </form>
           
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
						<a href="{{route('retencion.iva.seleccion_sucursal',$empresa->rif)}}" class="dropdown-item dropdown-footer">{{$empresa->rif}} {{$empresa->nombre}}</a>
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
        <!-- Modal cargando pagina-->
        <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Enviando Correo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Espere un momento por favor enviando...
                </div>
                
                </div>
            </div>
        </div>
<div>
    <div>
        @if(Session::has('message'))
			<div class="alert {!! Session::get('alert') !!}" id='alerta'>
				<button type="button" class="close" id='cerrarAlerta'aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				{!! Session::get('message') !!}				
			</div>  			
		@endif
    </div>
    <table class='table'>
        <thead>
            <tr>
                
                <td>Fecha</td>
                <td>Proveedor</td>
                <td>Tipo</td>
                <td>Rif Provee</td>
                <td>Factura</td>
                <td>Comprobante</td>
                <td>Opciones</td>
            </tr>
        </thead>
        <tbody>
            @foreach($retenciones as $retencion)
                <tr>
                    
                    <td>{{$retencion->fecha_docu}}</td>
                    <td style="width: 300px">{{$retencion->nom_retenido}}</td>
                    <td>{{$retencion->tipo_docu}}</td>
                    <td>{{$retencion->rif_retenido}}</td>
                    <td>{{$retencion->documento}}</td>
                    <td>{{$retencion->comprobante}}</td>
                    <td>
                        <a href="{{route('retencion.iva.generar_comprobante',[$retencion->comprobante,$retencion->rif_agente])}}" class='btn btn-secondary btn-sm'><i class="fas fa-file-pdf"></i> ver</a>
                        <a href="{{route('retencion.iva.generar_comprobante',[$retencion->comprobante,$retencion->rif_agente,'firma'])}}" class='btn btn-secondary btn-sm'><i class="fas fa-file-pdf"></i> ver+firma</a>
                        <a href="{{route('retencion.iva.editar_retencion',[$retencion->comprobante,$retencion->rif_agente])}}" class='btn btn-warning btn-sm'>Editar</a>
                        <a href="{{route('retencion.iva.envioemail',[$retencion->comprobante,$retencion->rif_agente])}}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#staticBackdrop">Email</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection
@section('js')

<script type="text/javascript">

	$(document).ready(function() {	
		
        $('#retenciones').DataTable({
        scrollY: 500,
        select: true,
        paging: true,
        searching: true,
        ordering:  false
        });			
    	
        $("#cerrarAlerta").click(function(){
			//cerramos el alerta que indica si un archivo fue cargado o no se cargo			
			$("#alerta").hide();
		});

	} );
    
</script>
@endsection