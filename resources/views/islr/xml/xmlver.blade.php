@extends('layouts.app')
@section('content')
<div class="container">
	<h3>Retgistros para el XML 
		<a href="{{route('islr.xml.listar')}}" class="btn btn-warning float-right"><i class="fas fa-chevron-left"></i>Regresar</a>
		
	</h3>
	<hr>
	
	@if(isset($xml))
		<h2>{{$xml[0]->nombre_empresa ?? ''}}</h2>
		<p>Ultimo dia del mes: {{$xml[0]->fecha ?? ''}}</p>
		
		@if($datosEncabezado->activo==1)
			<p class="text-danger">NOTA: puede modificar y eliminar registros de este archivo y este se actualizara automaticamente, pero si inserta nuevas retenciones o empleados no se reflejaran, debera actualizar el  archivo xml. Si desea actualizarlo haga click en el boton Actualizar XML este realizará una copia del archivo actual y generara uno nuevo.</p>
			<button type="button" class="btn-primary btn-sm" data-toggle="modal" data-target="#exampleModal">
				Actualizar XML
			</button>
		@endif	
		<?php 
			$rif=str_replace("-", "", $xml[0]->rif_empresa ?? '');
			$periodo=date('Ym',strtotime($xml[0]->fecha ?? ''));
		?>
		@if(!empty($aviso))<div class="bg-success"><b class=" text-center">{{$aviso}}</b></div>@endif
		<table border="1">
		  <thead>		  	
		  	<tr bgcolor="#bbc1c3">
		  		<td></td>
		  		<td>A</td>
		  		<td>B</td>
		  		<td>C</td>
		  		<td>D</td>
		  		<td>E</td>
		  		<td>F</td>
		  		<td>G</td>
		  		<td>H</td>
		  		<td>I</td>
		  		<td>J</td>
		  		
		  	</tr>
		  	<tr>
		  		<td bgcolor="#cfcfcf">1</td>
		  		<td colspan="6">Data para Crear archivo XML de retISRLSalariosOtras</td>
		  		<td><b class="text-primary">RIF Agente :</b></td>
		  		<td><span class="text-danger">{{$rif}}</span></td>
		  		<td></td>
		  		<td></td>
		  		
		  	</tr>
		  	<tr>
		  		<td bgcolor="#cfcfcf">2</td>
		  		<td colspan="3">RUTA SALIDA DE ARCHIVO XML:</td>
		  		<td colspan="3">Escritorio</td>
		  		<td><b class="text-primary">Periodo:</b></td>
		  		<td><span class="text-danger">{{$periodo}}</span></td>
		  		<td></td>
		  		<td></td>
		  		
		  	</tr>
		  	
		    <tr>
		      <td bgcolor="#cfcfcf">3</td>		      
		      <td bgcolor="#cfcfcf" rowspan="2">ID - Sec.</td>
		      <td bgcolor="#cfcfcf" rowspan="2">RIF</td>		      
		      <td bgcolor="#cfcfcf" rowspan="2">NºFactura</td>
		      <td bgcolor="#cfcfcf" rowspan="2">NºControl</td>
		      <td bgcolor="#cfcfcf" rowspan="2">Fecha Cierre</td>		      
		      <td bgcolor="#cfcfcf" rowspan="2">Cod Concepto</td>		      
		      <td bgcolor="#cfcfcf" rowspan="2">Monto operacion</td>
		      <td bgcolor="#cfcfcf" rowspan="2">Porcentaje Retención</td>
		      <td bgcolor="#cfcfcf" rowspan="2">A Retener</td>
		      <td bgcolor="#cfcfcf" rowspan="2">Nombre Proveedor</td>
		      
		    </tr>

		    <tr><td bgcolor="#cfcfcf">4</td></tr>
		  </thead>
		  <tbody>
		  	<?php $nControl=5; $id=1; //inicializar el contador ?>
		  	@foreach($xml as $islr)
		    <tr>
		    	<td scope="row" bgcolor="#cfcfcf">{{$nControl}}</td>
		    	<th scope="row">{{$id}}</th>	
		    	<?php 
		    		$nControl++;
		    		$id++;
		    		$fecha = date('d/m/Y',strtotime($islr->fecha));
		    		//si no existe numero de factura colocar 0

		    		if(isset($islr->num_factura)){

		    			$nFactura = $islr->num_factura;
		    			$nFactura = str_replace("-", "", $nFactura);
		    		}else{
		    			$nFactura=0;
		    		}
		    		//si no existe numero de control colocar 0
		    		if(isset($islr->num_control)){
		    			$nControlFactura = $islr->num_control;
		    			$nControlFactura = str_replace("-", "", $nControlFactura);
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
		    		$retener = number_format($islr->total_retener,2,',','.');
		    	?>
		     
		      <td width="100px">
		      	{{$islr->rif_retenido}}
		      </td>	      
		      		     
		      <td width="110px">{{$nFactura}}</td>
		      <td width="110px">{{$nControlFactura}}</td>
		      <td style="width: 100px"  class="text-center">{{$fecha}}</td>	      
		      <td  @if($islr->codigo_servicio=='000') class="bg-danger" @endif>{{$islr->codigo_servicio}}</td>		      
		      <td class="text-right"><b class="text-right">{{$monto_retenido}}</b></td>
		      <td width="80px"  class="text-right">{{$porcentaje_retencion}}</td>
		      <td  class="text-right">{{$retener}}</td>		
		      <td >
		      	@if($islr->islr_o_rrhh === 'islr')
		      		<a href="{{route('islr.edit',[$islr->islr_y_rrhh_id,'xml',$encabezadoId,$fechaIniFin])}}" ><p style="font-size: 13px;">{{$islr->nombre_retenido}}</p></a>
		      	@endif
		      	@if($islr->islr_o_rrhh === 'rrhh')
		      		<a href="{{route('rrhh.edit',[$islr->islr_y_rrhh_id,$xml[0]->nombre_empresa,'xml',$encabezadoId,$fechaIniFin])}}" ><p style="font-size: 13px;">{{$islr->nombre_retenido}}</p></a>
		      	@endif
		      	@if($islr->islr_o_rrhh === 'gerente')
		      		<a href="{{route('declarantes.edit',[$islr->islr_y_rrhh_id,'xml',$encabezadoId,$fechaIniFin])}}" ><p style="font-size: 13px;">{{$islr->nombre_retenido}}</p></a>
		      	@endif
		      	</td>   
		      
		      		      		      
		    </tr>
		      				    
		    @endforeach
		    <tr>
		    	<td colspan="7"><h4>Total Monto Base</h4></td>
		    	<td>{{$total}}</td>
		    	<td><b class="text-danger">Total a Retener</b></td>
		    	<td><b class="text-danger">{{$total_retener}}</b></td>
		    </tr>
			</tbody>
		</table>
		
		@if(!isset($error))
		   <a href="{{route('islr.descargarXml',[$rif,$periodo])}}" class="btn btn-success">Descargar XML</a>
		@else
			@can('acceso','xml.update')
			<a href="{{route('islr.descargarXml',[$rif,$periodo])}}" class="btn btn-success">Descargar XML</a>
			@endcan
		@endif
		
		<a href="{{route('islr.export.excel')}}"></a>
	@endif

	<!-- Modal -->
	<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header btn-primary">
	        <h5 class="modal-title" id="exampleModalLabel">Actualizar</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	       ¿Confirma que desea actualizar este archivo XML y generar una copias de los registros anteriores?
	      
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
	        <a href="{{route('xml.new',$encabezadoId)}}"><button class="btn btn-primary">Actualizar</button></a>			        
	        
	      </div>
	    </div>
	  </div>
	</div>	<!--fin modal-->

</div>
@endsection