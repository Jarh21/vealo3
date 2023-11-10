@extends('layouts.app')

@section('content')
<style>
	.alternar:hover{ background-color:#C1BDBC;}
</style>
<div class="container">
	<H4>Crear nueva Lista de Habladores<a href="{{route('habladores.index')}}" class="float-end float-right"><< Regresar</a></H4>
	@if(Session::has('message'))
		<div class="alert alert-danger">
			{!! Session::get('message') !!}
		</div>
		
	@endif
	<div class="card">
		
		<form name="tipoProducto" id="tipoProducto" action="{{route('cambiarTipoProductoParaCrearLista')}}" method="post">
			@csrf
		<div class="card-header">
			<div class="row">
				<div class="col">
					Seleccione Tipo de Producto 
				</div>
				<div class="col">
				@if(isset($tiposProductos))
				<select name="tipo_producto" id="" class="form-control" onchange="document.tipoProducto.submit();" require >

					<option value="" >--Seleccione--</option>
					@foreach($tiposProductos as $tipoProducto)
					<option value="{{$tipoProducto->keycodigo}}" @if($tipoProductoSelec == $tipoProducto->keycodigo) selected @endif>{{$tipoProducto->nombre}}</option>
					@endforeach
				</select>
				@else
					<b class="text-danger">No hay tipo de producto en base de datos siace</b>
				@endif
				</div>
			</div>
			
			
		</div>
		</form>
		<form  action="{{route('guardarListaCreada')}}" method="post">
			@csrf
		<div class="card-body">
			<div class="row">
				<div class="col">
					<label for=""> Escriba el nombre de la nueva lista</label>	
				</div>
				<div class="col">						
					<input type="text" name="nombre_lista" value="{{ session('nombreListaHablador') ?? ''}}" class="form-control" required>
				</div>			
				<div class="col">
					<button class="btn btn-primary ">Guardar</button>
				</div>
			</div>
			<table class="table " id="tablaproductos">
				<thead>
					<tr >
						<th>Productos</th>						
						<th>Lista</th>
						<th>Tipo Producto</th>
						
					</tr>
				</thead>
				<tbody>
					@if(isset($productos))
						@foreach($productos as $producto)
						@php $idcheck = 'producto'.$producto->codprod; @endphp
						<tr style="font-size:13px;" class="alternar" onclick="selectCelda('{{$idcheck}}');">
							<td ><input id="producto{{$producto->codprod}}" type="checkbox" name="codprod[]" value="{{$producto->codprod}}"><label class="mx-2" for="{{$producto->codprod}}">{{$producto->codprod}} {{$producto->nombre}}({{$producto->tipoiva}}) <span class="text-primary">{{$producto->linea}}</span><span class="text-success"> {{$producto->sublinea}}</span></label></td>
							<td>{{$producto->listas ?? ''}}</td>
							<td>{{$producto->tipoproducto}}</td>
							
						</tr>
						
						@endforeach
					@endif
				</tbody>
			</table>
		</div>
		
		</form>
		<div>
		<p class="mx-2 text-danger"><b>Nota:</b> una vez seleccionado todos lo productos asegurece de que no tenga ninguna palabra filtrando el listado, esto es el la casilla que dice Buscar: ya que si hay algun filtro no se guardaran el resto de productos pre-seleccionado.</p>
		</div>
		
	</div>
</div>
@endsection
@section('js')

<script type="text/javascript">
	$(document).ready(function() {
		//hacer focus en el campo nfacturas del modal
				
		//data table
		$('#tablaproductos').DataTable({
			scrollY: 350,
			paging: false,
			language:{
			"search": "Buscar:"			
		}
		});   	

	} );
</script>

<script type="text/javascript">
        function selectCelda(id){
            let checkbox = document.getElementById(id);
            if(checkbox.checked == true){
                checkbox.checked = false;

            }else{
                checkbox.checked =true;
            }
            
        }
    </script> 

@endsection