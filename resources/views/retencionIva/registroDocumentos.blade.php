@extends('layouts.app')
@section('css')

<!-- <link rel="stylesheet" type="text/css" href="{{asset('css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('css/select2.min.css')}}"> -->
@endsection
@section('content')
	<div class="container-fluid">
		<h4>Retencion IVA  <a href="#" data-toggle="modal" data-target="#modalCambioSucursal" class="btn btn-outline-primary my-2">Seleccione sucursal ->{{session('empresaRif')}} {{session('empresaNombre') ?? 'No hay sucursal seleccionada'}}</a></h4>
		<hr>
		
		<!-- Modal sucursal -->
		<div class="modal fade" id="modalCambioSucursal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Sucursales</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				</div>
				<div class="modal-body">
				@if(isset($empresas))
					Seleccione la sucursal
					@foreach($empresas as $empresa)
						<!-- <option value="{{--$empresa->rif--}}|{{--$empresa->nombre--}}|{{--$empresa->basedata--}}">{{--$empresa->rif--}} {{--$empresa->nombre--}}</option> -->
						<a href="{{route('seleccionSucursal',$empresa->rif)}}" class="dropdown-item dropdown-footer">{{$empresa->rif}} {{$empresa->nombre}}</a>
						<div class="dropdown-divider"></div>
					@endforeach
				@endif
				</div>
				<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>				        		        
				
				</div>
			</div>
			</div>
		</div>	<!--fin modal-->	

		<!-- Modal cargando pagina-->
        <div class="modal fade"  id="mi-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Cargando Documento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Espere un momento por favor cargando...
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar progress-bar-striped " role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                
                </div>
            </div>
        </div><!-- fin Modal cargando pagina-->
			
		<div class="mb-3">				
			<a href="#facturaManual1" class="btn btn-outline-success btn-sm mx-3 abrirFacturaManual">
			<i class="fas fa-file-invoice-dollar text-success" title="Ingresar Factura Manual"></i>Ingresar Factura Manual
    		</a>
			
		</div>
		<div class="mb-3">
    	<form id='guardarDocumentoForm' action="{{route('retencion.iva.buscarFacturasSiace')}}" method="POST">
	      	@csrf
			
			
			Facturas del Libro de Compras del Siace.
				<div class="row">					
									
					<div class="col-8">
						<div class="row">
							<div class="col mt-2">
								<label for="">Proveedor</label>
								<select name="proveedorRif"  id="proveedorRif" class="js-example-basic-single " style="width: 100%;" title="Seleccionar el proveedor de la facturas del siace" >
								<option value=""></option>
								@if(isset($proveedores))
								@foreach($proveedores as $proveedor)
									<option value="{{$proveedor->rif}}|{{$proveedor->porcentaje_retener}}">{{$proveedor->rif}} {{$proveedor->nombre}} ({{$proveedor->porcentaje_retener ?? 'No tiene'}}%)</option>		
								@endforeach
								@endif
								</select>
							</div>
							<div class="col">
								<label for="">Numero de Documento</label>

								<input type="text" id="nfactura" required name="nfactura" class="form-control my-2" placeholder="Numero de factura del siace" autofocus title="Numeros de Facturas del Siace">
							</div>						
						
						</div>
						<div id="facturaManual1" class="ocultoFacturaManual " style="display: none">
							<form name="factura_manual" id="factura_manual" action="#" method="post">
								<div class="row border">
								
									<div class="col  mb-2">
										<label for="">Fecha Documento</label>
										<input type="date" id='fecha_docu' name='fecha_docu' class="form-control">
										<label for="">Tipo de Documento</label>
										<select name="tipo_docu" id="tipo_documento" class="form-control">
											<option value="FA">Factura</option>
											<option value="NC">Nota Credito</option>
											<option value="ND">Nota Debito</option>
										</select>
										
										<label for="factura_afectada" id="label_factura_afectada">Factura Afectada</label>
										<input id="factura_afectada" name='fact_afectada' type="text" class="form-control">
									</div>
									<div class="col">
										<label for="">Serie</label>
										<input type="text" name='serie' class="form-control">
										<label for="">Numero de Control</label>
										<input type="text" id='control_fact' name='control_fact' class="form-control">
										<label for="">Tipo Transacción</label>
										<input type="text" id='tipo_trans' name='tipo_trans' class="form-control">
									</div>

								</div>
								<div class="row border" style="background-color:#F6F5F3">
									<div class="col mb-2">
										<label for="">Total Compra + Iva</label>
										<input type="text" name="comprasmasiva" id="comprasmasiva" class="form-control">
										<label for="">Excento</label>
										<input type="text" name="sincredito" id="sincredito" class="form-control">
										<label for="">% Alicuota</label>
										<input type="text" name="porc_alic" id="porc_alic" class="form-control" value="{{$iva ?? 0}}" readonly>
										<label for="">% Retencion</label>
										<input type="text" name="porc_reten" id="porc_reten" class="form-control" readonly>

									</div>
									<div class="col">
										<label for="">Base Imponible</label>
										<input type="text" name='base_impon' id='base_impon' class="form-control" readonly>
										<label for="">Impuesto Iva</label>
										<input type="text" name='iva' id='iva' class="form-control" readonly>
										<label for="">IVA Retenido</label>
										<input type="text" name='iva_retenido' id='iva_retenido' class="form-control" readonly>
										<label for="">Factura Manual</label>
										<input type="checkbox" name="factura_import_manual" id="factura_import_manual"><br>
										<label for="">Tipo de Operacion</label>
										<label for="compra">Compra</label><input type="radio"  name="compra_venta" value="C" id="compra" @if($tipoOperacion=='C')checked @endif>
										<label for="venta">Venta</label><input type="radio"  name="compra_venta" value="V" id="venta" @if($tipoOperacion=='V')checked @endif>
										<button type="button" id='calcular' name='calcular' class="btn btn-sm btn-warning float-right mt-2">Calcular</button> 
									</div>
									
								</div>
								
								
								
								
							</form>
						</div>	
												
					</div>					
					
					<div class="col">
						<button type="submit" class="btn btn-primary btn-sm d-inline" id='guardarBtn' title="buscar" onclick="abrirModalCargando()"><i class="fa fa-search"></i>Guardar</button>
						<!-- <a href="#info1" class="inf mx-2">+ Opciones</a> -->
					</div>	    				
				</div>
				
				<div id="info1" class="row well oculto d-none">
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
			<div class="alert {!! Session::get('alert') !!}" id='alerta'>
				<button type="button" class="close" id='cerrarAlerta'aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				{!! Session::get('message') !!}
				
			</div>
			
   			
		@endif
    	<form action="{{route('retencion.iva.generar')}}" method="post"> 
			@csrf  	
			@if(!empty($mensaje))
			<div class="alert {{$mensaje['tipo']}}">
				<h4><i class="fa fa-exclamation-triangle"></i>{{$mensaje['texto']}}</h4>
			</div>
			@endif	
			<!-- <table id="articulos" class="table table-bordered" data-page-length='25'> -->
			<table id="" class="table table-bordered" data-page-length='25'>
				<thead>
					<tr>    				
						<th>Nº</th>    				
						<th>Proveedor</th>
						<th>Tipo <br>Docu</th>
						<th>Nº Factura</th>
						<th>Base Imponible</th>
						<th>Impuesto Iva</th>    					
						<th>IVA Retenido</th>
						<th>Acción</th>
					</tr>
				</thead>
				
				<tbody>
					@if(isset($registros))
						@foreach($registros as $registro)
						<tr id="tr{{$registro->keycodigo}}">
							<td>
								@if($registro->iva==0)
									<b class="text-danger">La factura no posee IVA</b>
								@else
									<input type="checkbox" id="check{{$registro->keycodigo}}" name="facturasPorRetener[]" onchange="isCheckBoxSeleccionado({{$registro->keycodigo}})" class="CheckedAK" value="{{$registro->keycodigo}}" @if(in_array($registro->keycodigo, session('documentos_seleccionados_iva', []))) checked @endif></td>
								@endif	
							<td>{{$registro->nom_retenido}} <span class="badge badge-secondary">{{$registro->porc_reten ?? '0'}}%</span> </td>
							<td>
								@if($registro->tipo_docu=='FA')
									Factura
								@endif
								@if($registro->tipo_docu=='NC')
									Nota Credito
								@endif
								@if($registro->tipo_docu=='ND')
									Nota Debito
								@endif
							</td>
							<td>{{$registro->documento}}</td>
							<td>{{$registro->base_impon}}</td>
							<td>{{$registro->iva}}</td>
							<td>{{$registro->iva_retenido}}</td> 
							<td>
							<a href="{{route('retencion.iva.editarDocumento',$registro->keycodigo)}}" onclick="centeredPopup(this.href, 'myWindow', 700, 750); return false;" class="btn btn-secondary btn-sm">Editar</a>

								<button type='button' id='eliminarBtn{{$registro->keycodigo}}' class='btn btn-danger btn-sm' onclick="eliminar('{{$registro->keycodigo}}')">Eliminar</button>
							</td>
							
						</tr>
						
						@endforeach
					@endif
				</tbody>		 
				
				
			</table><hr>
				
			<div class="">			
				<button id="pagarcuentas" class="btn btn-primary my-2" disabled><i class="far fa-file-alt mx-2"></i>Generar Retención</button>
			</div>
    	</form> <!-- fin del formulario que toma las facturas seleccionadas y genera la retencion -->	
    	
    	<div><p>Tilde el recuadro de la factura que desee pagar, posteriormente dele click al boton "Pagar la Factura seleccionada" que se encuentra al final </p></div>	
	</div>
	
	
