<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetencionIvaDetalle extends Model
{
    use HasFactory;
    protected $table="retenciones_dat";
    protected $primaryKey = 'keycodigo';
    public $timestamps = false;
}
