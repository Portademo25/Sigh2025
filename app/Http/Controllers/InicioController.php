<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InicioController extends Controller
{
    public function index()
    {
         $mensaje = '¡Bienvenido a tu página de inicio del Sistema de Constancia!';
         $textoAdicional = 'Aquí puedes gestionar tus constancias de manera eficiente.';

        // Pasa el mensaje a la vista
        return view('inicio', ['mensaje' => $mensaje],
            ['textoAdicional' => $textoAdicional]
    );
    }
}
