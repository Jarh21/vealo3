<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParametroCalculoComision extends Model
{
    use HasFactory;
    protected $table = 'parametros_calculo_comision';
    public $timestamps = true;
}