@endsection
@section('js')


	<script type="text/javascript">
		
		/********************validamos campos del formulario registro de facturas manuales*********************************/
		/*ocultamos los campos*/
		$("#factura_afectada").prop( "disabled", true );
		$("#label_factura_afectada").prop( "disabled", true );
		/********************* que hacer cuando se seleccion el tipo de documento ***********************************************/
		$("#tipo_documento").change(function(){
			let valorTipoDocumento = document.getElementById('tipo_documento');/**se trae el valor del select */
			if(valorTipoDocumento.value != 'FA'){
				/**Mostramos si no es factura */
				$("#factura_afectada").prop( "disabled", false );
				$("#label_factura_afectada").prop( "disabled", false );
				let requerir = document.getElementById('factura_afectada');
				requerir.require('true');
			}else{
				/**si no es favtura */
				$("#factura_afectada").prop( "disabled", true );
				$("#label_factura_afectada").prop( "disabled", true );
			}
			
		});
		/******optener el porcentaje de retencion del proveedor del selec y colocarlo en el campo % Retencion del formulario******** */
		$("#proveedorRif").change(function(){
			let proveedor = document.getElementById('proveedorRif');
			let datosProveedor = proveedor.value.split('|');
			let porcentajeRetencion = document.getElementById('porc_reten');
			porcentajeRetencion.value = datosProveedor[1];
		});
		/*******************************cerrar el alert********************************************************* */
		$("#cerrarAlerta").click(function(){
			//cerramos el alerta que indica si un archivo fue cargado o no se cargo			
			$("#alerta").hide();
		});
		/***************************Calcular Montos de Factura******************************************************* */
		$("#calcular").click(function(){
			
			/*validamos que le monto total no este vacio*/
			if(document.getElementById('comprasmasiva').value == ''){
				alert("Debe ingresar el monto Total de la Factura");
				document.getElementById('comprasmasiva').focus();
				return;
			}
			/*validamos que iva alicuota no este vacio*/
			if(document.getElementById('porc_alic').value == ''){
				alert("Debe ingresar el valor del IVA, alicuota, no de be ser vacío");
				document.getElementById('porc_alic').focus();
				return;
			}
			
			/*validamos que iva alicuota no este vacio*/
			if(document.getElementById('sincredito').value == ''){
				alert("Debe ingresar el valor del monto exento de lo contrario colocar 0");
				document.getElementById('sincredito').focus();
				return;
			}
			/*validamos que le monto porcentaje re retencion 100 o 75 no este vacio*/
			if(document.getElementById('porc_reten').value == ''){
				alert("El proveedor seleccionado no tiene registrado cuanto porcentaje de retención se le calculara, por lo tanto modifique esta informacion en el proveedor para continuar.");
				document.getElementById('porc_reten').focus();
				return;
			}
					

			TC=parseFloat(document.getElementById('comprasmasiva').value);	
			CSC=parseFloat(document.getElementById('sincredito').value);
			ALI=parseFloat(document.getElementById('porc_alic').value);
			RET=parseFloat(document.getElementById('porc_reten').value);

			/*validamos que si la factura no tiene excento se coloque en 0 para el calculo */
			if(document.getElementById('sincredito').value == ''){				
				CSC = 0;
			}else{				
				CSC=parseFloat(document.getElementById('sincredito').value);
			}

			/**Validamos que el monto excento no sea mayor que el monto total de la factura */	
			if(CSC >= TC) {
				alert("El Monto Excento = "+CSC+" no debe ser mayor o igual al Total de la Compra = "+TC);				
				return;
			}

			Resultado=((TC-CSC)/(1+(ALI/100)));
			IVA = (Resultado*(ALI/100)).toFixed(2);	
			document.getElementById('base_impon').value = (Resultado).toFixed(2);
			document.getElementById('iva').value = IVA;
			document.getElementById('iva_retenido').value=((IVA*RET)/100).toFixed(2);

			$('#base_impon').prop('readonly', true);
			$('#iva').prop('readonly', true);
			$('#iva_retenido').prop('readonly', true);
			$('#guardarBtn').prop('disabled',false);

		});

