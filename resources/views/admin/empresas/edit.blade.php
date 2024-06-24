@extends('layouts.app')
@section('content')
<div class="container">
	<h3>Editar Empresa {{$empresa->nombre}} <a href="{{url('/admin/empresas')}}" class="btn btn-success float-right">Regresar</a></h3><hr>
	@if ($errors->any())
			    <div class="alert alert-danger">
			        <ul>
			            @foreach ($errors->all() as $error)
			                <li>{{ $error }}</li>
			            @endforeach
			        </ul>
			    </div>
			@endif
	

			
			
			<form action="{{route('admin.empresas.update',$empresa->id)}}" method="POST" accept-charset="UTF-8" enctype="multipart/form-data">
				@method('PUT')
				@csrf
				<div class="row">
					<div class="col-10">
						<div class="card">
							<div class="card-body">
								<p>Datos de la Empresa</p>
								<div class="row">
									<div class="col">
										<label>% RIF</label>
										<input type="text" class="form-control" name="rif"  placeholder="J-30631519-5" value='{{$empresa->rif}}'>
									</div>
									<div class="col">
										<label>Codigo</label>
										<input type="color" class="form-control" name="color" placeholder="1" value='{{$empresa->color}}'>
									</div>
								</div>
								<div class="row">
									<div class="col">
										<label>Nombre Corto</label>
										<input class="form-control" type="text" name="nom_corto" placeholder="FH" value='{{$empresa->nom_corto}}'>
									</div>
									<div class="col">
										<label>Nombre</label>
										<input type="text" class="form-control" name="nombre" placeholder="Farma ..." value='{{$empresa->nombre}}'>
									</div>
								</div>
								<div class="row">
									<div class="col">
										<label>Direccion</label>
										<textarea class="form-control" name="direccion" id="" cols="12" rows="3" >{{$empresa->direccion}}</textarea>
										
									</div>
								</div>
								<div class="row">
									<div class="col">
										<label>Telefono</label>
										<input type="text" class="form-control" name="telefono" placeholder="0414-0000000" value='{{$empresa->telefono}}'>
									</div>
									<div class="col">
										<div class="form-check">					
											<input type="checkbox" id="is_agente_retencion" class="form-check-input" name="is_agente_retencion" @if($empresa->is_agente_retencion==1) checked @endif>
											<label  for="is_agente_retencion">Es agente retención de impuestos (IVA)</label>
											<input type="text" name="providencia" value="{{$empresa->providencia_iva}}" class="form-control" placeholder="Providencia emitida por el seniat">
										</div>
									</div>
									
								</div>								
								<div class="row">
									<div class="col">
										<div class="form-group">
											<label>Firma Digital</label>
											<input type="file" class="form-control" name="firma">
											<img src="{{asset($empresa->firma)}}">					
										</div>
									</div>
									<div class="col">
										<div class="form-group">
											<label>Logo de la Empresa</label>
											<input type="file" class="form-control" name="logo">
											<img src="{{asset($empresa->logo)}}">					
										</div>
									</div>
								</div>
								
							</div>
						</div>
						
					</div>
				</div>
				<div class="row">	
					<div class="col-5">	
						<input type="checkbox" name="is_sincronizacion_remota" id="sincronizacion_remota" @if($empresa->is_sincronizacion_remota==1) checked @endif>
						<label for="sincronizacion_remota" title="esto es util cuando se esta trabajando con una base de datos local y se necesita traer los datos de la base de datos original">Sincronizacion Remota entre dos servidores</label>
						<div class="card">
							<div class="card-body">
							<p class="text-danger">Parametros de Conexión SEGUNDO SERVIDOR Vealo</p>
								<div class="form-group">
									<label for="" class="text-danger">Servidor 2 Vealo</label>
									<input type="text" name="servidor2" class="form-control" value="{{$empresa->servidor2}}">
									<label for="" class="text-danger">Puerto 2 Vealo</label>
									<input type="text" name="puerto2" class="form-control" value="{{$empresa->puerto2}}">
									<label for="" class="text-danger">Usuario 2 Vealo</label>
									<input type="password" name="nomusua2" class="form-control" value="{{$empresa->nomusua2}}">
									<label for="" class="text-danger">Clave 2 Vealo</label>
									<input type="password" name="clave2" class="form-control" value="{{$empresa->clave2}}">
									<label for="" class="text-danger">Base Datos 2 Vealo</label>
									<input type="text" name="basedata2" class="form-control" value="{{$empresa->basedata2}}">
								</div>
							</div>
						</div>
						
					</div>
					<div class="col-5">
						<div class="card">
							<div class="card-body">
							<p class="text-primary">Parametros de Conexión del SIACE</p>
								<div class="form-group">
									<label for="">Servidor</label>
									<input type="text" name="servidor" class="form-control" value="{{$empresa->servidor}}">
									<label for="">Puerto</label>
									<input type="text" name="puerto" class="form-control" value="{{$empresa->puerto}}">
									<label for="">Usuario</label>
									<input type="password" name="nomusua" class="form-control" value="{{$empresa->nomusua}}">
									<label for="">Clave</label>
									<input type="password" name="clave" class="form-control" value="{{$empresa->clave}}">
									<label for="">Base Datos</label>
									<input type="text" name="basedata" class="form-control" value="{{$empresa->basedata}}">
									
								</div>
							</div>
						</div>
						<button type="submit" class="btn btn-primary float-right">Editar</button>
					</div>
					
				</div>
				
				
			</form>
		
</div>
@endsection