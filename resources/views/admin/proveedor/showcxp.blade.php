@extends('layouts.app')
@section('content')
	
	<div class="jumbotron">
	  <h1 class="display-4">Proveedor: {{$proveedor->nombre}}</h1>
	  
	   <hr class="my-4">
	  <p class="lead">Rif: {{$proveedor->rif}}</p>
	  <p class="lead">Persona: {{$proveedor->tipo_contribuyente}}</p>
	  <p class="lead">Direccion: {{$proveedor->direccion}}</p>		
	  <a class="btn btn-primary btn-lg" href="{{route('proveedor.index','proveedor.indexcxp')}}" role="button">Regresar</a>
	</div>
@endsection