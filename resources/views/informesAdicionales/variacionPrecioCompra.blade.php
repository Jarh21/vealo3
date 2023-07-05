@extends('layouts.informes')
@section('css')
<link rel="stylesheet" type="text/css" href="{{asset('css/select2.min.css')}}">
@endsection

@section('content')
    <div class='container'>
        <h3>Productos Con variacion de costos</h3>
        <form action="{{route('buscarProductosVariacionPrecioCompra')}}" method="post" name="formulario1">
            @csrf
            <span class="mr-2" >Productos</span>
            <select name="codigo_productos[]" class="js-example-basic-single" style="width: 75%"; multiple="multiple">
                <option value=""></option>
                @if(isset($productos))
                   
                    @foreach($productos as $producto)
                        <option value="{{$producto->codprod}}"
                         
                        >
                        {{$producto->codprod}} {{$producto->nombre}}
                        </option>		
                    @endforeach
                @endif
            </select>
            <a href="javascript:enviar_formulario()"><i class="fa fa-search"></i></a>
        </form>
    </div>
    <div class='continer'>
        @if(isset($compras))
            <table>
                @foreach($compras as $compra)
                    @foreach($compra as $resultado)
                    <tr>
                        <td>{{$resultado->fecha}}</td>
                        <td>{{$resultado->proveedor}}</td>
                        <td>{{number_format($resultado->costo,2)}}</td>
                    </tr>
                    @endforeach
                @endforeach
            </table>
        @endif
    </div>
@endsection
@section('js')
<script src="{{asset('js/select2.min.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // select 2
		$('.js-example-basic-single').select2({
	    	placeholder: 'Seleccione los productos',    	
	    	maximumSelectionLength:100,
	    });
    });
</script>
<script>
    function enviar_formulario(){
        document.formulario1.submit();
    }
</script>
@endsection

