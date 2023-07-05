<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    public function datosEmpresa(){
        return view('admin.datosEmpresa');
    }

    


}
