@extends('layouts.app')
@section('content')
	<main role="">
    <section id="">
        <div class="container mt-4 mb-2">
            <div class="row">
                <div class="col-12 text-center">
                    <span class="">
                        <!--<img src="img/logofd.png" class="text-center" alt=""> -->
                    </span>
                    <h3 class="text-center text-secondary">
                        Registro De Operaciones En Divisas

                    </h3>
                    <hr>
                    <form class="text-left" action="{{route('save.operacio.divisa')}}" method="post">
                    	@csrf
                        <div class="form-row">
                            <label>Empresa</label>
                            <select name="conexion" class="form-control">
                                <option value="hptal">Farmacia Farma Hospital</option>
                                <option value="ffap">Farmacia Fuerzas Armadas</option>
                                <option value="fexpres">Farmcaia Farma Expres</option>
                                <option value="ffados">Farmacia Fuerzas Armadas Dos</option>
                            </select>
                            <div class="form-group col-6">
                                <label for="fecha" class="font-weight-bolder">Fecha</label>
                                <input type="datetime" class="form-control" id="fecha" value="{{date('Y-m-d H:i:s')}}"  name="fecha">
                            </div>
                            <div class="form-group col-6">
                                <label for="asesor" class="font-weight-bolder">Asesor</label>
                                <select name="asesor" id="asesor" class="form-control" required>                                	
                                	<option value="">-- Seleccione el Asesor --</option>
                                    <option value="007-Jose Rivero">Jose Rivero</option>
                                    <option value="737-ALEXANDRA VILLANUEVA">ALEXANDRA VILLANUEVA</option>
                                	@foreach($asesores as $asesor)
                                	<option value="{{$asesor->codusua}}-{{$asesor->usuario}}">{{$asesor->usuario}}</option>
                                	@endforeach
                                </select>
                                
                            </div>
                            <?php $ValorDivisa=$cotizacionDivisa->precio_venta_moneda_nacional; ?>
                           
                        <div class="form-group col-6">
                            <label for="cotizacion" class="font-weight-bolder">Cotizacion</label>
                            <input type="text" class="form-control" name="cotizacion" placeholder="Valor de la divisa actualmente" value="{{number_format($ValorDivisa,2,',','.')}}" >
                        </div>
                        <div class="form-group col-6">
                            <label for="divisasAconsumir" class="font-weight-bolder">Divisas a consumir</label>
                            <input type="text" class="form-control" id="divisasAconsumir" name="divisasAconsumir" placeholder="Cantidad de divisas que consumira el cliente" tabindex="2" onchange="alerta();" onkeyup="alerta();" required>
                        </div>
                        <div class="form-group col-6">
                            <label for="montoAconsumirEnBolivares" class="font-weight-bolder">Monto a consumir en Bolivares</label>
                            <input type="text" class="form-control" name="montoAconsumirEnBolivares" id="montoAconsumirEnBolivares" placeholder="Monto a consumir en bolivares" readonly>
                        </div>
                        <div class="form-group col-6">
                            <label for="divisasRecibidas" class="font-weight-bolder">Divisas Recibidas</label>
                            <input type="text" class="form-control" id="divisasRecibidas" name="divisasRecibidas" placeholder="Cantidad de divisas que recibes" tabindex="3" required onchange="vuelto();" onkeyup="vuelto();">
                        </div>
                        <div class="form-group col-6">
                            <label for="divisasCambioEfectivo" class="font-weight-bolder">Divisa disponible para el vuelto</label><img src="{{asset('imagenes/62878dollarbanknote_109277.png')}}">
                            <input type="text" class="form-control" id="divisasCambioEfectivo" name="divisasCambioEfectivo" placeholder="Cantidad de divisas que das como cambio" tabindex="4" onchange="vuelto();" onkeyup="vuelto();" required value="0">
                        </div>
                        <div class="form-group col-6">
                        	
                            <label for="montoCambioEnPagoMovil" class="font-weight-bolder">Monto de cambio en pago movil Bs. </label><img src="{{asset('imagenes/whatsappmobile-phone_85144.png')}}">
                            <input type="text" class="form-control" id="montoCambioEnPagoMovil" name="montoCambioEnPagoMovil" placeholder="Monto transferido por pago movil al cliente" readonly>
                        	
                        </div>
                        
                        <div class="form-group col-6">
                            <label for="numFactura" class="font-weight-bolder">Numero de Factura</label>
                            <input type="text" class="form-control" name="numFactura" placeholder="Indique el numero de factura" tabindex="6" required>
                        </div>
                        
                        </div>
                            <button type="submit" class="btn btn-primary mt-3" tabindex="7">Registrar Operaci&oacute;n</button>
                    </form>
            	</div>
            </div>           
            	
            	
    </section>
</main>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script type="text/javascript">
    	
    	function alerta(){
    		
    		var dc = document.querySelector('#divisasAconsumir').value;
    		var resultado = dc*<?php echo $ValorDivisa;?>;
    		document.getElementById('montoAconsumirEnBolivares').value=resultado;    		
    	
    	}

    	function vuelto(){
    		var divisaConsumir =document.querySelector('#divisasAconsumir').value;
    		var divisaRecibida =document.querySelector('#divisasRecibidas').value;
    		var vueltoDivisa = document.querySelector('#divisasCambioEfectivo').value;
    		var total = (((divisaRecibida-vueltoDivisa - divisaConsumir))*<?php echo $ValorDivisa;?>);
    		document.getElementById('montoCambioEnPagoMovil').value= total.toLocaleString();
    	}
    </script>
    <script>
     /*   window.jQuery || document.write('<script src="jquery.slim.min.js"><\/script>')*/
    </script>
    <script src="bootstrap.bundle.min.js"></script>
@endsection