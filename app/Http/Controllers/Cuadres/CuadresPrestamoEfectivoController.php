<?php

namespace App\Http\Controllers\Cuadres;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cuadres\CuadrePrestamo;
use Illuminate\Support\Facades\Auth;
class CuadresPrestamoEfectivoController extends Controller
{
    public function index(){
        return CuadrePrestamo::where('empresa_rif',session('empresaRif'))->where('fecha',session('fechaCuadre'))->get();
    }

    public function guardarPrestamoEfectivo(Request $request){
        $prestamo = new CuadrePrestamo();
        $prestamo->empresa_rif = session('empresaRif');
        $prestamo->fecha =session('fechaCuadre');
        $prestamo->rif = $request->rif;
        $prestamo->nombre = $request->nombre;
        $prestamo->descripcion = $request->descripcion;
        $prestamo->monto = $request->monto;
        $prestamo->creado_por = Auth::user()->name;
        $prestamo->save();
    }

    public function eliminar($id){
        $prestamo = CuadrePrestamo::find($id);
        $prestamo->delete();
    }
}
