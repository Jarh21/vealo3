@extends('layouts.app')
@section('css')


@endsection
@section('content')
    <h4>Retencion IVA Registro de Retención <a href="{{route('retencion.iva.index')}}" class='btn btn-warning btn-sm float-right'>< Regresar</a></h4><hr>
    <form action="{{route('retencion.iva.guardar')}}" method="post" name='registroRetencion' id='registroRetencion' target="_blank">
        @csrf
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <label for="">Proveedor Rif</label>
                        <input type="text" readonly class='form-control' name='rif_agente' value="{{$rif_agente ?? ''}}">
                    </div>
                    <div class="col">
                        <label for="">Nombre</label>
                        <input type="text" readonly class='form-control' name='nom_agente' value="{{$nom_agente ?? ''}}">
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label for="">Fecha</label>
                        <input type="date" class='form-control' name='fecha' required>
                        <label for="">Nº Comprobante</label>
                        <input type="text" class='form-control' name='comprobante' value='{{$contador}}' readonly>
                        <label for="">Nº Egreso/Cheque</label>
                        <input type="text" class='form-control' name='cheque' >
                    </div>
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                Datos del ultimo Comprobante
                                
                            </div>
                            <div class="card-body">
                                {{$ultimoComprobante[0]->comprobante ?? '0'}}
                            </div>
                        </div>
                        <input type="checkbox" name="firma_digital" id="firma_digital" value='firma'><label for="firma_digital">Firma Digital</label>
                    </div>
                </div>           
                
            </div>
            <div class="">
                <table class='table'>
                    <thead>
                        <tr>
                            <th>Fecha Documento</th>
                            <th>Nº Documento</th>
                            <th>Nº Control Factura</th>
                            <th>Tipo Transaccion</th>
                            <th>Nº Factura Afectada</th>
                            <th>Total Compas + Iva</th>
                            <th>Base Imponible</th>
                            <th>Impuesto IVA</th>
                            <th>IVA Retenido</th>
                            <th>% Retención</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $suma=0 ?>
                        @foreach($datosFacturas as $datoFactura)
                        <tr>
                            <td>{{$datoFactura->fecha_docu}}</td>
                            <td>{{$datoFactura->documento}}</td>
                            <td>{{$datoFactura->control_fact}}</td>
                            <td>{{$datoFactura->tipo_trans}}</td>
                            <td>{{$datoFactura->fact_afectada}}</td>
                            <td>{{$datoFactura->comprasmasiva}}</td>
                            <td>{{$datoFactura->base_impon}}</td>
                            <td>{{$datoFactura->iva}}</td>
                            <td>{{$datoFactura->porc_reten}}%</td>
                            <td>{{$datoFactura->iva_retenido}}</td>
                            <input type="hidden" name="facturas_id[]" value="{{$datoFactura->keycodigo}}">
                        </tr>
                        <?php $suma = $suma + floatval($datoFactura->iva_retenido);?>
                        
                        @endforeach
                        
                    </tbody>
                    <thead>
                        <tr>
                            <td colspan='9'><h4>Total IVA Retenido:</h4></td>
                            <td><h4>{{$suma}}</h4></td>
                        </tr>
                    </thead>
                </table>
                <button type="submit" id='guardarBtn' class="btn btn-primary float-right">Guardar Comprobante</button>
            </div>

        </div>
    </form>
@endsection
@section('js')
<script type="text/javascript">
    $(document).ready(function(){
        $("#registroRetencion").submit(function(){
            var formValido = true;
        
            // Verificar si los campos están llenos
            $(this).find('input[type="text"]').each(function(){
                if($(this).val() === ''){
                    formValido = false;
                    return false; // Salir del bucle each si se encuentra un campo vacío
                }
            });
            // Si los campos están llenos, deshabilitar el botón de submit
            if(formValido){
                $("#guardarBtn").prop('disabled', true);
            }

            window.open('', 'myNewWindow'); // Abre una nueva ventana o pestaña con un nombre específico
            $(this).attr('target', 'myNewWindow'); // Establece el atributo target para que el resultado del formulario se abra en la nueva ventana o pestaña
        });

    });
</script>
@endsection