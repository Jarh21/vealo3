<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<div id="app" class="container-fluid">
    <h4>Notas De Credito Pendientes Por Descontar</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Rif</th>
                <th>Proveedor</th>
                <th>Fecha Nota Cred.</th>
                <th>Documento Nota Cred.</th>                
                <th>Monto</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            @foreach($notasDeCreditos as $notaDeCredito)
            <tr>
                <td>{{$notaDeCredito->proveedor_rif}}</td>
                <td>{{$notaDeCredito->proveedor_nombre}}</td>
                <td>{{$notaDeCredito->cierre}}</td>
                <td>{{$notaDeCredito->documento}}</td>                
                <td>{{$notaDeCredito->creditos}}</td>
                <td><a href="{{route('cuentasporpagar.agregarNotaCreditoPorDescontar',[$notaDeCredito->id,$factura_id,$codigoRelacion])}}" class="btn btn-primary">Agregar</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
</div>