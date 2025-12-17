<?php

// app/Models/Conexion.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conexion extends Model
{
    protected $table = 'conexion'; 

    protected $fillable = [
        'user_id',
        'fechaconexion',
        'horaconexion', // <-- Agregada
        'ipconexion',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}