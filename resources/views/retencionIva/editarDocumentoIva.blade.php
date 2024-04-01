<link href="{{ asset('css/app.css') }}" rel="stylesheet">

<div id="app" class="container-fluid">
<form name="editarDocumentoForm" id="editarDocumentoForm" action="{{route('retencion.iva.updateDocumento')}}" method="post">
    <div class="row">		
        <div class="col">
            <div class="row">
                <div class="col mt-2">
                    <label for="">Proveedor</label>
                    <select name="proveedorRif"  id="proveedorRif" class="js-example-basic-single " style="width: 100%;" title="Seleccionar el proveedor de la facturas del siace" >
                    <option value=""></option>
                    @if(isset($proveedores))
                        @foreach($proveedores as $proveedor)
                            <option value="{{$proveedor->rif}}|{{$proveedor->porcentaje_retener}}|{{$proveedor->nombre}}" @if($proveedor->rif==$documento->rif_retenido)selected @endif>{{$proveedor->rif}} {{$proveedor->nombre}} ({{$proveedor->porcentaje_retener ?? 'No tiene'}}%)</option>		
                        @endforeach
                    @endif
                    </select>
                </div>
                <div class="col">
                    <label for="">Numero de Documento</label>
					<input type="hidden" name="keycodigo" value="{{$documento->keycodigo}}">
                    <input type="text" id="nfactura" required name="nfactura" class="form-control my-2" value="{{$documento->documento ?? ''}}">
                </div>						
            
            </div>
            
                
			@csrf
			<div class="row border">
			
				<div class="col  mb-2">
					<label for="">Fecha Documento</label>
					<input type="date" id='fecha_docu' name='fecha_docu' class="form-control" value="{{$documento->fecha_docu}}" required>
					<label for="">Tipo de Documento</label>
					<select name="tipo_docu" id="tipo_documento" class="form-control">
						<option value="FA" @if($documento->tipo_docu=='FA')selected @endif >Factura</option>
						<option value="NC" @if($documento->tipo_docu=='NC')selected @endif >Nota Credito</option>
						<option value="ND" @if($documento->tipo_docu=='ND')selected @endif >Nota Debito</option>
					</select>
					
					<label for="factura_afectada" id="label_factura_afectada">Factura Afectada</label>
					<input id="factura_afectada" name='fact_afectada' type="text" class="form-control" value="{{$codumento->fact_afectada ?? ''}}">
				</div>
				<div class="col">
					<label for="">Serie</label>
					<input type="text" name='serie' class="form-control" value="{{$documento->serie ?? ''}}">
					<label for="">Numero de Control</label>
					<input type="text" id='control_fact' name='control_fact' value="{{$documento->control_fact ?? ''}}" class="form-control">
					<label for="">Tipo Transacción</label>
					<input type="text" id='tipo_trans' name='tipo_trans' value="{{$documento->tipo_trans ?? ''}}" class="form-control">
				</div>

			</div>
			<div class="row border" style="background-color:#F6F5F3">
				<div class="col mb-2">
					<label for="">Total Compra + Iva</label>
					<input type="text" name="comprasmasiva" id="comprasmasiva" value="{{$documento->comprasmasiva ?? ''}}" class="form-control" required>
					<label for="">Excento</label>
					<input type="text" name="sincredito" id="sincredito" value="{{$documento->sincredito ?? ''}}" class="form-control">
					<label for="">% Alicuota</label>
					<input type="text" name="porc_alic" id="porc_alic" class="form-control" value="{{$iva ?? 0}}">
					<label for="">% Retencion</label>
					<input type="text" name="porc_reten" id="porc_reten" value="{{$documento->porc_reten}}" class="form-control" required>

				</div>
				<div class="col">
					<label for="">Base Imponible</label>
					<input type="text" name='base_impon' id='base_impon' value="{{$documento->base_impon ?? ''}}" class="form-control" readonly required>
					<label for="">Impuesto Iva</label>
					<input type="text" name='iva' id='iva' value="{{$documento->iva ?? ''}}" class="form-control" readonly>
					<label for="">IVA Retenido</label>
					<input type="text" name='iva_retenido' id='iva_retenido' value="{{$documento->iva_retenido ?? ''}}" class="form-control" readonly>
					<label for="">Tipo de Operacion</label>
					<label for="compra">Compra</label><input type="radio"  name="compra_venta" value="C" id="compra" @if($documento->estatus=='C')checked @endif>
					<label for="venta">Venta</label><input type="radio"  name="compra_venta" value="V" id="venta" @if($documento->estatus=='V')checked @endif>
					<button type="button" id='calcular' name='calcular' class="btn btn-sm btn-warning float-right mt-2">Calcular</button> 
				</div>                   
			</div>               
                
            <div class="">
            <button type="submit" class="btn btn-primary btn-sm d-inline float-right my-2" id='guardarBtn' title="buscar"><i class="fa fa-search"></i>Guardar</button>        
        </div>
                                    
        </div>					
        
        	    				
    </div>
    </form>
</div>

<script src="{{ asset('js/app.js')}}"></script>


	<script type="text/javascript">
		// select 2
		$('.js-example-basic-single').select2({			
				placeholder: 'Seleccione el proveedor',    	
				/* maximumSelectionLength:1, */
			});
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
				requerir.required('true');
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
				alert("Debe ingresar el Porcentaje de retención del Impuesto del proveedor 100 o 75, si selecciona el proveedor este dato se cargara automaticamente");
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
				alert("El Monto Excento"+CSC+" no debe ser mayor o igual al Total de la Compra !"+TC);				
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

		/********************cuando envias el documento deshabilita el boton guardar******************** */
		$("#editarDocumentoForm").submit(function(){			
			
			if ($('#base_impon').val() === '') {
				alert('Antes de enviar el formulario debes calcular los montos, preciona el boton amarillo calcular');
				event.preventDefault(); // Evita que el formulario se envíe
			}
			if($('#tipo_documento').val() != 'FA' && $("#factura_afectada")===''){
				alert('El campo Factura Afectada es requerido ya que es una nota de credito o debito que se esta registrando');
				event.preventDefault(); // Evita que el formulario se envíe					
			}	
			$('#guardarBtn').prop('disabled',true);
		});
		
	</script>
