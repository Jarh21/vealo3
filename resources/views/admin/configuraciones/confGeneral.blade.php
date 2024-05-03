@extends('layouts.app')
@section('content')
    <div class="container">
        <h3>Configuracion General</h3><hr>
        <form action="{{route('guardarConfiguracionGeneral')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="container">
                <div class="row my-3">
                    <div class="col-4">
                        <label for="correo_del_sistema">Correo electronico utilizado por vealo para el envio de correos</label>
                    </div>
                    <div class="col">
                        <input type="text" name="correo_del_sistema" class="form-control" value="{{$correo_del_sistema ?? ''}}" >               
                    </div>
                </div>
                <div class="row my-3">
                    <div class="col-4">
                        <label for="password_correo_del_sistema">Contraseña de aplicacion de google para el correo</label>
                    </div>
                    <div class="col">
                        <input type="text" name="password_correo_del_sistema" id="password_correo_del_sistema" value="{{$password_correo_del_sistema ?? ''}}" class="form-control">
                    </div>
                    
                </div>
                
                <h3> Cómo crear y usar contraseñas de aplicaciones </h3>
                <p>Importante: Para crear una contraseña de la aplicación, necesitas tener activada la Verificación en 2 pasos en tu Cuenta de Google.</p>

                <p>Si usas la Verificación en 2 pasos y recibes un error de "contraseña incorrecta" cuando accedes, intenta usar una contraseña de la aplicación.</p>
                <ul>
                    <li>Ve a tu Cuenta de Google.</li>
                    <li>Selecciona Seguridad.</li>
                    <li>En la sección "Cómo acceder a Google", selecciona Verificación en 2 pasos.</li>
                    <li>En la parte inferior de la página, selecciona Contraseñas de aplicaciones.</li>
                    <li>Ingresa un nombre que te ayude a recordar dónde usarás la contraseña de la aplicación.</li>
                    <li>Selecciona la opción Generar.</li>
                    <li>Para ingresar la contraseña de la aplicación, sigue las instrucciones que aparecen en pantalla. La contraseña de la aplicación es el código de 16 caracteres que se genera en el dispositivo.
                Selecciona Listo.</li>
                    
                </ul>

                <p>Si configuraste la Verificación en 2 pasos, pero no encuentras la opción para agregar una contraseña de la aplicación, puede deberse a los siguientes motivos:</p>
                <p>Tu Cuenta de Google tiene la Verificación en 2 pasos configurada solo para las llaves de seguridad.
                Accediste a una cuenta de trabajo, de institución educativa o de otra organización.
                Tu Cuenta de Google tiene activada la Protección avanzada.
                Nota: Por lo general, deberás ingresar una contraseña de la aplicación una vez por app o dispositivo.</p>
                <hr>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
            
        </form>
    </div>
@endsection