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
    $request->validate(['email' => 'required|email']);
    $email = strtolower(trim($request->email));

    // 1. ¿Existe en la tabla local de Laravel?
    $user = User::where('email', $email)->first();

    if ($user) {
        // Validación de bloqueo permanente
        if ($user->is_locked) {
            return back()->withErrors(['email' => 'Esta cuenta está bloqueada permanentemente por seguridad.']);
        }

        // Si es empleado, verificar vigencia en SIGESP
        if ($user->rol_id != 1) {
            $sigueActivo = DB::connection('sigesp')->table('sno_personal')
                ->whereRaw('LOWER(TRIM(coreleper)) = ?', [$email])
                ->exists();

            if (!$sigueActivo) {
                return back()->withErrors(['email' => 'Acceso denegado: Su ficha no está activa en la nómina de SIGESP.']);
            }
        }

        // --- LA CLAVE ESTÁ AQUÍ ---
        // Redirigimos al login pasando 'user_verified' para que la vista muestre la contraseña
        return redirect()->route('login')
                         ->withInput(['email' => $email])
                         ->with('user_verified', true);
    }

    // 2. Si es NUEVO: Buscar en SIGESP para permitir el registro inicial
    $personal = DB::connection('sigesp')->table('sno_personal')
        ->whereRaw('LOWER(TRIM(coreleper)) = ?', [$email])
        ->first();

    if ($personal) {
        session([
            'register_email' => $email,
            'register_codper' => trim($personal->codper)
        ]);
        return redirect()->route('auth.complete_register')
                         ->with('success', 'Trabajador identificado. Por favor, complete su registro.');
    }

    return back()->withErrors(['email' => 'Este correo no coincide con nuestro archivo de personal.']);
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
