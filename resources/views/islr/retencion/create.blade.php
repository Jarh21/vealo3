@extends('layouts.islr')
@section('content')
<div class="container">
	<h3>Registrar Determinacion de la Retencion <a href="{{url('/retencion')}}" class="btn btn-success float-right">Regresar</a></h3><hr>
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
			
			<form action="{{route('retencion.save')}}" method="POST">
				@csrf
				<div class="form-group">
					<label>% Retencion</label>
					<input type="text" class="form-control" name="procent_retencion" >
				</div>
				<div class="form-group">
					<label>Valor U.T</label>
					@foreach($valorUT as $ut)
						<p>{{$ut->monto}}</p>
					@endforeach
				</div>
				<div class="form-group">
					<label for="disabledTextInput">Factor</label>
					<input class="form-control" id="disabledTextInput" type="text" name="factor" value="83.3334">
				</div>
				
				
				<button type="submit" class="btn btn-primary float-right">Guardar</button>
			</form>
		</div>	
	</div>
</div>
@endsection