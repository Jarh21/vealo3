@extends('layouts.app')

@section('content')
<div class="container">
    <div class="  bg-light">
        <p class="display-5">Registro de Cuadre</p>
        <form action="{{route('cuadres.seleccionFechaRegistroCuadre')}}" method="post">
            <div class="d-flex flex-row bd-highlight ">
                
                @csrf
                <div class="p-2 bd-highlight">
                    <label for="">Empresa</label>
                </div>
                <div class="p-2 bd-highlight">
                    <cambio-empresa></cambio-empresa>
                </div> 
                <div class="p-2 bd-highlight">
                    <label for="">Fecha del cuadre</label>
                </div>
                <div class="p-2 bd-highlight">
                    <input name="fecha" type="date" @if(isset($fechaCuadre)) value="{{$fechaCuadre}}" @endif class="form-control">
                    
                </div>
                <div class="p-2 bd-highlight">
                <button type="submit" class="btn btn-primary">Continuar</button>
                </div>
                    
            </div>
        </form><hr>
    </div>
    @if(!empty($reporteZetas))
    <div class="container">
        <div class="my-3">
            <h5>Indique las Observaciones del Cuadre</h5>
            
            <div class="my-2"><observacion-cuadre-efectivo/></div>
            <div class="my-2"><observacion-cuadre-tarjeta/></div>
            <div class="my-2"><observacion-cuadre-otra/></div>
            
        </div>
        <fieldset disabled>
            <div>
                <b class="text-secondary">Indique los detalles de Cierre de lote</b>
                <div><cuadre-cierre-de-lotes/></div>
            </div> 
        </fieldset>
        
        <div class="my-3">
            <b>Indique los Detalles de las Transferencias Recibidas</b>
            <div><cuadre-registrar-transferencias/></div>
        </div>

        <div class="my-3">
            <b>Indique los Detalles de los Prestamos en Efectivo</b>
            <div><cuadre-prestamos-efectivo/></div>
        </div>
            <div class="row">
                <div class="col">
                    <h4>Información de Reporte Z</h4>
                    <table class="table "  style="font-size: 13px">
                        <tr>
                            <th colspan=2 class="table-warning">Datos del Reporte Z</th>
                            <th colspan=4 class="table-primary">Ventas</th>
                            <th colspan=4 class="table-success">Reverso</th>
                            <th colspan=3 class="table-secondary">Factura</th>
                        </tr>
                        <tr >
                            <th class="table-warning">Maquina</th>
                            <th class="table-warning">N.Z</th>
                            <th class="table-primary">Excento</th>
                            <th class="table-primary">Base</th>
                            <th class="table-primary">IVA</th>
                            <th class="table-primary">Total</th>
                            <th class="table-success">Excento</th>
                            <th class="table-success">Base</th>
                            <th class="table-success">IVA</th>
                            <th class="table-success">Total</th>
                            <th class="table-secondary">Anterior</th>
                            <th class="table-secondary">Cant. actual</th>
                            <th class="table-secondary">Ultima Actual</th>
                        </tr>
                    
                    
                        @foreach($reporteZetas as $reporteZeta)
                        <tr>
                            <td class="table-warning">{{$reporteZeta->fiscalserial}}</td>
                            <td class="table-warning">{{$reporteZeta->numero_de_zeta}}</td>
                            <td class="table-primary">{{$reporteZeta->ventas_exento}}</td>
                            <td class="table-primary">{{$reporteZeta->ventas_de_tasa_general}}</td>
                            <td class="table-primary">{{$reporteZeta->total_impuesto_en_ventas}}</td>
                            <td class="table-primary">{{$reporteZeta->total_ventas}}</td>
                            <td class="table-success">{{$reporteZeta->nota_de_credito_exento}}</td>
                            <td class="table-success">{{$reporteZeta->nota_de_credito_tasa_general}}</td>
                            <td class="table-success">{{$reporteZeta->total_impuesto_en_nota_de_credito}}</td>
                            <td class="table-success">{{$reporteZeta->total_nota_de_credito}}</td>
                            <td class="table-secondary">{{$reporteZeta->numero_factura_anterior}}</td>
                            <td class="table-secondary">{{$reporteZeta->cantidad_facturas}}</td>
                            <td class="table-secondary">{{$reporteZeta->numero_ultima_factura}}</td>
                        </tr>    
                        @endforeach
                    
            
                    </table>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col">
                    <h4>INFORMACIÓN DE REPORTE Z GUARDADA EN SISTEMA (UTILICE ESTA TABLA PARA FINES COMPARATIVOS)</h4>
                    <table class=" "  style="font-size: 13px">
                        <thead>
                           <tr>
                           <th>Equipo</th>
                           <th>Fecha</th>
                           <th>Codorigen</th>
                           <th>Maquina</th>
                           <th>Zeta</th>
                           <th>Debitos</th>
                           <th>Creditos</th>
                           <th>Exento</th>
                           <th>Gravado</th>
                           <th>Iva</th>                            
                            </tr> 
                        </thead>
                        <tbody border=1>
                          @foreach($reportesCxc as $reporteCxc)
                            <tr style="height: 10px;">
                                <td class="px-3" style="border: grey 3px solid;">{{$reporteCxc->equipo}}</td>
                                <td class="px-3" style="border: grey 3px solid;">{{$reporteCxc->fecha}}</td>
                                <td class="px-3" style="border: grey 3px solid;">{{$reporteCxc->codorigen}}</td>
                                <td class="px-3" style="border: grey 3px solid;">{{$reporteCxc->fiscalserial}}</td>
                                <td class="px-3" style="border: grey 3px solid;">{{$reporteCxc->fiscalz}}</td>
                                <td class="px-3" style="border: grey 3px solid;">{{number_format($reporteCxc->debitos)}}</td>
                                <td class="px-3" style="border: grey 3px solid;">{{number_format($reporteCxc->creditos)}}</td>
                                <td class="px-3" style="border: grey 3px solid;">{{number_format($reporteCxc->exento)}}</td>
                                <td class="px-3" style="border: grey 3px solid;">{{number_format($reporteCxc->gravado)}}</td>
                                <td class="px-3" style="border: grey 3px solid;">{{$reporteCxc->iva}}</td>
                            </tr>
                            @endforeach   
                        </tbody>                       
                    </table>
                </div>
            </div>
            <div class="my-2"><observacion-cuadre-general/></div>
    </div>
    @else
        @if(isset($fechaCuadre))
            <div class="alert alert-warning">¡ No se encontraron registros para la fecha !</div>
        @endif
    @endif
</div>

@stop