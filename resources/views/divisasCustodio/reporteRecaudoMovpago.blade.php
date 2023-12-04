@extends('layouts.app')
@section('content')
	<div class="container-fluid">
		<h3>Reporte Operaciones Divisas Recaudo SIACE</h3>
		<h4>Empresa {{session('empresaRif')}} {{session('empresaNombre')}}</h4>
		<listar-asesor-por-fecha></listar-asesor-por-fecha>
	</div>
@endsection
@section('js')


@endsection