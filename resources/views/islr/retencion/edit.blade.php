@extends('layouts.app')
@section('content')
<div class="container">
	<h3>Modificar Determinacion de la Retencion <a href="{{url('/retencion')}}" class="btn btn-warning float-right">Regresar</a></h3><hr>
	<div class="row">
		<div class="col-6">

			@if ($errors->any())
			    <div class="alert alert-danger">
			        <ul>
			            @foreach ($errors->all() as $error)
			                <li>{{ $error }}</li>
			            @endforeach
			        </ul>
			    </div>
			@endif
			
			<form action="{{route('retencion.update',$retencion->id)}}" method="POST">
				@method('PUT')
				@csrf
				<div class="form-group">
					<label>% Retencion</label>
					<p>{{$retencion->procent_retencion}}</p>
				</div>
				<div class="form-group">
					<label>Valor U.T</label>
					<input type="text" class="form-control" name="valorUT" value="{{$retencion->valorUT}}" required>
				</div>
				<div class="form-group">
					<label>Factor</label>
					<input class="form-control" type="text" name="factor" value="{{$retencion->factor}}" required>
				</div>
				<div class="form-group">
					<label>Sustraendo</label>
					<input type="text" class="form-control" name="sustraendo" value="{{$retencion->sustraendo}}" required>
				</div>
				<div class="form-group">
					<label>Monto Minimo Sujeto a Retencion</label>
					<input type="text" class="form-control" name="monto_min_retencion" value="{{$retencion->monto_min_retencion}}" required>
				</div>
				
				<button type="submit" class="btn btn-primary float-right">Modificar</button>
			</form>
		</div>	
	</div>
</div>
@endsection