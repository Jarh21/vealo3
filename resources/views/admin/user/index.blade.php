@extends('layouts.app')

@section('content')
    <div class="container">
        <h3>Listado de usuarios</h3>
        <div> <a href="{{ route('admin.user.register') }}" class="ml-4 text-sm text-gray-700 dark:text-gray-500 underline">+ Registrar Nuevo Usuario</a></div>
        <div class="card">
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Nombres</th>
                            <th>Correo</th>
                            <th>Acción</th>
                        </tr>
                        
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{$user->id}}</td>
                            <td>{{$user->name}}</td>
                            <td>{{$user->email}}</td>
                            <td>
                                <a class="btn btn-secondary" href="{{route('admin.user.edit',$user)}}">Editar</a>
                                <a class="btn btn-danger" onclick="eliminar('{{$user->id}}')">Eliminar</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
    <script type="text/javascript">
        function eliminar(id){
            let conf = confirm("¿Desea Eliminar el usuario seleccionado?");
            if(conf){
                location.href="user/delete/"+id;
            }
        }
    </script>
  
@endsection