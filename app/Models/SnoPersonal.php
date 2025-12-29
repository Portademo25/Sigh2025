<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SnoPersonal extends Model
{
    protected $table = 'sno_personal';
    protected $primaryKey = 'codper';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['codemp', 'codper', 'cedper', 'nomper', 'apeper', 'coreleper', 'fecingper', 'codger'];
}
