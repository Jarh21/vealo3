@extends('layouts.app')
@section('css')


@endsection
@section('content')
<h4>Listado Retencion IVA <a href="{{route('retencion.iva.index')}}" class='btn btn-warning btn-sm float-right'>< Regresar</a></h4><hr>

<div>
    <table class='table'>
        <thead>
            <tr>
                <td>Empresa</td>
                <td>Fecha</td>
                <td>Proveedor</td>
                <td>Rif Provee</td>
                <td>Factura</td>
                <td>Comprobante</td>
                <td>Opciones</td>
            </tr>
        </thead>
        <tbody>
            @foreach($retenciones as $retencion)
                <tr>
                    <td>{{$retencion->nom_agente}}</td>
                    <td>{{$retencion->fecha_docu}}</td>
                    <td>{{$retencion->nom_retenido}}</td>
                    <td>{{$retencion->rif_retenido}}</td>
                    <td>{{$retencion->documento}}</td>
                    <td>{{$retencion->comprobante}}</td>
                    <td>
                        <a href="{{route('retencion.iva.generar_comprobante',$retencion->comprobante)}}" class='btn btn-secondary btn-sm'><i class="fas fa-file-pdf"></i> ver</a>
                        <a href="#" class='btn btn-warning btn-sm'>Editar</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection
@section('js')

<script type="text/javascript">

	$(document).ready(function() {	
		
			$('#retenciones').DataTable({
		    scrollY: 500,
		    select: true,
		    paging: true,
		    searching: true,
    		ordering:  false
			});			
    	

	} );
</script>
@endsection