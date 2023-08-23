@extends('layouts.app')
@section('content')
    <div class="container">
        <h3>Registro de Usuarios <a href="{{route('admin.user.index')}}" class="btn btn-warning float-right"><i class="fas fa-chevron-left"></i>Regresar</a></h3>
            @if(session('info'))
            <div class="alert alert-info">
                <strong>{{session('info')}}</strong>
            </div>
            @endif
        <form action="{{route('admin.user.save')}}" method="post">
            
            @csrf
            <div class="row">
                <div class="col">
                    <label for="">Usuario</label>
                    <input type="text" value="" class="form-control" name="name">
                    <label for="">Email</label>
                    <input type="text" value="" class="form-control" name="email">
                    <label for="">Password</label>
                    <input type="password" class="form-control" name="password">
                    <button type="submit" class="btn btn-primary float-end my-3">Guardar</button>
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            Roles de usuarios
                        </div>
                        <div class="card-body">                            
                            @foreach($roles as $role)
                                <div>
                                    <input id="rol" name="roles[]" value="{{$role->id}}" type="checkbox">
                                    <label for="rol">{{$role->name}}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                </div>
                
            </div>
            
        </form>
    </div>
@endsection