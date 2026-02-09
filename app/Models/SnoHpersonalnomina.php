<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SnoHpersonalnomina extends Model
{
    protected $table = 'sno_hpersonalnomina';
    // Esta tabla suele ser de relación, si no tiene PK única, usamos codper temporalmente
    protected $primaryKey = 'codper';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['codemp', 'codnom', 'codper', 'codque', 'codasicar', 'tabpersonal'];
}
