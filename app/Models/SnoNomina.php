<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SnoNomina extends Model
{
    protected $table = 'sno_nomina';
    protected $primaryKey = 'codnom';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['codemp', 'codnom', 'desnom', 'tipnom'];
}
