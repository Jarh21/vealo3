<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetencionIva extends Model
{
    use HasFactory;
    protected $table="retenciones";
    protected $primaryKey = 'keycodigo';
    public $timestamps = false;
}
