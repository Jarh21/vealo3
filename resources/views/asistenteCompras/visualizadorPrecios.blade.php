@extends('layouts.app')

@section('content')
    <h3>Visualizador de Precios</h3>
    <p>Comparar los precios de los productos por drogueria.</p>
    <a href="{{route('asistentecompra.descargarExcel')}}" class="btn btn-success">Descargar Excel</a>
        <visualizador-precios></visualizador-precios>
        
  

@endsection
@section('js')


	
@endsection