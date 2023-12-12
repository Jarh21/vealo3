@extends('layouts.app')

@section('content')
@php 
		//si monedaBase es nacional s calcula el valor de la divisa dividiendo el monto entre la tasa y si es extranjera se multiplica el monto por el valor de la tasa
		$monedaBase = session('monedaBase'); 
		
	@endphp
<div class="container-fluid bg-white">
    <div class="row">
		<div class="col-3">
			<img src="{{ asset(session('logo_empresa'))}}" alt="AdminLTE Logo" class="" style="opacity: .8" width="100px">
            <p>{{session('nombre_general_empresa')}}</p>
		</div>
		<div class="col">
			<h3 class="d-inline"><i class="fa fa-calculator nav-icon mr-2"></i>Relacion Pago Por Proveedor </h3>
			
		</div>
		
	</div>
    <div class="d-print-none mb-3">
        <h4>Reporte de la relacion de Pagos Por empresa y fecha</h4>
        <div class="row">
            <div class="col-10">
                <form action="{{route('resultadoReportePagoPorProvedorTodasEmpresas')}}" method="post" id="busca">
                    @csrf
                    <div class="row">
                        <div class="col">
                        <label for="proveedor">Proveedor</label>
                            <select name="proveedor" class="js-example-basic-single" style="width: 80%" multiple="multiple" required>
			    				<option value=""></option>
			    				@if(isset($proveedores))
			    				@foreach($proveedores as $proveedor)
									<option value="{{$proveedor->rif}}|{{$proveedor->nombre}}" @if(isset($proveedorRif)) @if($proveedorRif == $proveedor->rif) selected @endif @endif>{{$proveedor->rif}} {{$proveedor->nombre}}</option>		
								@endforeach
								@endif
			    			</select>
                        </div>
                    </div>
                    <div class="row">                        
                        <div class="col">
                            <label>Fecha Inicio</label>
                            <input type="date" name="fechaIni" @if(isset($fechaIni)) value="{{$fechaIni}}" @endif class="form-control" required>
                        </div>
                        <div class="col">
                            <label>Fecha Final</label>
                            <input type="date" name="fechaFin" @if(isset($fechaFin)) value="{{$fechaFin}}" @endif class="form-control" required>
                        </div>
                        <div class="col">
                            <div class=" mt-4">
                                <button type="submit" class="btn btn-primary btn-sm d-enline"><i class="fa fa-search"></i>Buscar</button>
                                <a href="#" onclick="javascript:window.print();" class="btn btn-secondary btn-sm"><i class="fa fa-print"></i>Imprimir</a>
                            </div>
                            
                        </div>
                    </div>
                    
                    
                    
                </form>
            </div>
            
        </div>
			
	</div>
    <div>
        <!-- resultado de la busqueda -->
        @if(isset($pagos))
        <div class="my-3">
            <h4>Proveedor: {{$proveedorSeleccionado}}</h4>
            <p>Fecha Pago del: {{$fechaIni}} al {{$fechaFin}}</p>
        </div>
            <table class="table my-1 p-0">
                <thead>
                    <tr>
                        <th>Empresa</th>
                        <th>Facturas</th>
                        <th>Monto Entregado</th>                       
                        <th>Firma</th>
                    </tr>
                </thead>
                 <!--   verificamos si la moneda base es nacional o exrtanjera, si se extranjera se coloca solo debitos -credito sin dividir entre la tasa  -->
                @if($monedaBase=='nacional')
                <tboby>
                    <?php $sumaBs=0;$sumaDivisa=0; $id=0?>
                    @foreach($pagos as $pago)
                    <?php $id++; ?>  
                    <!-- separamos los documentos porrque vienen todos concatenados    -->         
                    <?php $documentos = explode(',',$pago->documento); ?> 
                    <tr>
                        <td>{{$pago->empresa_rif}} {{$pago->empresa_nombre}}</td>
                        <td>
                            <form name='{{$pago->empresa_rif}}' id='{{$pago->empresa_rif}}'>
                                
                                @foreach($documentos as $documento)
                                <!-- separamos el numero de factura del monto -->
                                <?php $datosFactura = explode('-',$documento) ?>             
                                <span @if($datosFactura[2] > 0)style="text-decoration:line-through" title="Factura cancelada" @endif>{{$datosFactura[0] ?? 'no hay'}} </span><span >{{number_format($datosFactura[1],2)}}</span>
                                <input type="checkbox" checked name="otro{{$pago->empresa_rif}}[]" id="otro{{$pago->empresa_rif}}" value="{{$documento}}" class="d-print-none" onclick="sumarFacturas('{{$pago->empresa_rif}}')">
                                <br>                                
                                @endforeach
                                <input type="hidden" id='inputSubtotal{{$pago->empresa_rif}}' name='{{$pago->empresa_rif}}' class="subtotales" value="{{round($pago->divisa,3)}}" >
                            
                            </form>
                        </td>                        
                        <td> 
                                                   
                          
                            <b id='subTotal{{$pago->empresa_rif}}'>{{number_format($pago->divisa,3,'.',',')}}</b>
                                                                                 
                        </td>
                        <td></td>
                    </tr>                        
                    <?php $sumaDivisa += floatval($pago->divisa);?>
                    @endforeach
                    <tr style="background-color: #D6DCD9">
                        <td colspan='2' style="text-align: right">Total a la Fecha: </td>                        
                        <td id='total'>{{number_format($sumaDivisa,3)}}</td>                            
                        <td></td>
                    </tr>
                        
                </tbody>
                @else
                <!-- cuando el valor de la moneda Base es extranjera -->
                <tboby>
                    <?php $sumaBs=0;$sumaTotal=0; $id=0?>
                    @foreach($pagos as $pago)
                    <?php $id++; ?>  
                    <!-- separamos los documentos porrque vienen todos concatenados    -->         
                    <?php $documentos_real = explode(',',$pago->documento_real); ?> 
                    <tr>
                        <td>{{$pago->empresa_rif}} {{$pago->empresa_nombre}}</td>
                        <td>
                            <form name='{{$pago->empresa_rif}}' id='{{$pago->empresa_rif}}'>
                                
                                @foreach($documentos_real as $documento_real)
                                <!-- separamos el numero de factura del monto -->
                                <?php $datosFactura = explode('-',$documento_real) ?>             
                                <span @if($datosFactura[2] > 0)style="text-decoration:line-through" title="Factura cancelada" @endif>{{$datosFactura[0] ?? 'no hay'}} </span><span >{{number_format($datosFactura[1],2)}}</span>
                                <input type="checkbox" checked name="otro{{$pago->empresa_rif}}[]" id="otro{{$pago->empresa_rif}}" value="{{$documento_real}}" class="d-print-none" onclick="sumarFacturas('{{$pago->empresa_rif}}')">
                                <br>                                
                                @endforeach
                                <input type="hidden" id='inputSubtotal{{$pago->empresa_rif}}' name='{{$pago->empresa_rif}}' class="subtotales" value="{{round($pago->divisa,3)}}" >
                            
                            </form>
                        </td>                        
                        <td> 
                                                   
                          
                            <b id='subTotal{{$pago->empresa_rif}}'>{{number_format($pago->monto_real,3,'.',',')}}</b>
                                                                                 
                        </td>
                        <td></td>
                    </tr>                        
                    <?php $sumaTotal += floatval($pago->monto_real);?>
                    @endforeach
                    <tr style="background-color: #D6DCD9">
                        <td colspan='2' style="text-align: right">Total a la Fecha: </td>                        
                        <td id='total'>{{number_format($sumaTotal,3)}}</td>                            
                        <td></td>
                    </tr>
                        
                </tbody>
                @endif
            </table>
        @endif
    </div>
    <div class="d-print-none">
        <div class="alert alert-info">
            <p>
                <b>Nota:</b>Este reporte solo muestra las facturas relacionadas para pagar en una fecha especificada, esto se realiza en el siguiente enlace <a href="{{route('relacionPagoFacturasIndex')}}">Facturas a Relacionar</a> y dicha relacion se visualiza en: <a href="{{route('listadoFacturasCalculadas')}}">Facturas Calculadas</a>, este reporte no incluye los pagos registrados directamente del modulo <a href="{{route('cuentasporpagar.facturasPorPagar')}}">Ingreso Factura</a>
            </p>
        </div>
    </div>
