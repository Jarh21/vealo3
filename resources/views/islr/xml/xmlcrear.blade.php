@extends('layouts.app')
@section('content')
<div class="container">
	<h3>Crear retgistros para el XML <a href="{{route('islr.xml.listar')}}" class="btn btn-warning float-right">Regresar</a></h3><hr>
	<div  class="row">
		<div class="col-6">
			<form action="{{route('islr.xmlCrear')}}" method="POST">
			@csrf

				@if ($errors->any())
				    <div class="alert alert-danger">
				        <ul>
				            @foreach ($errors->all() as $error)
				                <li>{{ $error }}</li>
				            @endforeach
				        </ul>
				    </div>
				@endif
				
				@if (isset($errorFecha))
					<div class="alert alert-danger">
						<ul>
							<li>{{$errorFecha}}</li>
						</ul>
					</div>
				@endif
				<label>Empresa</label>
				<select name="empresaRif" class="form-control" required>
					<option value="">--Seleccione--</option>
					@foreach($empresas as $empresa)
					<option value="{{$empresa->rif}}">{{$empresa->nombre}}</option>
					@endforeach
				</select>
			
				<label for="name">Fecha Inicio</label>
				<input type="date" name="fechaIni" class="form-control" value="{{old('fechaIni')}}" required>
			
				<label for="name">Fecha Fin</label>
				<input type="date" name="fechaFin" class="form-control" value="{{old('fechaFin')}}" required>	
				<label>Observación</label>
				<input type="text" name="observacion" class="form-control">
				<button type="submit" class="btn btn-success my-3">Crear</button>
				
			</form>
		</div>

	</div>
	@if(isset($xml))
		<h2>{{$xml[0]->nombre_empresa}}</h2>
		<p>Ultimo dia del mes: {{$xml[0]->fecha}}</p>
		<table class="table table-hover table-striped">
		  <thead>
		    <tr>
		      <th scope="col">Nº</th>
		      <th scope="col">RIF</th>
		      <th scope="col">Proveedor</th>
		      <th scope="col">NºFactura</th>
		      <th scope="col">NºControl</th>
		      <th scope="col">Fecha</th>
		      <th scope="col">Cod Concepto</th>		      
		      <th scope="col">Monto</th>
		      <th scope="col">% Retención</th>
		      <th scope="col">Accion</th>
		    </tr>
		  </thead>
		  <tbody>
		  	<?php $nControl=1;//inicializar el contador ?>
		  	@foreach($xml as $islr)
		    <tr>
		    	<th scope="row">{{$nControl}}</th>	
		    	<?php 
		    		$nControl++;
		    		$fecha = date('d-m-Y',strtotime($xml[0]->fecha));
		    		//si no existe numero de factura colocar 0
		    		if(isset($islr->num_factura)){
		    			$nFactura = $islr->num_factura;
		    		}else{
		    			$nFactura=0;
		    		}
		    		//si no existe numero de control colocar 0
		    		if(isset($islr->num_control)){
		    			$nControlFactura = $islr->num_control;
		    		}else{
		    			$nControlFactura=0;
		    		}

		    		//si no existe % de retencion se coloca 0
		    		if(isset($islr->porcentaje_retencion)){
		    			$porcentaje_retencion = $islr->porcentaje_retencion;
		    		}else{
		    			$porcentaje_retencion=0;
		    		}

		    		//dar formato de moneda al monto
		    		$monto_retenido= number_format($islr->monto_operacion,2,',','.');
		    		
		    	?>
		      <form action="{{route('xml.update',[$islr->id,$islr->rif_empresa,$islr->fecha,$islr->encabezado_id])}}" method="POST">
		      	@method('put')
		      	@csrf
		      <td><div style="width: 95px;">{{$islr->rif_retenido}}</div></td>		      
		      <td><p style="font-size: 13px;">{{$islr->nombre_retenido}}</p></td>		     
		      <td><input type="text" name="num_factura" value="{{$nFactura}}" class="w-100"></td>
		      <td><input type="text" name="num_control" value="{{$nControlFactura}}"class="w-100"></td>
		      <td>{{$fecha}}</td>		      
		      <td><input type="text" name="codigo_servicio" value="{{$islr->codigo_servicio}}"class="w-50"></td>		      
		      <td><input type="text" name="monto_operacion" value="{{$monto_retenido}}"class="w-10"></td>
		      <td><input type="text" name="porcentaje_retencion" value="{{$porcentaje_retencion}}"class="w-50"></td>		     
		      <td><button type="submit" class="btn btn-primary btn-sm btn-block">Actualizar</button></td>
		      </form>		      
		    </tr>		    
		    @endforeach
		  </tbody>
		</table>
		<a href="{{route('islr.descargarXml')}}" class="btn btn-success">Descargar XML</a>
		<a href="{{route('islr.export.excel')}}">Excel</a>
	@endif
</div>
@endsection