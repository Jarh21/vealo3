@extends('layouts.app')
@section('css')

<link rel="stylesheet" type="text/css" href="{{asset('css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('css/select2.min.css')}}">
@endsection
@section('content')
	<div class="container-fluid">
		<h4>Retencion IVA {{session('empresaNombre')}} {{session('empresaRif')}}</h4>
		<hr>

		<div class="mb-3">				
			<a href="{{route('nuevafacturaporpagar.index')}}" class="btn btn-outline-success btn-sm mx-3 ">
			<i class="fas fa-file-invoice-dollar text-success" title="Ingresar Factura Manual"></i>Ingresar Factura Manual
    		</a>
			
		</div>
		<div class="mb-3">
    	<form action="{{route('optenerfacturasporpagar')}}" method="POST">
	      	@csrf
			
			
			Facturas del Libro de Compras del Siace.
				<div class="row">					
									
					<div class="col-5">
						<div class="form-group">
						<input type="text" id="nfactura" name="nfactura" class="form-control my-2" placeholder="Numero de factura del siace" autofocus title="Numeros de Facturas del Siace">
						<select name="proveedorRif" class="js-example-basic-single " style="width: 100%;" title="Seleccionar el proveedor de la facturas del siace" >
							<option value=""></option>
							@if(isset($proveedores))
							@foreach($proveedores as $proveedor)
								<option value="{{$proveedor->rif}}">{{$proveedor->rif}} {{$proveedor->nombre}}</option>		
							@endforeach
							@endif
						</select>
						</div>	
												
					</div>					
					
					<div class="col">
						<button type="submit" class="btn btn-primary btn-sm d-inline" title="buscar"><i class="fa fa-search"></i>Buscar</button>
						<a href="#info1" class="inf mx-2">+ Opciones</a>
					</div>	    				
				</div>
				
				<div id="info1" class="row well oculto">
					<div class="col">
						Observación
						<input type="text" name="observacion" id="observacion" placeholder='Observacion adicional' class='form-control'>
					</div>
									
					<div class="col">
						Importar el Libro desde
						<input type="date" name="fecha_cierre_ini" class="form-control" >
					</div>
					<div class="col">
						Importar el Libro hasta
						<input type="date" name="fecha_cierre_fin" class="form-control" >
					</div>					
					
				</div>   
					
		        
		</form>
		</div>
		@if(Session::has('message'))
			<div class="alert alert-danger">
				{!! Session::get('message') !!}
			</div>
   			
		@endif
    	    	
    	@if(!empty($mensaje))
    	<div class="alert {{$mensaje['tipo']}}">
    		<h4><i class="fa fa-exclamation-triangle"></i>{{$mensaje['texto']}}</h4>
    	</div>
    	@endif	
    	<table id="articulos" class="table table-bordered" data-page-length='25'>
    		<thead>
    			<tr>    				
    				<th>Nº</th>    				
    				<th>Proveedor</th>
    				<th>Nº Factura</th>
    				<th>Base</th>
    				<th>Iva</th>    					
    				<th>Total</th>
    			   	<th>Acción</th>
    			</tr>
    		</thead>
    		
            <tbody>
            
            </tbody>		 
    		
    		
    	</table><hr>
    		
		<div class="">			
			<button id="pagarcuentas" class="btn btn-primary my-2" disabled><i class="far fa-file-alt mx-2"></i>Generar Retención</button>
		</div>
    		
    	
    	<div><p>Tilde el recuadro de la factura que desee pagar, posteriormente dele click al boton "Pagar la Factura seleccionada" que se encuentra al final </p></div>	
	</div>
	
	
@endsection
@section('js')
	<script type="text/javascript">
		//focus en input
		$("#nfactura").focus();

		$(function () {
			//data table
			$('#articulos').DataTable({
			scrollY: 400,
			select: true,
			paging: false,
			searching: true,
			ordering:  true,
			language:{
				"search": "Buscar dentro del listado de facturas cargadas al sistema:"			
			}
			
			});
		});
	</script>
	<script type="text/javascript">
		/*function abrirModal(){
			
			$('#exampleModal').modal('show');
			
		}*/
		//mostart y ocultar div
		jQuery(document).ready(function(){
		$(".oculto").hide();              
			$(".inf").click(function(){
				var nodo = $(this).attr("href");  
		
				if ($(nodo).is(":visible")){
					$(nodo).hide();
					return false;
				}else{
				$(".oculto").hide("slow");                             
				$(nodo).fadeToggle("fast");
				return false;
				}
			});
		}); 
	</script>

	<script type="text/javascript">
	


	$(document).ready(function() {
		//hacer focus en el campo nfacturas del modal
		
		$('body').on('shown.bs.modal', '#exampleModal', function () {
    		//$(this).find(":input:not(:button):visible:enabled:not([readonly]):first").focus();
			document.getElementById("nfactura").focus();
		});
		
		// select 2
		$('.js-example-basic-single').select2({			
	    	placeholder: 'Seleccione el proveedor',    	
	    	/* maximumSelectionLength:1, */
	    });
		
		   	

	} );
	</script>

	<script type="text/javascript">
            $(".marcar").click(function() { 
                    $("input[type=checkbox]").prop("checked",true);
            })

            function isCheckBoxSeleccionado(id){
            	var checked = $(".CheckedAK:checked").length;/*optener la longitud de los check*/          	
            	var cuentaId = id;
            	var checkedId = 'check'+cuentaId;
            	var trId ='tr'+cuentaId;
            	if(document.getElementById(checkedId).checked == true){
            		/*contamnos la cantidad de elementos en el arreglo del checkbox 
            		si es mayor a 1 activamos el boton de pagar varias facturas*/
            		if(checked >=1){
            			document.getElementById("pagarcuentas").disabled = false;
            		}else{
            			document.getElementById("pagarcuentas").disabled = true;
            		}
            		           		
            		//agregamos la clase bg-primary al <tr>
            		document.getElementById(trId).className  += " bg-primary";
            	}else{

            		if(checked >=1){
            			document.getElementById("pagarcuentas").disabled = false;
            		}else{
            			document.getElementById("pagarcuentas").disabled = true;
            		}
            		//al deseleccionar borramos la clase bg-primary            		
            		document.getElementById(trId).className = document.getElementById(trId).className
    .replace(new RegExp('(?:^|\\s)'+ 'bg-primary' + '(?:\\s|$)'), '');
            	}
            	
            } 
            
	</script>
@endsection
