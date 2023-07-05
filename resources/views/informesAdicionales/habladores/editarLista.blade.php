@extends('layouts.app')

@section('content')
<style>
	.alternar:hover{ background-color:#C1BDBC;}
</style>
<div class="container">
	<H4>Agregar a la Lista de Habladores {{$listado}}<a href="{{route('habladores.index')}}" class="float-end float-right"><< Regresar</a></H4>
	<div class="card">
		
		<form name="tipoProducto" id="tipoProducto" action="{{route('cambiarTipoProductoParaEditarLista')}}" method="post">
			@csrf
            <input type="hidden" name="listado" value="{{$listado}}">
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
            <input type="hidden" name="nombre_lista" value="{{$listado}}" class="form-control" >
		<div class="card-body">
			<div class="row">						
				<div class="col">
                <button class="btn btn-primary btn-sm float-right mb-1">Guardar</button>
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
						<tr style="font-size:13px;" class="alternar" onclick="selectCelda('{{$idcheck}}');" >
							<td >
								
								@php
									$enLista=0; 
									if($producto->listas <>''){
										$datosListas = explode('-',$producto->listas);
										
										foreach($datosListas as $datos){
											if($datos == $listado ):
												$enLista=1;
											
											endif;
										}
									}	
								@endphp
								
								<input id="producto{{$producto->codprod}}" type="checkbox" name="codprod[]" value="{{$producto->codprod}}" @if($enLista==1)disabled @endif>
								
								{{$producto->codprod}} {{$producto->nombre}}({{$producto->tipoiva}}) <span class="text-primary">{{$producto->linea}}</span><span class="text-success"> {{$producto->sublinea}}</span></td>
							<td>{{$producto->listas ?? ''}}</td>
							<td>{{$producto->tipoproducto}}</td>
							
						</tr>
						
						@endforeach
					@endif
				</tbody>
			</table>
            
		</div>
		
		</form>
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