/************************************************************************ */
		function eliminar(id){
			let boton ='#eliminarBtn'+id;
			let confirmar = confirm("¿Confirma eliminar la factura seleccionada ?");
			if(confirmar){
				$(boton).prop('disabled', true); 
				window.location = "eliminar-factura/"+id;
			}
		}
/********************************************************************************************** */
		//focus en input
		$("#nfactura").focus();
/************************************************************************************************ */
		$(function () {
			//data table
			$('#articulos').DataTable({
			scrollY: 350,
			select: true,
			paging: false,
			searching: true,
			ordering:  true,
			language:{
				"search": "Buscar dentro del listado de facturas cargadas al sistema:"			
			}
			
			});
		});
/******************************************************************************************************** */
		//mostart y ocultar div
		jQuery(document).ready(function(){
			/**si carga la pagina y hay un check habilitamos el boton Generar Retencion */
			if ($('.CheckedAK').is(':checked')) {
				document.getElementById("pagarcuentas").disabled = false;
			}	
			$(".oculto").hide();
			$(".ocultoFacturaManual").hide();
			/***************cuando presione el signo + y despliega otras opciones del formulario******** */
			$(".inf").click(function(){
				var nodo = $(this).attr("href");  
		
				if ($(nodo).is(":visible")){
					//repliegue del formuladio
					$(nodo).hide();
					return false;
				}else{
				//despliegue del formulario
				$(".oculto").hide("slow");                             
				$(nodo).fadeToggle("fast");				
				return false;
				}
			});
			/********************cuando envias el documento deshabilita el boton guardar******************** */
			$("#guardarDocumentoForm").submit(function(){
				/*******************validamos los campos del formulario dependiendo si es manual o importado */
				 if ($('#factura_import_manual').is(':checked')) {

					
				
					if ($('#base_impon').val() === '') {
						alert('Antes de enviar el formulario debes calcular los montos, preciona el boton amarillo calcular');
						event.preventDefault(); // Evita que el formulario se envíe
					}
				}
				$('#mi-modal').modal('show'); // Muestra el modal con fade
				$('.progress-bar').animate({width:'95%'}, 4000); // Cambia 1000 por la duración deseada en milisegundos				
				 	
				$('#guardarBtn').prop('disabled',true);
			});
/************************************Abrir factura Manual*********************************************************** */
			$(".abrirFacturaManual").click(function(){
				var nodo = $(this).attr("href");  
		
				if ($(nodo).is(":visible")){
					//oculto
					$(nodo).hide();
					$('#factura_import_manual').prop('checked', false);
					$('#proveedorRif').removeAttr('required');
					$('#proveedorRif').removeAttr('required');
					$('#fecha_docu').removeAttr('required');
					$('#control_fact').removeAttr('required');										
					$('#comprasmasiva').removeAttr('required');
					$('#porc_alic').removeAttr('required');
					$('#porc_reten').removeAttr('required');

					return false;
				}else{
					//visible
					$(".ocultoFacturaManual").hide("slow");                             
					$(nodo).fadeToggle("fast");
					$('#factura_import_manual').prop('checked', true);
					$('#proveedorRif').attr('required', 'required');
					$('#fecha_docu').attr('required', 'required');
					$('#control_fact').attr('required', 'required');										
					$('#comprasmasiva').attr('required', 'required');
					$('#porc_alic').attr('required', 'required');
					$('#porc_reten').attr('required', 'required');
					
					
					return false;
				}
			});
/****************************************************************************************************** */
			// select 2
			$('.js-example-basic-single').select2({			
				placeholder: 'Seleccione el proveedor',    	
				/* maximumSelectionLength:1, */
			});
/******************************************************************************************************** */
			$(".marcar").click(function() { 
                $("input[type=checkbox]").prop("checked",true);
            })
/******************************************************************************************************* */
            
		}); 
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
				document.getElementById(trId).className  += " bg-info";
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

<script>
	/* function centeredPopup(url, winName, w, h) {
		//centar la ventana pop up
		const dualScreenLeft = window.screenLeft !== undefined ? window.screenLeft : window.screen.left;
		const dualScreenTop = window.screenTop !== undefined ? window.screenTop : window.screen.top;

		const width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
		const height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

		const left = (width / 2) - (w / 2) + dualScreenLeft;
		const top = (height / 2) - (h / 2) + dualScreenTop;

		const newWindow = window.open(url, winName, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);

		if (window.focus) {
			newWindow.focus();
		}
	} */
</script>

@endsection
