<link href="{{ asset('css/app.css') }}" rel="stylesheet">

<div id="app" class="container-fluid">
	<div class="container">
		<div class="card my-3">
			<div class="card-header">
				<h4>Sucursales</h4>
			</div>
			<div class="card-body">
				<form action="{{route('cuentasporpagar.guardarSeleccionEmpresa')}}" method="post">
					@csrf
					<input type='hidden' value="{{$rutaSolicitante ?? ''}}" name='ruta_solicitante'>
					<div class="form-group">
						<label>Seleccione la sucursal a trabajar</label>
						<select name="empresa" class="form-control" required>
							<option value="">-- Seleccionar --</option>
							@foreach($empresas as $empresa)
							<option value="{{$empresa->rif}}|{{$empresa->nombre}}|{{$empresa->basedata}}|{{$empresa->logo}}" @if(session('empresaRif') == $empresa->rif) selected @endif>{{$empresa->rif}} {{$empresa->nombre}}</option>
							@endforeach
						</select>
						
					</div>
					<div class="form-group">
						<label>Modo de Pago</label>
						<select name="modo_pago" class="form-control" required>
							<option value="">-- Seleccione --</option>
							<option value="1|bolivares" @if(session('modoPago')=='bolivares') selected @endif>Bolivares <b class="text-primary">Bs.</b></option>
							<option value="2|dolares" @if(session('modoPago')=='dolares') selected @endif>Divisas <b class="text-success">$</b></option>
						</select>
					</div>
					<button type="submit" class="btn btn-primary">Seleccionar</button>
				</form>
				
			</div>
		</div>
	</div>
</div>	
<script src="{{ asset('js/app.js')}}"></script>
<script>
	function centeredPopup(url, winName, w, h) {
		/*centar la ventana pop up*/
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
	}
</script>