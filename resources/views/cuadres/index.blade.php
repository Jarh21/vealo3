@extends('layouts.app')

@section('content')
<div class="container">
    <div class="">
        <p class="display-5">Cuadres y Arqueo de cajas</p>
    </div>
   
    <?php
        # definimos los valores iniciales para nuestro calendario
        $month=date("n",strtotime($anioYmesCuadre));
        $year=date("Y",strtotime($anioYmesCuadre));
        $diaActual=date("j");       
        # Obtenemos el dia de la semana del primer dia
        # Devuelve 0 para domingo, 6 para sabado
        $diaSemana=date("w",mktime(0,0,0,$month,1,$year))+7;
        # Obtenemos el ultimo dia del mes
        $ultimoDiaMes=date("d",(mktime(0,0,0,$month+1,1,$year)-1));
        $color = session('color');
        
        $meses=array(1=>"Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio",
        "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
    ?>
    <div class='mb-2'>
        <form action="{{route('cuadres.buscarMes')}}" method="post" id="fechaCuadre" name="fechaCuadre">
            @csrf @method('post')
            <input type="month" class="form-control w-25 d-inline" name="anioYmesCuadre" value="{{$anioYmesCuadre ?? ''}}" onchange="document.forms.fechaCuadre.submit();">
        </form>           
    </div>
    <table id="calendar" >
        

        <thead>
            <tr>
                <th>Lun</th><th>Mar</th><th>Mie</th><th>Jue</th>
                <th>Vie</th><th>Sab</th><th>Dom</th>
            </tr>
        </thead>
        <tbody>
        <tr >
            <?php
                $last_cell=$diaSemana+$ultimoDiaMes;
                // hacemos un bucle hasta 42, que es el máximo de valores que puede
                // haber... 6 columnas de 7 dias
                for($i=1;$i<=42;$i++)
                {
                    if($i==$diaSemana)
                    {
                        // determinamos en que dia empieza
                        $day=1;
                    }
                    if($i<$diaSemana || $i>=$last_cell)
                    {
                        // celca vacia
                        echo "<td class='nulo'>&nbsp;</td>";
                    }else{
                        // mostramos el dia
                        $fecha=$year.'-'.$month.'-'.$day;
                        if($day==$diaActual){
                            echo "<td class='hoy'><span class='badge badge-pill badge-secondary m-2'>$day</span></td>";
                        }else{
                            echo "<td><span class='badge rounded-pill bg-warning text-dark m-2'>$day</span>";
                            if($day <= 15){
                                echo"ALVARO DELGADO $fecha 01:06:41 pm";
                                echo"<span class='badge bg-danger'>Procesado</span>";
                            }else{
                                
                            }
                            echo"</td>";
                        }
                            
                        $day++;
                    }
                    // cuando llega al final de la semana, iniciamos una columna nueva
                    if($i%7==0)
                    {
                        echo "</tr><tr>\n";
                    }
                }
            ?>
            </tr>
        </tbody>
        
    </table>
</div>

@stop

