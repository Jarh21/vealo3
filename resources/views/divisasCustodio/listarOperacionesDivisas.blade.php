@extends('layouts.app')
@section('content')
	<div class="container">
		<h3>Listado de las operaciones con Divisas <a href="{{route('divisasCustodio.create')}}" class="btn btn-primary float-right">Registrar Divisa</a></h3>
		<form class="text-left" action="{{route('buscar.operaciones.divisas')}}" method="post">
	    	@csrf
	        <div class="form-row">
	            <div class="form-group col-6">
	            	<label>Empresa</label>
	            	<select name="conexion" class="form-control" required>
	            		<option value="">-- Seleccione Empresa --</option>
	            		@foreach($empresas as $empresa)
	            			<option value="{{$empresa->basedata}}" @if(isset($datosEmpresa)) @if($datosEmpresa[0]->conexion==$empresa->basedata)selected @endif @endif>{{$empresa->nombre}}
	            			</option>
	            		@endforeach
	            	</select>
	                <label for="fecha" class="font-weight-bolder">Fecha a buscar</label>
	                <input type="date" required class="form-control" value="{{$fecha ?? date('Y-m-d')}}" id="fecha" name="fecha">
	            </div>
			</div>
			<button type="submit" class="btn btn-primary">Buscar</button>
		</form>
		@if(isset($registrosDiario))
		<h3>{{$datosEmpresa[0]->nombre_empresa}}</h3>
		<?php $conexion =$datosEmpresa[0]->conexion; ?>
		<form class="form-inline ml-5" action="{{route('buscar.operaciones.divisas.asesor',[$conexion,$fecha])}}" method="post">
			@csrf
            <div class="input-group input-group-sm">
                <input class="form-control form-control-navbar" name="busqueda" type="search" placeholder="Search"
                    aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-navbar" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
		<table class="table">
            		<thead>
            			<tr>
            				<th>Nº Factura</th>
	            			<th>Asesor</th>
	            			<th>Divisa a consumir</th>
	            			<th>Divisa Recibida</th>
	            			<th>Vuelto en $</th>
	            			<th>Vuelto Pago Movil</th>
	            			<th>Fecha</th>
	            			<th>Accion</th>
            			</tr>
            		</thead>
            		 @foreach($registrosDiario as $registroDiario)
            		<tbody>
            			<tr>
            				<td>{{$registroDiario->numero_factura}}</td>
            				<td>{{$registroDiario->usuario}}</td>
            				<td>{{$registroDiario->divisa_a_consumir}}$</td>
            				<td>{{$registroDiario->divisa_a_recibir}}$</td>
            				<td>{{$registroDiario->divisa_para_cambio_en_efectivo}}$</td>
            				<td>{{number_format($registroDiario->monto_para_cambio_en_pago_movil,2,',','.')}}</td>
            				<td>{{$registroDiario->registrado}}</td>
            				<td><a href="#" class="btn btn-primary">Edit</a></td>
            			</tr>	
            		</tbody>
            		@endforeach
            	</table>
        @endif
	</div>
@endsection