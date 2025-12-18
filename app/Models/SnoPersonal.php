<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SnoPersonal extends Model
{
    protected $table = 'sno_personal'; // Forzamos el nombre de la tabla
    protected $fillable = ['codemp', 'codper', 'cedper', 'nomper', 'apeper', 'coreleper', 'fecingper', 'codger'];
}
