@extends('layouts.app')
@section('content')
<div class="container">
	<h3>Registrar De Empresas <a href="{{url('/empresas')}}" class="btn btn-success float-right">Regresar</a></h3><hr>
	@if ($errors->any())
		<div class="alert alert-danger">
			<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif
	<form action="{{route('empresas.save')}}" method="POST">
		@csrf
		<div class="row">
			<div class="col-8">
				<div class="card">
					<div class="card-body">
						<p>Datos de la Empresa</p>
						<div class="row">
							<div class="col">
								<label>% RIF</label>
								<input type="text" class="form-control" name="rif"  placeholder="J-3063....-5" >
							</div>
							<div class="col">
								<label>Codigo</label>
								<input type="color" class="form-control" name="color" placeholder="1" >
							</div>
						</div>
						<div class="row">
							<div class="col">
								<label>Nombre Corto</label>
								<input class="form-control" type="text" name="nom_corto" placeholder="..tres letras .." >
							</div>
							<div class="col">
								<label>Nombre</label>
								<input type="text" class="form-control" name="nombre" placeholder="" >
							</div>
						</div>
						<div class="row">
							<div class="col">
								<label>Direccion</label>
								<textarea class="form-control" name="direccion" id="" cols="12" rows="3" ></textarea>
								
							</div>
						</div>
						<div class="row">
							<div class="col">
								<label>Telefono</label>
								<input type="text" class="form-control" name="telefono" placeholder="0247-0000..." >
							</div>
							<div class="col">
								<div class="form-check">					
									<input type="checkbox" id="is_agente_retencion" class="form-check-input" name="is_agente_retencion" >
									<label  for="is_agente_retencion">Es agente retención de impuestos (IVA)</label>
								</div>
							</div>
							
						</div>								
						
						<div class="form-group">
							<label>Firma Digital</label>
							<input type="file" class="form-control" name="firma">
							<img src="">					
						</div>
					</div>
				</div>
				
			</div>
			<div class="col-4">
				<div class="card">
					<div class="card-body">
					<p>Parametros de Conexión del SIACE</p>
						<div class="form-group">
							<label for="">Servidor</label>
							<input type="text" name="servidor" class="form-control" >
							<label for="">Puerto</label>
							<input type="text" name="puerto" class="form-control" >
							<label for="">Usuario</label>
							<input type="password" name="nomusua" class="form-control" >
							<label for="">Clave</label>
							<input type="password" name="clave" class="form-control" >
							<label for="">Base Datos</label>
							<input type="text" name="basedata" class="form-control" >
							<input type="checkbox" name="is_sincronizacion_remota" id="sincronizacion_remota">
									<label for="sincronizacion_remota" title="esto es util cuando se esta trabajando con una base de datos local y se necesita traer los datos de la base de datos original">Sincronizacion Remota entre dos servidores</label>
						</div>
					</div>
				</div>
				<div class="card">
					<div class="card-body">
					<p>Parametros de Conexión SEGUNDO SERVIDOR</p>
						<div class="form-group">
							<label for="">Servidor2</label>
							<input type="text" name="servidor2" class="form-control" >
							<label for="">Puerto2</label>
							<input type="text" name="puerto2" class="form-control" >
							<label for="">Usuario2</label>
							<input type="password" name="nomusua2" class="form-control" >
							<label for="">Clave2</label>
							<input type="password" name="clave2" class="form-control" >
							<label for="">Base Datos2</label>
							<input type="text" name="basedata2" class="form-control" >
						</div>
					</div>
				</div>
				<button type="submit" class="btn btn-primary float-right">Guardar</button>
			</div>
		</div>
	</form>
</div>
@endsection