</div>
@endsection
@section('js')

<script>
    // select 2
	$(document).ready(function() {
	    $('.js-example-basic-single').select2({
	    	placeholder: 'Seleccione el proveedor',    	
	    	maximumSelectionLength:1,
	    });

        //buscar todos los input que tienen el total en farmacia y luego ejecutar el calculo de los totales
        /* var sumaSubTotales = document.getElementsByClassName('subtotales');//nos traemos todos los input que contengan la clase subtotales
        totalSumado=0;
         for(let i=0;i < sumaSubTotales.length;i++){//recoremos el arreglo del formulario 
            //totalSumado = totalSumado + parseFloat(sumaSubTotales[i].value);//sumamos
            console.log(sumaSubTotales[i].name);
            sumarFacturas(sumaSubTotales[i].name);
        } */
    

	});
</script>
<script>
    function sumarFacturas(empresaRif){
        //optenemos los valores de las input
        var subtotal = document.getElementById('inputSubtotal'+empresaRif);
        var total = document.getElementById('inputTotal');
        var formulario = document.getElementById(empresaRif);
        var campo = 'otro'+empresaRif;
        //detectamos la cantidad de checkbox
        obj = formulario[campo];
        totalchecks = obj.length;
        subTotalSumado=0;
       //recorremos todos los checkbox comparando los que esten activos
        for(i=0; i< totalchecks; i++){
            if(obj[i].checked == true){
                valor = obj[i].value.split('-');//separamos el numero de la factura del monto
                //console.log(valor);
                subTotalSumado = subTotalSumado + parseFloat(valor[1]);//sumamos los montos
                
            }
        } 
        //imprimimos el resultado por pantalla    
        document.getElementById('subTotal'+empresaRif).innerHTML =  subTotalSumado.toFixed(3);
        subtotal.value = subTotalSumado;
        actualizarTotal();//llamamos la funcion que actualiza el monto total
    }     

    function actualizarTotal(){
        var sumaSubTotales = document.getElementsByClassName('subtotales');//nos traemos todos los input que contengan la clase subtotales
        totalSumado=0;
        for(let i=0;i < sumaSubTotales.length;i++){//recoremos el arreglo del formulario 
            totalSumado = totalSumado + parseFloat(sumaSubTotales[i].value);//sumamos
        }
        document.getElementById('total').innerHTML = totalSumado.toLocaleString('en-IN', { minimumFractionDigits: 3});//le damos formato e imprimimos en pantalla
       // console.log('estoy en atualizar total');
        

    }
</script>
@endsection