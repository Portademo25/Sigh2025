<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SigespEmpresa extends Model
{
    protected $table = 'sigesp_empresa';

    // ESTO ES LO QUE FALTA:
    protected $primaryKey = 'codemp'; // La PK es codemp
    public $incrementing = false;     // No es un id autoincremental
    protected $keyType = 'string';    // El código suele ser string (ej: '0001')

    protected $fillable = ['codemp', 'nombre', 'rif', 'dirlibemp', 'telemp'];
}
