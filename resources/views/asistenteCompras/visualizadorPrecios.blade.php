@extends('layouts.app')

@section('content')
    <h3>Visualizador de Precios</h3>
    @php 
    $fecha = '2023-10-27';
		$dia_semana = date('l', strtotime($fecha));
		dd($dia_semana);
    @endphp
    <p>Comparar los precios de los productos por drogueria.</p>
    <a href="{{route('asistentecompra.descargarExcel')}}" class="btn btn-success">Descargar Excel</a>
        <visualizador-precios></visualizador-precios>
        
  

@endsection
@section('js')


	
@endsection