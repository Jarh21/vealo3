@extends('layouts.app')
@section('content')
    <div>
        <h4>Retencion IVA Generar Archivo TXT. <a href="#" data-toggle="modal" data-target="#modalCambioSucursal" class="btn btn-outline-primary my-2">Seleccione sucursal ->{{session('empresaRif')}} {{session('empresaNombre') ?? 'No hay sucursal seleccionada'}}</a> <a href="{{route('retencion.iva.index')}}" class='btn btn-warning btn-sm float-right'>< Regresar</a></h4>
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
						<a href="{{route('retencion.iva.seleccion_sucursal',[$empresa->rif,'retencion.iva.generarTxt'])}}" class="dropdown-item dropdown-footer">{{$empresa->rif}} {{$empresa->nombre}}</a>
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
        <div class="row">
            <div class="col-6">
                <form action="{{route('retencion.iva.buscarregistrosparatxt')}}" method="post">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <label for="">Seleccione el rando de Fecha</label>
                            <div class="row">
                                <div class="col">
                                    <label for="">Desde</label>
                                    <input type="date" class="form-control" name="fechaini" value="{{$fechaini ?? ''}}">
                                </div>
                                <div class="col">
                                    <label for="">Hasta</label>
                                    <input type="date" class="form-control" name="fechafin" value="{{$fechafin ?? ''}}">
                                </div>
                            </div>
                            <button class="btn btn-primary float-right my-2">Buscar</button>
                            @if(isset($detalleTxt))<a href="{{route('retencion.iva.descargarTxt')}}" class="btn btn-success my-2">Exportar TXT</a>@endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @if(isset($detalleTxt))
        <div>
        <table  id="txt" class="table table-sm">
            <thead>
                <tr>
                    <th>Nº</th>
                    <th>Rif Agente</th>
                    <th>Periodo</th>
                    <th>Fecha Fac</th>
                    <th>Tipo<br> oper</th>
                    <th>Tipo<br> Docu</th>
                    <th>Rif Prov</th>
                    <th>Documento</th>
                    <th>N Control</th>
                    <th>Monto</th>
                    <th>Base</th>
                    <th>Monto <br>IVA</th>
                    <th>Docu<br> Afect</th>
                    <th>Comprobante</th>
                    <th>IVA Retener</th>
                    <th>Alicuota</th>
                    <th>Nº Expe</th>
                </tr>
            </thead>
            <tbody>
                <?php $contador=1; ?>
                @foreach($detalleTxt as $detalle)
                    <tr @if($detalle->tipo_docu!='FA') style="background:#F8F1A2" @endif>
                        <td>{{$contador}}</td>
                        <td>{{$detalle->rif_agente}}</td>
                        <td>{{$detalle->periodo}}</td>
                        <td>{{$detalle->fecha}}</td>
                        <td>{{$detalle->estatus}}</td>
                        <td>{{$detalle->tipo_docu}}</td>
                        <td>{{$detalle->rif_retenido}}</td>
                        <td>{{$detalle->documento}}</td>
                        <td>{{$detalle->control_fact}}</td>
                        <td>{{$detalle->comprasmasiva}}</td>
                        <td>{{$detalle->base_impon}}</td>
                        <td>{{$detalle->iva}}</td>
                        <td>{{$detalle->fact_afectada}}</td>
                        <td>{{$detalle->comprobante}}</td>
                        <td>{{$detalle->iva_retenido}}</td>
                        <td>{{$detalle->porc_alic}}</td>
                        <td>0</td>
                        <?php $contador++; ?>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="text-center"><a href="{{route('retencion.iva.descargarTxt')}}" class="btn btn-success my-2">Exportar TXT</a></div>
        </div>
        
        @endif
        
    </div>
@endsection
@section('js')
<script type="text/javascript">
    $(document).ready(function() {	
		
        $('#txt').DataTable({
        
        select: false,
        paging: false,
        searching: true,
        ordering:  false
        });
        
        

    } );
    </script>
@endsection