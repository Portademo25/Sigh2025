<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SnoHperiodo extends Model
{
    protected $table = 'sno_hperiodo';
    protected $primaryKey = 'codperi'; // Clave compuesta o única en este contexto
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['codemp', 'codnom', 'codperi', 'fecdesper', 'fechasper', 'cerper'];
}
