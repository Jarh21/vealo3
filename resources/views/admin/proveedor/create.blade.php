
@extends('layouts.app')
@section('content')

<div class="container">
	<h3>Registro de Proveedores <a href="{{route('proveedor.index')}}" class="btn btn-warning float-right"><i class="fas fa-chevron-left"></i>Regresar</a></h3>
	@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
	@endif
	<div class="row">
		<div class="col-sm">
		<form action="{{route('proveedor.save')}}" method="POST" enctype="multipart/form-data">
			@csrf
			<a href="http://contribuyente.seniat.gob.ve/BuscaRif/BuscaRif.jsp">Consultar Rif en el SENIAT</a>
		  <div class="form-group">
		    <label for="name">Razon Social</label>
		    <input type="text" class="form-control" name="nombre"  placeholder="Nombre del proveedor" value="">
		    
		  </div>
		  <div class="form-row">
		  	<div class="col-1">
		  		<label for="rif">RIF</label>
		  	</div>
		  	<div class="col-2">
		  		
			    <select name="tipoRif" id="tipoRif" class="form-control"  onchange="llenarCampo();">
			    	<option value="J">J</option>
			    	<option value="V">V</option>
			    	<option value="E">E</option>
			    	<option value="P">P</option>
			    	<option value="G">G</option>
			    </select>
		  	</div>-
		    <div class="col">
		    	<input type="text" class="form-control" name="rifNumero" id="rifNumero" placeholder="00000000"  onchange="llenarCampo();" onkeyup="llenarCampo();" maxlength="9">
		    </div>-
		    <div class="col-2">
		    	<input type="text" class="form-control" name="rifTerminal" id="rifTerminal" maxlength="1" placeholder="0" onchange="llenarCampo();"  onkeyup="llenarCampo();">
		    	
		    </div>
		    <div class="col">
		    	<input type="text" class="form-control" readonly name="rif" id="rif">
		    </div>	    
		  </div>
		  <div class="form-group my-2">
		  	<label>Tipo de Proveedor</label>
		  	<select name="tipo_proveedor" class="form-control" required>
		  		<option value="">-- Selecciones --</option>
		  		<option value="productos">Productos</option>
		  		<option value="servicios">Servicios</option>
		  	</select>
		  </div>
		  <div class="form-group">
		    <label for="direccion">Codigo Del Servicio</label>
		    <input type="text" class="form-control" name="codigoFiscal" placeholder="001,007 ...">
		  </div>
		  <div class="form-group my-2">
		  	<label>Tipo de Contribuyente</label>
		  	<select name="tipo_contribuyente" class="form-control">
		  		<option value="Natural">Natural</option>
		  		<option value="Juridico">Juridico</option>
		  	</select>
		  </div>
		  <div class="form-group">
		    <label for="direccion">Porcentaje de retención IVA</label>
		  <!--   <input type="text" class="form-control" name="porcentaje_retener" placeholder="100,70 ..."> -->
			<select name="porcentaje_retener" class="form-control">
				<option value="">-- Seleccione --</option>
				@foreach($porceRetencionIva as $porce)
					<option value="{{$porce->porcentaje}}">{{$porce->porcentaje}}</option>
				@endforeach	
			</select>
		  </div>
		  <div class="form-group">
			<label for="">Correo Electronico</label>
			<input type="email" name="correo" id="" class="form-control">
		  </div>
		  <div class="form-group">
		    <label for="direccion">Dirección</label>
		    <textarea class="form-control" id="exampleFormControlTextarea5" rows="4" name="direccion" placeholder="Direccion del proveedor"></textarea>
		  </div>		  
		 
		  <button type="submit" class="btn btn-primary">Guardar</button>
		  <button type="reset" class="btn btn-danger">Borrar</button>
		</form>
	</div>
	</div>
</div>


@endsection
@section('js')
<script type="text/javascript">
	function llenarCampo(){
		var tipoRif = document.querySelector('#tipoRif').value;
		var rifNumero = document.querySelector('#rifNumero').value;
		var rifTerminal = document.querySelector('#rifTerminal').value;
		document.getElementById('rif').value = tipoRif+"-"+rifNumero+"-"+rifTerminal;
	}
</script>
@endsection
