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
    public function __construct()
    {
        $this->middleware('guest')->except('checkEmail');
    }

    // PASO 1: Verificar el correo ingresado (Prioridad Estper 1)
  public function checkEmail(Request $request)
{
    $request->validate(['email' => 'required|email']);
    $email = strtolower(trim($request->email));

    // 1. PRIMERO: Verificamos si ya existe en nuestra base de datos local (PostgreSQL)
    $user = User::where('email', $email)->first();

    if ($user) {
        // EXCEPCIÓN DE SEGURIDAD:
        // Si es Administrador (1) o Analista RRHH (3), saltamos la validación de SIGESP.
        // Esto garantiza que el personal de gestión siempre pueda acceder.
        if (in_array($user->rol_id, [1, 3])) {
            return redirect()->route('login')
                             ->withInput(['email' => $email])
                             ->with('user_verified', true);
        }

        // Si es un Empleado común, SÍ validamos que su ficha siga ACTIVA en SIGESP
        $personalActivo = DB::connection('sigesp')->table('sno_personal')
            ->whereRaw('LOWER(TRIM(coreleper)) = ?', [$email])
            ->where('estper', '1')
            ->first();

        if (!$personalActivo) {
            return back()->withErrors([
                'email' => 'Acceso denegado: Su ficha no figura como ACTIVA en el sistema de nómina (SIGESP).'
            ]);
        }

        return redirect()->route('login')
                         ->withInput(['email' => $email])
                         ->with('user_verified', true);
    }

    // 2. Si NO existe en nuestra DB, buscamos en SIGESP para permitir un registro nuevo
    $personalNuevo = DB::connection('sigesp')->table('sno_personal')
        ->whereRaw('LOWER(TRIM(coreleper)) = ?', [$email])
        ->where('estper', '1')
        ->first();

    if ($personalNuevo) {
        session([
            'register_email' => $email,
            'register_codper' => trim($personalNuevo->codper)
        ]);
        return redirect()->route('auth.complete_register')
                         ->with('success', 'Trabajador identificado en SIGESP. Proceda a completar su registro local.');
    }

    return back()->withErrors(['email' => 'Este correo no coincide con nuestro archivo de personal activo.']);
}

    // PASO 2: Mostrar formulario usando el CODPER de la sesión
    public function showRegisterForm()
    {
        if (!session('register_email') || !session('register_codper')) {
            return redirect()->route('login')->withErrors(['email' => 'Debes verificar tu correo primero.']);
        }

        $codper = session('register_codper');

        // Buscamos específicamente por el código de personal que capturamos en el paso 1
        // Así nos aseguramos de traer los datos de Juan Luis y no de Jackson
        $empleado = DB::connection('sigesp')->table('sno_personal')
            ->where('codper', $codper)
            ->where('estper', '1')
            ->first();

        if (!$empleado) {
            return redirect()->route('login')->withErrors([
                'email' => 'Error de consistencia: La ficha activa ya no está disponible.'
            ]);
        }

        return view('auth.complete-register', compact('empleado'));
    }

    // PASO 3: Guardar el usuario validando contra la ficha activa
    public function storeUser(Request $request)
    {
        $request->validate([
            'cedula_check' => 'required',
            'fecha_ingreso_check' => 'required|date',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $codper = session('register_codper');

        // Buscamos nuevamente por codper y estper 1 para máxima seguridad
        $empleado = DB::connection('sigesp')->table('sno_personal')
            ->where('codper', $codper)
            ->where('estper', '1')
            ->first();

        if (!$empleado) {
            return redirect()->route('login')->withErrors([
                'email' => 'La sesión expiró o el trabajador ya no figura como activo.',
            ]);
        }

        // Validación de identidad
        $cedulaValida = trim($empleado->cedper) === trim($request->cedula_check);
        $fechaValida = date('Y-m-d', strtotime($empleado->fecingper)) === $request->fecha_ingreso_check;

        if (!$cedulaValida || !$fechaValida) {
            return back()->withErrors([
                'cedula_check' => 'Los datos de identidad no coinciden con la ficha activa.',
            ])->withInput();
        }

        try {
            $user = User::create([
                'name'       => trim($empleado->nomper),
                'apellido'   => trim($empleado->apeper),
                'email'      => strtolower(trim($empleado->coreleper)),
                'password'   => Hash::make($request->password),
                'cedula'     => trim($empleado->cedper),
                'codper'     => trim($empleado->codper),
                'rol_id'     => 2,
                'estatus_id' => 1, // Usuario activo
                'organizacion_id' => 9,
            ]);

            $user->assignRole('empleado');
            Auth::login($user);
            session()->forget(['register_email', 'register_codper']);

            return redirect()->route('home')->with('success', '¡Bienvenido(a) ' . $user->name . '!');

        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Error al crear la cuenta: ' . $e->getMessage()]);
        }
    }
}
