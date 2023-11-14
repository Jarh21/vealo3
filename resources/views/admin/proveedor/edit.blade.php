@extends('layouts.app')
@section('content')
<div class="container">
	<h3>Editar Proveedores {{$proveedor->nombre}} <a href="{{url('/proveedor')}}"><button type="button" class="btn btn-warning float-right"><i class="fas fa-chevron-left"></i>Regresar</button></a></h3><hr>
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
		<form action="{{route('proveedor.update',$proveedor->id)}}" method="POST" enctype="multipart/form-data">
			@method('PUT')
			@csrf
		  <div class="form-group">
		    <label for="name">Razon Social</label>
		    <input type="text" class="form-control" name="nombre"  placeholder="Nombre del proveedor" value="{{$proveedor->nombre}}">
		    
		  </div>
		  <div class="form-row">
		  	<div class="col-1">
		  		<label for="rif">RIF</label>
		  	</div>
		  	<div class="col-2">
		  		
			    <select name="tipoRif" id="tipoRif" class="form-control" onchange="llenarCampo();">
			    	<option value="J" @if($cadenaRif[0]=='J') selected @endif>J</option>
			    	<option value="V" @if($cadenaRif[0]=='V') selected @endif>V</option>
			    	<option value="E" @if($cadenaRif[0]=='E') selected @endif>E</option>
			    	<option value="P" @if($cadenaRif[0]=='P') selected @endif>P</option>
			    	<option value="G" @if($cadenaRif[0]=='G') selected @endif>G</option>			    	
			    </select>
		  	</div>-
		    <div class="col">
		    	<input type="text" class="form-control" name="rifNumero" id="rifNumero" value="@if(isset($cadenaRif[1])) {{$cadenaRif[1]}} @endif"  onchange="llenarCampo();" onkeyup="llenarCampo();">
		    </div>-
		    <div class="col-2">
		    	<input type="text" class="form-control" name="rifTerminal" id="rifTerminal" maxlength="1" value="@if(isset($cadenaRif[2])){{$cadenaRif[2]}} @endif" onchange="llenarCampo();" onkeyup="llenarCampo();"> 
		    </div>=
		    <div class="col">
		    	<input type="text" class="form-control" name="rif" id="rif" value="{{$proveedor->rif}}">
		    </div>	    
		  </div>
		  <div class="form-group my-2">
		  	<label>Tipo de proveedor</label>
		  	<select name="tipo_proveedor" class="form-control">
		  		 
		  		<option value="productos" @if($proveedor->tipo_proveedor=='productos') selected @endif>Productos</option>
		  		<option value="servicios" @if($proveedor->tipo_proveedor=='servicios') selected @endif>Servicios</option>
		  		
		  	</select>
		  </div>
		  <div class="form-group my-2">
		  	<label>Tipo de Contribuyente</label>
		  	<select name="tipo_contribuyente" class="form-control">
		  		 
		  		<option value="Natural" @if($proveedor->tipo_contribuyente=='Natural') selected @endif>Natural</option>
		  		<option value="Juridico" @if($proveedor->tipo_contribuyente=='Juridico') selected @endif>Juridico</option>
		  		
		  	</select>
		  </div>
		  <div class="form-group">
		    <label for="direccion">Porcentaje de retención IVA</label>
		    <input type="text" class="form-control" name="porcentaje_retener" placeholder="100,75 ..." value="{{$proveedor->porcentaje_retener}}">
		  </div>
		  <div class="form-group">
		    <label for="direccion">Porcentaje de retención ISLR</label>
		    <input type="text" class="form-control" name="ultimo_porcentaje_retener_islr" placeholder="Ejemplo 3 " value="{{$proveedor->ultimo_porcentaje_retener_islr}}">
		  </div>
		  <div class="form-group">
		    <label for="direccion">Codigo Fiscal</label>
		    <input type="text" class="form-control" name="codigoFiscal" placeholder="001,007 ..." value="{{$proveedor->codigoFiscal}}">
		  </div>
		  <div class="form-group">
		    <label for="direccion">Direccion</label>
		    <textarea class="form-control" id="exampleFormControlTextarea5" rows="4" name="direccion">{{$proveedor->direccion}}</textarea>
		  </div>
		  <div class="card">
			<div class="card-body">
				<h5>Opciones de Cuentas por pagar incluir o excluir al momento de cargar las facturas y relacionar los pagos.</h5><hr>
				<div><input type="checkbox" name="descontar_nota_credito" id="nota_credito" @if($proveedor->descontar_nota_credito == 1)checked @endif><label for="nota_credito">Descontar Nota de creditos</label></div>
				<div><input type="checkbox" name="agregar_nota_debito" id="nota_debito" @if($proveedor->agregar_nota_debito == 1)checked @endif><label for="nota_debito">Agregar Notas de Debito</label></div>
				<div><input type="checkbox" name="agregar_islr" id="agregar_islr" @if($proveedor->agregar_islr == true) checked @endif> <label for="agregar_islr">Agregar ISLR <span class="text-danger">(Nota: para que realice el calculo debe tener registrado el porcentaje de retencion de ISLR.)</span></label> </div>
				<div><input type="checkbox" name="agregar_igtf" id="igtf" @if($proveedor->agregar_igtf == 1)checked @endif><label for="igtf">Agregar Impuesto IGTF por pagos en divisa</label></div>
				<div><label for="">Dias de Credito</label><input type="number" class="form-control w-50" placeholder='ingrese el numero de dias de credito' name="dias_credito" id="dias_credito"></div>
				
				
				
			</div>
		  </div>		  
		 
		  <button type="submit" class="btn btn-primary">Editar</button>
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
		document.getElementById('rif').value = tipoRif+"-"+rifNumero.trim()+"-"+rifTerminal;
		
	}
</script>
@endsection