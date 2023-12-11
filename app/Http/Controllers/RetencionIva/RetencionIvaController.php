<?php

namespace App\Http\Controllers\RetencionIva;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Proveedor;

class RetencionIvaController extends Controller
{
    public function index(){
        $proveedores = Proveedor::all();
        return view('retencionIva.importarDocumento',['proveedores'=>$proveedores]);
    }
}
