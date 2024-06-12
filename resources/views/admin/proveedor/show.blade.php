@extends('layouts.app')
@section('content')
	
	<div class="jumbotron">
	  <h4 class="display-4">Proveedor: {{$proveedor->nombre}} Rif: {{$proveedor->rif}}</h4>
	  
	   <hr class="my-4">
	  
	   <div class="form-group my-2">
		  	<label>Tipo de proveedor</label>
		  	{{$proveedor->tipo_proveedor  ?? 'No registrado'}} 	  		
		  	
		  </div>
		  <div class="form-group my-2">
		  	<label>Tipo de Contribuyente</label>
		  	{{$proveedor->tipo_contribuyente ?? 'No registrado'}}
		  </div>
		  <div class="form-group">
		    <label for="direccion">Porcentaje de retención IVA</label>
		    <!-- <input type="text" class="form-control" name="porcentaje_retener" placeholder="100,75 ..." value="{{--$proveedor->porcentaje_retener--}}"> -->
			 {{$proveedor->porcentaje_retener ?? 'No registrado'}}
		  </div>
		  <div class="form-group">
		    <label for="direccion">Porcentaje de retención ISLR</label>
		    {{$proveedor->ultimo_porcentaje_retener_islr ?? 'No registrado'}}
		  </div>
		  <div class="form-group">
		    <label for="direccion">Codigo Fiscal</label>
		    {{$proveedor->codigoFiscal ?? 'No registrado'}}
		  </div>
		  <div class="form-group">
			<label for="">Correo Electronico</label>
			{{$proveedor->correo ?? 'No registrado'}}
		  </div>
		  <div class="form-group">
		    <label for="direccion">Direccion</label>
		    {{$proveedor->direccion  ?? 'No registrado'}}
		  </div>
		  <div class="card">
			<div class="card-body">
				<h5>Opciones de Cuentas por pagar incluir o excluir al momento de cargar las facturas y relacionar los pagos.</h5><hr>
				<div><input disabled type="checkbox" name="descontar_nota_credito" id="nota_credito" @if($proveedor->descontar_nota_credito == 1)checked @endif><label for="nota_credito">Descontar Nota de creditos</label></div>
				<div><input disabled type="checkbox" name="agregar_nota_debito" id="nota_debito" @if($proveedor->agregar_nota_debito == 1)checked @endif><label for="nota_debito">Agregar Notas de Debito</label></div>
				<div><input disabled type="checkbox" name="agregar_islr" id="agregar_islr" @if($proveedor->agregar_islr == true) checked @endif> <label for="agregar_islr">Agregar ISLR </label> </div>
				<div><input disabled type="checkbox" name="agregar_igtf" id="igtf" @if($proveedor->agregar_igtf == 1)checked @endif><label for="igtf">Agregar Impuesto IGTF por pagos en divisa</label></div>
				<div class="row">
					<div class="col-2"><label for="">Dias de Credito</label></div>
					<div class="col"><input type="number" disabled class="form-control w-50" placeholder='ingrese el numero de dias de credito' name="dias_credito" id="dias_credito" value="{{$proveedor->dias_credito ?? 0}}"></div>					
				</div>			
			</div>
		  </div>
	  <a class="btn btn-primary btn-lg" href="{{url('/proveedor')}}" role="button">Regresar</a>
	</div>
@endsection