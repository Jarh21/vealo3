@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row">
		<div class="col-3">			
            <img src="{{ asset(session('logo_empresa'))}}" alt="AdminLTE Logo" class="" style="opacity: .8" width="100px">
            <p>{{session('nombre_general_empresa')}}</p>
		</div>
		<div class="col">
			<h3 class="d-inline"><i class="fa fa-calculator nav-icon mr-2"></i>Relacion Pago de Facturas </h3>
			<h3>{{session('empresaNombre')}} {{session('empresaRif')}}</h3>
		</div>
		
	</div>
    <div class="d-print-none">
        <h4>Reporte de la relacion de Pagos Por empresa y fecha</h4>
        <div class="row">
            <div class="col-10">
                <form action="{{route('resultadoReporteRelacionPagosPorEmpresa')}}" method="post" id="busca">
                    @csrf
                    <div class="row">
                        <div class="col">
                            <label>Fecha Inicio</label>
                            <input type="date" name="fechaIni" class="form-control">
                        </div>
                        <div class="col">
                            <label>Fecha Final</label>
                            <input type="date" name="fechaFin" class="form-control">
                        </div>
                        <div class="col">
                            <div class=" mt-4">
                                <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-search"></i>Buscar</button>
                            </div>
                            
                        </div>
                    </div>
                    
                    
                    
                </form>
            </div>
            <div class="col-2">
                <a href="#" onclick="javascript:window.print();"><i class="fa fa-print"></i>Imprimir</a>
            </div>
        </div>
			
	</div>
    <div>
        <!-- resultado de la busqueda -->
        @if(isset($pagos))
            <table class="table my-1 p-0">
                <thead>
                    <tr>
                        <th>Proveedor</th>
                        <th>Fecha Pago</th>
                        <th>Pago Bs.</th>
                        <th>Pago Divisa.</th>
                        <th>Firma</th>
                    </tr>
                </thead>
                
                <tboby>
                    @foreach($pagos as $pago)
                        <?php $sumaBs=0;$sumaDivisa=0; ?>
                        @foreach($pago->pagoProveedores as $pagoProveedor)
                        <tr>
                        <td>{{$pagoProveedor->proveedor_rif}} {{$pagoProveedor->proveedor_nombre}}
                        @if(!empty($cuenta->observacion))
								<span class="right badge badge-warning d-print-none">{{$cuenta->observacion}}</span>
								@endif
                        </td>
                        <td>{{$pagoProveedor->fecha_real_pago}}</td>
                        <td>{{number_format($pagoProveedor->pago_bolivares,2).' Bs.'}}</td>
                        <td>{{number_format($pagoProveedor->pago_divisas,2).' $'}}</td>
                        <td style=" border-bottom: 2px solid ;"></td>
                        </tr>
                        <?php $sumaBs += floatval($pagoProveedor->pago_bolivares);?>
                        <?php $sumaDivisa += floatval($pagoProveedor->pago_divisas);?>
                        @endforeach
                        <tr style="background-color: #D6DCD9;">
                            <td colspan='2' style="text-align: right">Total a la Fecha: {{$pago->fechaPagoAcordado}}</td>
                            <td>{{number_format($sumaBs,2).' Bs.'}}</td>
                            <td>{{number_format($sumaDivisa,2).' $'}}</td>
                            <td></td>
                        </tr>
                    @endforeach    
                </tbody>

            </table>
        @endif
    </div>
</div>
@endsection