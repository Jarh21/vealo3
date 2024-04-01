@extends('layouts.app')
@section('css')
<style>
    .modal {
  display: none;
  position: fixed;
  z-index: 1;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0,0,0,0.4); /* Fondo oscuro */
}

.modal-content {
  background-color: #fefefe;
  margin: 15% auto;
  padding: 20px;
  border: 1px solid #888;
  width: 80%;
}

.close {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: black;
  text-decoration: none;
  cursor: pointer;
}
</style>
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
        <div class="modal fade"  id="mi-modal">
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
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar progress-bar-striped " role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
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
                
                <th>Fecha</th>
                <th>Proveedor</th>
                <th>Tipo</th>
                <th>Rif Provee</th>
                <th>Factura</th>
                <th>Comprobante</th>
                <th>Retención</th>
                <th>Opciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($retenciones_dat as $retencion)
                <tr @if($retencion->estatus_retencion=='A') style="background:#F95656" @endif>
                    
                    <td>{{$retencion->fecha_docu}}</td>
                    <td style="width: 300px">{{$retencion->nom_retenido}}</td>
                    <td>
                        @if($retencion->tipo_docu=='FA')
                        Factura
                        @endif
                        @if($retencion->tipo_docu=='NC')
                        Nota Credito
                        @endif
                        @if($retencion->tipo_docu=='ND')
                        Nota Debito
                        @endif
                        
                    </td>
                    <td>{{$retencion->rif_retenido}}</td>
                    <td>{{$retencion->documento}}</td>
                    <td>{{$retencion->comprobante}}</td>
                    <td>{{$retencion->iva_retenido}}</td>
                    <td>
                        @if($retencion->estatus_retencion=='N')
                        <a href="{{route('retencion.iva.generar_comprobante',[$retencion->comprobante,$retencion->rif_agente])}}" class='btn btn-secondary btn-sm' title="descargar PDF" target="popup" onClick="window.open(this.href, this.target, 'width=950,height=650,left=100,top=50');   return false;"><i class="fas fa-file-pdf"></i></a>
                        <a href="{{route('retencion.iva.generar_comprobante',[$retencion->comprobante,$retencion->rif_agente,'firma'])}}" class='btn btn-secondary btn-sm' title="descargar PDF con Firma" target="popup" onClick="window.open(this.href, this.target, 'width=950,height=650,left=100,top=50');   return false;"><i class="fas fa-file-pdf"></i>+<i class="fas fa-marker"></i></a>
                        <a href="{{route('retencion.iva.editar_retencion',$retencion->comprobante)}}" class='btn btn-warning btn-sm'><i class="fas fa-edit" title="Editar"></i></a>
                        <enviar-correo-retencion :datos="{comprobante:'{{$retencion->comprobante}}',rifAgente:'{{$retencion->rif_agente}}',correo_enviado:'{{$retencion->correo_enviado}}'}"></enviar-correo-retencion>
                        @else
                            Anulada
                        @endif
                        
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
    function abrirModalEnvioCorreo(){
        $('#mi-modal').modal('show'); // Muestra el modal con fade
        $('.progress-bar').animate({width:'95%'}, 4000); // Cambia 1000 por la duración deseada en milisegundos
    }
    
</script>

@endsection