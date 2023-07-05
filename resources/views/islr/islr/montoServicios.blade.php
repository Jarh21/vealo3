<!DOCTYPE html>
<html>
<head>
	<title></title>
	<!-- Styles -->
    <link href="{{ asset('css/bootstrap4.5.2.css') }}" rel="stylesheet">    
    <link rel="stylesheet" href="{{ asset('fontawesome-free-5.15.3/css/all.min.css')}}">
    <script defer src="{{ asset('fontawesome-free-5.15.3/js/all.min.js')}}"></script>
    <!--<script src="{{asset('js/jquery-3.5.1.min.js')}}"></script>-->
    <!--<script src="{{ asset('dist/js/adminlte.js')}}"></script>-->
</head>
<body>
	<div class="container-fluid">
	
		<form action="{{route('islr.savedetalle',$idRetencion)}}" method="post">
			@method('PUT')
			@csrf
			<div class="row">
			<div class="col">
				<label>Concepto del servicio:</label>
				<input type="text" name="concepto" class="form-control" required value="{{$islrDetalleEditar->concepto ?? ''}}">

			</div>
			<div class="col">
				<label>Fecha Factura</label>
				<input type="date" name="fecha_factura" class="form-control" value="{{$islrDetalleEditar->fecha_factura ?? ''}}">
			</div>
			</div>
			<div class="row">
			<div class="col-2">
				<label>N Factura:</label>
				<input type="text" name="nFactura" class="form-control" required value="{{$islrDetalleEditar->nFactura ?? ''}}">
			</div>
			<div class="col-2">
				<label>N Control:</label>
				<input type="text" name="nControlFactura" class="form-control" required value="{{$islrDetalleEditar->nControl ?? ''}}">
			</div>
			<div class="col-3">
				<label>Monto Base</label>
				<input type="text" name="monto" class="form-control rounded-right" required value="{{$islrDetalleEditar->monto ?? ''}}" id="monedaMontoServicio" >

			</div>
			<div class="col-2">
				<div class="form-group">
					<label>% Reten</label>
					<select name="porcentaje_id" class="form-control rounded-right" required>
						<option value="">-- Seleccine % --</option>
						@foreach($porcentajeRetencion as $porcen)
						<option value="{{$porcen->id}}" @if(isset($islrDetalleEditar) and $islrDetalleEditar->id_porcentaje_retencion== $porcen->id)selected @endif>{{$porcen->procent_retencion}}</option>
						@endforeach
					</select>
					
				</div>
			</div>
			<div class="col">
				<div class="form-group">
					<label>Acción</label>
					@if($datosRetenciones[0]->tipo_contribuyente=='Natural')
						@if(empty($detalleRetenciones))
							<button type="submit" class="btn-success btn-sm">Agregar Factura</button>
						@else
							<br><b class="text-danger">Persona Natural solo 1 registro, seniat toma 1 solo sustraendo por dia</b>
							
						@endif
					@else	
						<br><button type="submit" class="btn-success btn-sm">Agregar Factura</button>
					@endif
				</div>	
			</div>
			@if ($errors->any())		
				<div class="col-3">
				
			    <div class="alert alert-danger">
			        <ul>
			            @foreach ($errors->all() as $error)
			                <li>{{ $error }}</li>
			            @endforeach
			        </ul>
			    </div>
				
				</div>
			@endif
		</div>
		</form>

		<table class="table">
			<thead>
				<tr>
					<td>Factura</td>
					<th>Servicio</th>
					<th>NºFactura</th>
					<th>NºControl</th>
					<th>Monto Base</th>
					<th>%Retencion</th>
					<th>Sustraendo</th>
					<th>Retenido</th>
					<th>Retener</th>
					<th>Acción</th>
				</tr>
			</thead>
			<tbody>
				
			@if(isset($detalleRetenciones))	
			@foreach($detalleRetenciones as $detalleRetencion)
			
				<tr>
					<td>{{date('d-m-Y',strtotime($detalleRetencion->fecha_factura)) ?? ''}}</td>
					<td width="200px">{{$detalleRetencion->concepto}}</td>
					<td>{{$detalleRetencion->nFactura}}</td>
					<td>{{$detalleRetencion->nControl}}</td>
					<td>{{number_format($detalleRetencion->monto,2,',','.')}}</td>
					<td>{{$detalleRetencion->porcentaje_retencion}}</td>
					<td>{{number_format($detalleRetencion->sustraendo,2,',','.')}}</td>
					<td>{{number_format($detalleRetencion->monto_retenido,2,',','.')}}</td>
					<td>{{number_format($detalleRetencion->total_retener,2,',','.')}}</td>
					<td>
						<a href="{{route('islr.detalle.edit',['id'=>$detalleRetencion->id,'idRetencion'=>$idRetencion])}}" class="btn-warning btn-sm" title="Editar la factura y sus montos"><i class="fa fa-pencil-alt" aria-hidden="true"></i></a>
						<a href="{{route('islr.detalle.destroy',['id'=>$detalleRetencion->id,'idRetencion'=>$idRetencion])}}" class="btn-danger btn-sm" title="Eliminar la factura y sus montos"><i class="fa fa-trash" aria-hidden="true"></i></a>
						
					</td>	
				</tr>
			@endforeach
			
			@endif
			</tbody>
				
		</table>
		
		@if(isset($datosRetenciones))
		<table>
			@foreach($datosRetenciones as $datosRetencion)
			<tr>
				<td><h3>Total a retener: {{number_format($datosRetencion->total_retener,2,',','.')}}</h3></td>
			</tr>
			<tr>
				<td>Total letras: {{$datosRetencion->total_letras}}</td>
			</tr>
			@endforeach
		</table>
		@endif
	</div>

	<!-- Modal -->
			<div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
			     
			      <div class="modal-footer">
			        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
			        <a href=""><button class="btn btn-danger">Eliminar</button></a>			        
			        
			      </div>
			    </div>
			  </div>
			</div>	<!--fin modal-->	
</body>
    
</html>
