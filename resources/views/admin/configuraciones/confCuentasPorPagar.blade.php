@extends('layouts.app')
@section('content')
    <div class="container">
        <h3>Configuracion de Cuentas Por Pagar</h3><hr>
        <form action="{{route('guardarConfiguracionCuentasPorPagar')}}" method="post">
            @csrf
            <div class="row">
                <div class="col">
                <label for="pago_facturas_desde_facturas_por_pagar"> * Permitir registro de pago de facturas en divisas desde el registro de las facturas(facturas por pagar), esto es debido a que las facturas en divisas se cancelas es desde la relacion de facturas, pero se puede permitir desde el regitro de las mismas.</label>

                </div>
                <div class="col">
                <input 
                    type="checkbox" 
                    name="pago_facturas_desde_facturas_por_pagar" 
                    id="pago_facturas_desde_facturas_por_pagar"
                    @if($pago_facturas_desde_facturas_por_pagar==1)
                        checked
                    @endif    
                >

                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col">
                    <label for="verificar_facturas_en_siace">* En Facturas a relacionar, verificar si la factura no la han eliminado del sistema siace, esta verificacion solo aplica en aquellas facturas importadas de dicho sistema. </label>
                </div>
                <div class="col">
                <input 
                    type="checkbox" 
                    name="verificar_facturas_en_siace" 
                    id="verificar_facturas_en_siace"
                    @if($verificar_facturas_en_siace==1)
                        checked
                    @endif    
                >
                </div>
                
            </div>
            <hr>
            <div class="row">
                <div class="col">
                    <label for="verificar_tasa_dolar_tipo_moneda_o_historial_dolar">* En importar facturas y procesar pagos,  esta verificar la tasa del dolar en la tabla tipo_moneda o historial_dolar, en caso de usar moneda base las divisas debe escojer tipo_moneda_base. </label>
                </div>
                <div class="col">               
                    <select name="verificar_tasa_dolar_tipo_moneda_o_historial_dolar" id="verificar_tasa_dolar_tipo_moneda_o_historial_dolar">
                        <option value="historial_dolar_siace" @if($verificar_tasa_dolar_tipo_moneda_o_historial_dolar=='historial_dolar_siace')selected @endif>historial_dolar_siace</option>
                        <option value="historial_dolar_vealo" @if($verificar_tasa_dolar_tipo_moneda_o_historial_dolar=='historial_dolar_vealo')selected @endif>historial_dolar_vealo</option>
                        <option value="tipo_moneda_historial_tasa_vealo" @if($verificar_tasa_dolar_tipo_moneda_o_historial_dolar == 'tipo_moneda_historial_tasa_vealo')selected @endif>Tipo Moneda Historial Tasa Vealo</option>
                        <option value="tipo_moneda_historial_tasa_siace" @if($verificar_tasa_dolar_tipo_moneda_o_historial_dolar == 'tipo_moneda_historial_tasa_siace')selected @endif>Tipo Moneda Historial Tasa Siace</option>    
                        <option value="tipo_moneda_secundaria" @if($verificar_tasa_dolar_tipo_moneda_o_historial_dolar=='tipo_moneda_secundaria')selected @endif>tipo_moneda_secundaria</option>
                        <option value="tipo_moneda_base" @if($verificar_tasa_dolar_tipo_moneda_o_historial_dolar=='tipo_moneda_base')selected @endif>tipo_moneda_base</option>
                    </select>
                </div>                
            </div>
            <hr>
            <div class="row">
                <div class="col">
                    <label  for="tipo_moneda">* Para Optener la configuracion del tipo de moneda se puede hacer desde la base de datos del siace o del vealo, indique la d esu preferencia</label>
                </div>
                <div class="col">               
                    <select name="base_datos_tipo_moneda" id="tipo_moneda" >
                        <option value="deshabilitado">--Seleccione--</option>
                        <option value="tipo_moneda_siace" @if($base_datos_tipo_moneda == 'tipo_moneda_siace')selected @endif>Tipo Moneda Siace</option>
                        <option value="tipo_moneda_vealo" @if($base_datos_tipo_moneda == 'tipo_moneda_vealo')selected @endif>Tipo Moneda Vealo</option>


                    </select>
                </div>                
            </div>
            <hr>
            <div class="row">
                <div class="col">
                    <label  for="importar_server2_a_server1_cxp">* Importar de la tabla cxp del servidor 2 al servidor 1, el servidor1 es el principal </label>
                </div>
                <div class="col-2">               
                <input 
                    type="checkbox" 
                    name="importar_server2_a_server1_cxp" 
                    id="importar_server2_a_server1_cxp"
                    @if($importar_server2_a_server1_cxp==1)
                        checked
                    @endif    
                >
                </div>
                <div class="col">
                    <label for="">Numero de Registros a Importar de cxp </label>
                    <input type="text" name="numero_registros_importar_cxp" placeholder="minimo 200 ... maximo 1000" value="{{$numero_registros_importar_cxp ?? ''}}">
                    <label for="">Numero de Registros a Importar de Nota de Credito </label>
                    <input type="text" name="numero_registros_importar_notacredito" placeholder="minimo 50 ... maximo 300" value="{{$numero_registros_importar_notacredito ?? ''}}">
                </div>                
            </div>
            <hr>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </form>
    </div>
@endsection