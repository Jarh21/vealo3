@extends('layouts.app')
@section('content')
<div class="container">
		<h3><a href="{{route('islr.index')}}">Registro de ISLR </a></h3>
		
		@if ($errors->any())
	    <div class="alert alert-danger">
	        <ul>
	            @foreach ($errors->all() as $error)
	                <li>{{ $error }}</li>
	            @endforeach
	        </ul>
	    </div>
		@endif
		@if(isset($faltaDetalles))
			<div class="alert alert-danger">
		        <ul>
		            {{$faltaDetalles}}
		        </ul>
		    </div>
		@endif
	@foreach($datosRetenciones as $datosRetencion)
		
		<form action="{{route('islr.save2',[$datosRetencion->id,$accion,$encabezadoId ?? '',$fechaIniFin ?? ''])}}" method="post">
			@method('put')
			@csrf
			<div class="form-row">			
				<div class="col-1">
					<label>Empresa</label>			
				</div>
				<div class="col-3"><p>{{$datosRetencion->empresa}}</p></div>
				<div class="col-3"><b>Rif Empresa.&nbsp; </b>{{$datosRetencion->e_rif}}</div>
				<div class="col-3"><p>Nº Control {{$datosRetencion->nControl ?? $contador}}</p></div>
				@if(!isset($datosRetencion->nControl))
					<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#exampleModal3" title="solo se pueden eliminar la ultima retencion y las incompletas ">
					 <i class="fa fa-trash mr-2 fa-2x" aria-hidden="true"></i> Eliminar
					</button>
				@else
					@if($datosRetencion->nControl == $contador-1)
						<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#exampleModal3">
					  	<i class="fa fa-trash mr-2 fa-2x" aria-hidden="true"></i>Eliminar
						</button>
					@endif	
				@endif
			</div>
				
			<div class="form-row">
				
					<div class="col-auto">
					<label>Proveedor</label>
					</div>
					<div class="col-auto">
					<p>{{$datosRetencion->proveedor}} <b>Rif Prov.</b> {{$datosRetencion->p_rif}}</p>
					</div>
				
				
					<div class="col-auto" >	
					<b>Codigo Concepto Proveedor</b>
					<input  style="width: 95px;" class="" type="text" name="proveedor_codfiscal" required 
						
						@if(isset($datosRetencion->proveedor_codfiscal))
						value="{{$datosRetencion->proveedor_codfiscal}}"
						@else
						value="{{$datosRetencion->codigoFiscal}}"
						@endif
						>
						<span class="text-danger">(Solo modificar si es otro concepto)</span> 
					</div>				
					
													
				
			</div>
			<div class="form-row">
					<div class="col-auto">
						<label>Ultimo Porcentaje de Retencion del Proveedor <span class="text-danger">{{$ultimoPorcentajeProveedor ?? 0}}%</span></label>
						
					</div>
				</div>
			<div class="form-row mb-3">
				<div class="col">
					
					<b>Fecha de Retención</b><input type="date" name="fecha" class="form-control" required value="{{$valores['fecha'] ?? date('d-m-Y')}}">
				</div>
				<div class="col">
				
					<b>Serie:</b><input type="text" name="serie" class="form-control" value="{{$valores['serie']}}">
				</div>
				<div class="col">
					
					<b>Nº Cheque/Transferencia</b><input type="text" name="n_egreso_cheque" class="form-control" value="{{$valores['n_egreso_cheque']}}">
				</div>
			</div>	
			<div class="form-row">
				
				
				
			</div>
			<hr>
			<h4>Registre el monto del servicio</h4>

			
			
			<iframe  style="border:0;" src="{{route('islr.montoServicios',[$datosRetencion->id,$ultimoPorcentajeProveedor])}}" width="100%" height="400px"></iframe>
			
			<div class="fixed-bottom d-flex justify-content-end">
								
				<button class="btn btn-primary btn-lg my-3" type="submit" ><i class="fa fa-plus mr-2" aria-hidden="true"></i>Guardar Retención y Finalizar</button>
				
			</div>
			
		</form>
		
		<!-- Modal -->
			<div class="modal fade" id="exampleModal3" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			  <div class="modal-dialog">
			    <div class="modal-content">
			      <div class="modal-header">
			        <h5 class="modal-title" id="exampleModalLabel">Eliminar</h5>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
			      </div>
			      <div class="modal-body">
			       ¿Confirma que desea eliminar los registros seleccionados?
			       
			      </div>
			      @php
			      	$ncontrol=0;
			      	if(isset($datosRetencion->nControl)){
			      		$ncontrol = $datosRetencion->nControl;
			      	}else{
			      		$ncontrol = 0;
			      	}
			      @endphp
			      <div class="modal-footer">
			        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
			        <a href="{{route('islr.delete',[$datosRetencion->id,$ncontrol,$datosRetencion->e_rif])}}"><button class="btn btn-danger">Eliminar</button></a>			        
			        
			      </div>
			    </div>
			  </div>
			</div>	<!--fin modal-->
		
	@endforeach
	<div class="alert alert-ligth">
			<b>Importante:</b>	
			<p  class="text-secondary">
				* Persona JURIDICA: en caso de ser varias facturas se registran cada una de ellas independientes y el sistema calcula el total.</p>
			<p class="text-secondary">	
			* Persona NATURAL:	en caso de ser varias facturas debes sumar previamente todas las facturas y realizar un solo registro, los numero de factura y control van juntos separados por "/" en su respectiva casilla. Ej: Nº factura 1123/1124 , Nº Control 1123/1124.</p>
			<p class="text-danger">* La opción para anular la retencion es eliminando todas las facturas.</p>
			</div>
	</div>
			
	

@endsection