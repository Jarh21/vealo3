@extends('layouts.app')

@section('content')
	<div class="container">
		<h3>Registro Manual de Factura en Cuentas Por Pagar <a href="{{route('cuentasporpagar.facturasPorPagar')}}" class="btn btn-warning float-right"> <i class="fa fa-reply" aria-hidden="true"></i>Regresar</a></h3><hr>
		<h4>{{session('empresaNombre')}} {{session('empresaRif')}} </h4>
		<h5>Tipo de Moneda para el pago <span class="text-success">{{session('modoPago')}}</span></h5>
		@if(Session::has('message'))
			<div class="alert alert-danger">
				{!! Session::get('message') !!}
			</div>
   			
		@endif

			<form action="{{route('nuevafacturaporpagar.save')}}" method="post">
				<div class="col">
					@csrf					

			<!--		<div class="form-group">
						<label>Empresa</label>
						<select name="empresa_rif" class="form-control" required>
			    		<option value="">-- Seleccione Empresa --</option>
			    		@foreach($empresas as $empresa)
			    			<option value="{{$empresa->rif}}|{{$empresa->basedata}}" @if(isset($datosEmpresa)) @if($datosEmpresa[0]==$empresa->rif)selected @endif @endif>{{$empresa->nombre}}
			    			</option>
			    		@endforeach
			    		</select>

					</div>-->
					<div class="row my-2">
						<div class="col-3">
							<label>Nº Factura</label>
							<input type="text" name="documento" class="form-control" required value="{{old('documento')}}">
						</div>
						<div class="col-3">
							<label>Nº Control</label>
							<input type="text" name="n_control" class="form-control" required value="{{old('n_control')}}">
						</div>
						<div class="col-3">
							<label>Fecha Factura</label>
							<input type="date" name="fecha_factura" class="form-control" required value="{{old('cierre')}}">
						</div>
					</div>
					<div class="row my-3">
						<div class="col">
							<label>Proveedor</label>
							<select class="js-example-basic-single" name="proveedor" multiple="multiple">
								@foreach($proveedores as $proveedor)
								<option value="{{$proveedor->rif}}|{{$proveedor->nombre}}">{{$proveedor->rif}} {{$proveedor->nombre}}</option>				
								@endforeach				
							</select>
							
						</div>						
						
					</div>
					<div class="row">
						<div class="col mb-2">
							<label for="">Observación</label>
							<input type="text"class="form-control" name="observacion">
						</div>
						<div class="col-3">
							<label>% de Descuento</label>
							<input type="number" name="porcentaje_descuento" class="form-control">
						</div>
						<div class="col-3">
							<label>Dias de Credito</label>
							<input type="number" name="dias_credito" class="form-control">
						</div>
					</div>
					<div class="row">
						
						<div class="col-3">
							<label for="islr">Retencion ISLR</label>
							<input type="checkbox" name="islr" class="" id="islr">
						</div>
						<div class="col-3">
							<label for="factura_cancelada">Factura Cancelada</label>
							<input type="checkbox" name="factura_cancelada" id="factura_cancelada" onchange="mostrarOcultarFecha();">
							
						</div>						
						<div class="col">
						<div id="ver_fecha" style="display:none" class="d-enline">	
							<label for="">Fecha del Pago</label>							
							<input type="date" name="fecha_pago" class="form-control">
							</div>
						</div>
					</div>
				</div>	
					<div class="row my-2">			
						<div class="col">
							<label>Iva</label>
							<input type="text" name="poriva" id="poriva" class="form-control text-right" value="{{$poriva}}" readonly>
						</div>
						<div class="col">
							<label>Gravado</label>
							<input type="text" name="gravado_f" id="gravado" class="form-control text-right moneda" value="0"  onkeyup="calculoIva();calculo();">
							
						</div>
						<div class="col">
							<label>exento</label>
							<input type="text" name="excento" id="exento" class="form-control text-right" value="0" onkeyup="calculo();">
						</div>
						<div class="col">
							<label>Monto iva</label>
							<input type="text" name="montoiva" id="montoiva" class="form-control text-right" value="0" onchange="calculo();">
						</div>
						<div class="col">
							<label>Monto</label>
							<input type="text" name="debitos" id="debitos" class="form-control text-right" readonly required>
						</div>
					</div>
					
					<div class="my-3 ">
						<button type="submit" class="btn btn-primary float-end float-right">Guardar</button>
					</div>

					
				</form>		
			
	</div>
	

@endsection	

@section('js')

<script type="text/javascript">
	// select 2
	$(document).ready(function() {
	    $('.js-example-basic-single').select2({
	    	maximumSelectionLength:1,
	    });
	});

	$(document).ready(function() {	
		
			$('#proveedores').DataTable({
		    scrollY: 300,
		    select: true,
		    paging: true,
		    searching: true,
    		ordering:  true
			});
			
    	

	} );
</script>
<script type="text/javascript">
		
	function calculo(){
		
		var gravado =document.querySelector('#gravado').value;
		if(gravado==''){
			gravado=0;
		}
		var exento =document.querySelector('#exento').value;
		if(exento ==''){
			exento=0;
		}
		var montoiva =document.querySelector('#montoiva').value;
		if(montoiva==''){
			montoiva=0;
		}
		var total = (parseFloat(gravado) + parseFloat(exento) + parseFloat(montoiva));
		document.getElementById('debitos').value = total;
	}

	function calculoIva(){
		var poriva =document.querySelector('#poriva').value;
		var gravado =document.querySelector('#gravado').value;
		if(gravado==''){
			gravado=0;
		}
		var montoiva = ((parseFloat(gravado) * parseFloat(poriva))/100);
		
		document.getElementById('montoiva').value = montoiva;
	}
	
	function copiarProveedor(id){
		 
		var pNombre = 'proveedorNombre'+id;
		var pRif = 'proveedoRif'+id;		
		var proveedorNombre = document.getElementById(pNombre).value;
		var proveedorRif = document.getElementById(pRif).value;
		document.getElementById('proveedor_nombre').value = proveedorNombre;
		document.getElementById('proveedor_rif').value = proveedorRif;
	}


	function mostrarOcultarFecha(){

		let div = document.getElementById('ver_fecha');
		let pagada = document.getElementById('factura_cancelada');
		if(pagada.checked == true){
			div.style.display="block";
		}else{
			div.style.display="none";
		}

	}
</script>
@endsection