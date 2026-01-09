<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    // PASO 1: Verificar el correo ingresado

    public function __construct()
{
    // Esto asegura que incluso si alguien está "medio logueado",
    // la ruta de verificar email funcione.
    $this->middleware('guest')->except('checkEmail');
}


public function checkEmail(Request $request)
{
    // 1. Validamos que sea un email real
    $request->validate(['email' => 'required|email']);
    $email = strtolower(trim($request->email));

    // 2. ¿Ya existe en nuestra tabla de usuarios de Laravel?
    $user = \App\Models\User::where('email', $email)->first();

    if ($user) {
        // Si es Admin (rol_id 1), lo mandamos al login normal
        // Si es empleado (rol_id 2), verificamos que siga en SIGESP
        if ($user->rol_id != 1) {
            $sigueActivo = \Illuminate\Support\Facades\DB::table('sno_personal')
                ->whereRaw('LOWER(TRIM(coreleper)) = ?', [$email])
                ->exists();

            if (!$sigueActivo) {
                return back()->withErrors(['email' => 'Su acceso ha sido desactivado por nómina.']);
            }
        }

        return redirect()->route('login')->withInput(['email' => $email])
                         ->with('info', 'Hola de nuevo. Ingresa tu contraseña.');
    }

    // 3. Si NO existe en Laravel, lo buscamos en SIGESP para que se registre
    $personal = \Illuminate\Support\Facades\DB::table('sno_personal')
        ->whereRaw('LOWER(TRIM(coreleper)) = ?', [$email])
        ->first();

    if ($personal) {
        // Guardamos datos clave en la sesión para el siguiente paso (Registro)
        session([
            'register_email' => $email,
            'register_codper' => trim($personal->codper)
        ]);

        return redirect()->route('auth.complete_register')
                         ->with('success', 'Empleado encontrado. Complete su registro.');
    }

    // 4. Si no está en ningún lado
    return back()->withErrors(['email' => 'Este correo no está autorizado en el sistema de nómina.']);
}
    // PASO 2: Mostrar formulario de contraseña
public function showRegisterForm()
{
    // 1. Verificamos que el email esté en la sesión (que venga del paso anterior)
    if (!session('register_email')) {
        return redirect()->route('login')->withErrors(['email' => 'Debes verificar tu correo primero.']);
    }

    $email = session('register_email');

    // 2. Buscamos los datos en SIGESP para mostrarlos en el formulario
    $empleado = \Illuminate\Support\Facades\DB::table('sno_personal')
        ->whereRaw('LOWER(TRIM(coreleper)) = ?', [$email])
        ->first();

    if (!$empleado) {
        return redirect()->route('login')->withErrors(['email' => 'Error al recuperar los datos del trabajador.']);
    }

    // 3. Retornamos la vista que creamos antes con los datos del empleado
    return view('auth.complete-register', compact('empleado'));
}

    // PASO 3: Guardar el usuario
   public function storeUser(Request $request)
{
    $request->validate([
        'cedula_check' => 'required',
        'fecha_ingreso_check' => 'required|date',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $email = session('register_email');

    // 1. Buscar en SIGESP
    $empleado = DB::table('sno_personal')->where('coreleper', $email)->first();

    // 2. VALIDACIÓN CRÍTICA
    // Comparamos cédula (quitando espacios) y la fecha de ingreso
    $cedulaValida = trim($empleado->cedper) === trim($request->cedula_check);

    // SIGESP suele guardar fechas en formato Y-m-d, formateamos la entrada para asegurar
    $fechaValida = date('Y-m-d', strtotime($empleado->fecingper)) === $request->fecha_ingreso_check;

    if (!$cedulaValida || !$fechaValida) {
        return back()->withErrors([
            'cedula_check' => 'Los datos de identidad no coinciden con nuestro archivo de nómina.',
        ])->withInput();
    }

    // 3. TODO OK -> Crear usuario en Laravel
    $user = User::create([
        'name' => trim($empleado->nomper . ' ' . $empleado->apeper),
        'email' => $email,
        'password' => Hash::make($request->password),
        'cedula' => trim($empleado->cedper),
        'codper' => trim($empleado->codper),
        'rol_id' => 2, // 2 = Rol de Trabajador
    ]);

    Auth::login($user);
    session()->forget(['register_email', 'register_codper']);

    return redirect()->route('home')->with('success', '¡Cuenta activada! Ya puedes ver tus recibos.');
}
}
