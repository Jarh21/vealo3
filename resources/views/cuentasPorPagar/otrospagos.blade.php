@extends('layouts.app')

@section('content')
	<div class="container">
		<h3>Registro de Otros Pagos <a href="{{route('cuentasporpagar.facturasPorPagar')}}" class="btn btn-warning float-right"> <i class="fa fa-reply" aria-hidden="true"></i>Regresar</a></h3><hr>
		<h4>{{session('empresaNombre')}} {{session('empresaRif')}} </h4>
		<h5>Tipo de Moneda para el pago <span class="text-success">{{session('modoPago')}}</span></h5>
			
			<form action="{{route('saveotrospagos')}}" method="post">
				<div class="col">
					@csrf					

					<div class="row">

					<!--	<div class="col">
							<label>Empresa</label>
							<select name="empresa" class="form-control" required>
				    		<option value="">-- Seleccione Empresa --</option>
				    		@foreach($empresas as $empresa)
				    			<option value="{{$empresa->rif}}|{{$empresa->basedata}}" @if(isset($datosEmpresa)) @if($datosEmpresa[0]==$empresa->rif)selected @endif @endif>{{$empresa->nombre}}
				    			</option>
				    		@endforeach
				    		</select>
						</div>-->
						<div class="col">
							<label>Fecha del Pago</label>
							<input type="date" name="fecha_pago" class="form-control" required>
						</div>
						
					</div>
					<div class="row">						
						<div class="col">
							<label>Beneficiario</label>
							<input type="text" name="beneficiario" id="beneficiario" class="form-control" required>
						</div>
						<div class="col">
							<label>Rif Beneficiario</label>
							<input type="text" class="form-control" name="proveedor_rif" placeholder="Ej. J-12345678-0">
						</div>
						<div class="col">
							<label>Concepto</label>
							<input type="text" name="concepto_descripcion" id="concepto_descripcion" class="form-control" required>
						</div>
					</div>
					
					<div class="row my-2">
						<div class="col">
							<label>Referencia Bancaria</label>
							<input type="text" name="referencia_pago" class="form-control">
							
						</div>
						<div class="col">
							<label id="lbanco_id" >Banco</label>
							<select name="banco_id" id="banco_id" class="form-control">
								<option value="">- Selecciones -</option>
								@foreach($bancos as $banco)
								<option value="{{$banco->id}}">{{$banco->nombre}}| Nº {{$banco->id}}</option>
								@endforeach
							</select>
						</div>
						<div class="col">
							<label>Monto</label>
							<input type="text" name="creditos" id="creditos" class="form-control text-right" required>
						</div>					

					</div>
				</div>	
					
										
					<button type="submit" class="btn btn-primary">Guardar</button>
				</form>
			
			
	</div>
        
@endsection	
@section('js')
<script type="text/javascript">
		/*funcion que formatea el valor numerico al de moneda*/
	$(document).ready(function() {
        $("#creditos").on({
            "focus": function (event) {
                $(event.target).select();
            },
            "keyup": function (event) {
                $(event.target).val(function (index, value ) {
                    return value.replace(/\D/g, "")
                                .replace(/([0-9])([0-9]{2})$/, '$1,$2')
                                .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
                });
            }
        });
    } );    
</script>
@endsection