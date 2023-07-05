	<link rel="stylesheet" type="text/css" href="{{asset('css/bootstrap4.5.2.css')}}">
	<div class="jumbotron">
	  <h1 class="display-4">Proveedor: {{$proveedor->nombre}}</h1>
	  
	   <hr class="my-4">
	  <p class="lead">Rif: {{$proveedor->rif}}</p>
	  <p class="lead">Persona: {{$proveedor->tipo_contribuyente}}</p>
	  <p class="lead">Direccion: {{$proveedor->direccion}}</p>		
	  
	</div>