@extends('layouts.app')
@section('content')
<div class="container">
	<h3>Procesar Pago Movil de Operaciones con Divisas</h3><hr>
	{{$id}}
	<form class="text-left" action="{{route('save.pago.movil',[$conexion,$id])}}" method="post">
		@method('PUT')
    	@csrf
        <div class="form-row">
            <div class="form-group col-6">
                <label for="fecha" class="font-weight-bolder">Fecha</label>
                <input type="text" class="form-control" id="fecha" value="{{$buscarPagoMovil[0]->registrado}}"name="fecha" readonly>
            </div>
            
		</div>
		<div class="form-row">
            <div class="form-group col-6">
                <label for="fecha" class="font-weight-bolder">Asesor</label>
                <input type="text" class="form-control" id="fecha" value="{{$buscarPagoMovil[0]->usuario}}"name="usuario" readonly>
            </div>
           <div class="form-group col-6">
                <label for="monto" class="font-weight-bolder">Monto del pago movil</label>
                <input type="text" class="form-control" name="monto" value="{{number_format($buscarPagoMovil[0]->monto_para_cambio_en_pago_movil,2,',','.')}}" tabindex="5" readonly>
            </div>
		</div>
		<div class="form-row">
			<div class="form-group col-6">
                <label for="referenciaPagoMovil" class="font-weight-bolder">Numero de referencia del pago movil</label>
                <input type="text" class="form-control" autocomplete="off" name="referenciaPagoMovil" placeholder="Indique numero de referencia del pago movil" tabindex="5" required>
            </div>
			<div class="form-group col-6">
	            <label for="entidad" class="font-weight-bolder">Entidad Fianciera</label>
	            <select name="entidad" class="form-control" required>
	            	<option value="">-- Seleccione entidad Financiera</option>
	            	<option value="PROVINCIAL">PROVINCIAL</option>
	            	<option value="TESORO">TESORO</option>
	            	<option value="BNC">BNC</option>
	            	<option value="VENEZUELA">VENEZUELA</option>
	            </select>
	            
	        </div>
    	</div>
    	<div class="form-row">
    		<div class="col-6 mb-4">
    			<label>Telefono del cliente al que se le va hacer el pago movil</label>
    			<input type="text" name="tlf" class="form-control" autocomplete="off" required placeholder="ingrese el numero sin separador Ej: 04243669746">
    		</div>
    	</div>
		<button type="submit" class="btn btn-primary">Pagar</button>
	</form>	
</div>
@endsection