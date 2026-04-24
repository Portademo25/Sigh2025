<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

    Log::info("--- INICIO DE VALIDACIÓN PARA: $email ---");

    // 1. Verificación Local (PostgreSQL)
    $user = User::where('email', $email)->first();

    if ($user) {
        Log::info("Usuario encontrado en DB Local. Rol ID: {$user->rol_id}");

        if (in_array($user->rol_id, [1, 3])) {
            Log::info("Acceso Administrativo/RRHH concedido sin pasar por SIGESP.");
            return redirect()->route('login')
                             ->withInput(['email' => $email])
                             ->with('user_verified', true);
        }

        $personalActivo = DB::connection('sigesp')->table('sno_personal')
            ->whereRaw('LOWER(TRIM(coreleins)) = ?', [$email])
            ->where('estper', '1')
            ->first();

        if (!$personalActivo) {
            Log::warning("Usuario local existe pero NO está ACTIVO en SIGESP para el correo: $email");
            return back()->withErrors(['email' => 'Acceso denegado: Ficha INACTIVA en SIGESP.']);
        }

        Log::info("Empleado verificado y ACTIVO en SIGESP.");
        return redirect()->route('login')->withInput(['email' => $email])->with('user_verified', true);
    }

    // 2. Registro Nuevo (Búsqueda en SIGESP)
    Log::info("Buscando trabajador nuevo en SIGESP...");

    $personalNuevo = DB::connection('sigesp')->table('sno_personal')
        ->whereRaw('LOWER(TRIM(coreleins)) = ?', [$email])
        ->where('estper', '1')
        ->first();

    if ($personalNuevo) {
        Log::info("Trabajador nuevo identificado. CODPER: " . trim($personalNuevo->codper));

        // Guardamos en sesión
        session([
            'register_email' => $email,
            'register_codper' => trim($personalNuevo->codper)
        ]);

        Log::info("Datos guardados en sesión. Redirigiendo a complete_register...");

        // Verificamos si la sesión realmente se escribió (para descartar problemas de permisos)
        if (session('register_email') !== $email) {
            Log::error("¡ERROR CRÍTICO!: La sesión no se pudo escribir. Revisa los permisos de storage/framework/sessions");
        }

        return redirect()->route('auth.complete_register')
                         ->with('success', 'Trabajador identificado. Complete su registro.');
    }

    Log::error("Correo institucional no encontrado en SIGESP: $email");
    return back()->withErrors(['email' => 'Este correo no coincide con nuestro archivo de personal activo.']);
}
    // PASO 2: Mostrar formulario usando el CODPER de la sesión
    public function showRegisterForm()
{
    Log::info("--- ENTRANDO A showRegisterForm ---");
    Log::info("Sesión Email: " . session('register_email', 'VACÍO'));
    Log::info("Sesión Codper: " . session('register_codper', 'VACÍO'));

    if (!session('register_email') || !session('register_codper')) {
        return redirect()->route('login')->withErrors(['email' => 'Debes verificar tu correo primero.']);
    }

    $codper = session('register_codper');

    // Usamos TRIM en la consulta para ignorar espacios en blanco del SIGESP
    $empleado = DB::connection('sigesp')->table('sno_personal')
        ->whereRaw('TRIM(codper) = ?', [trim($codper)]) // <--- CAMBIO AQUÍ
        ->where('estper', '1')
        ->first();

    if (!$empleado) {
        Log::error("ERROR: No se encontró al empleado en SIGESP con CODPER: $codper");
        return redirect()->route('login')->withErrors([
            'email' => 'Error de consistencia: No se pudo cargar su ficha de trabajador.'
        ]);
    }

    Log::info("Empleado cargado con éxito: " . $empleado->nomper);

    return view('auth.complete-register', compact('empleado'));
}

    // PASO 3: Guardar el usuario validando contra la ficha activa
    public function storeUser(Request $request)
{
    Log::info("--- PROCESANDO ACTIVACIÓN FINAL ---");

    $request->validate([
        'cedula_check' => 'required',
        'fecha_ingreso_check' => 'required|date',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $codper = session('register_codper');
    $email_registro = session('register_email'); // El que validamos al inicio

    $empleado = DB::connection('sigesp')->table('sno_personal')
        ->where('codper', $codper)
        ->where('estper', '1')
        ->first();

    if (!$empleado || !$email_registro) {
        Log::error("Sesión perdida: Email(" . ($email_registro ?? 'No') . ") Codper(" . ($codper ?? 'No') . ")");
        return redirect()->route('login')->withErrors(['email' => 'La sesión expiró. Intente de nuevo.']);
    }

    // Comparación numérica de cédula para evitar errores de formato
    $cedulaValida = (int)trim($empleado->cedper) === (int)trim($request->cedula_check);

    // Comparación de fecha normalizada
    $fecha_sigesp = date('Y-m-d', strtotime($empleado->fecingper));
    $fechaValida = $fecha_sigesp === $request->fecha_ingreso_check;

    Log::info("Validación: Cédula(" . ($cedulaValida ? 'OK' : 'FAIL') . ") Fecha(" . ($fechaValida ? 'OK' : 'FAIL') . ")");

    if (!$cedulaValida || !$fechaValida) {
        return back()->withErrors([
            'cedula_check' => 'Los datos de identidad no coinciden con nuestro archivo. Verifique su cédula y fecha de ingreso.'
        ])->withInput();
    }

    try {
        $user = User::create([
            'name'       => trim($empleado->nomper),
            'apellido'   => trim($empleado->apeper),
            'email'      => $email_registro, // <--- USAMOS EL EMAIL DE LA SESIÓN
            'password'   => Hash::make($request->password),
            'cedula'     => trim($empleado->cedper),
            'codper'     => trim($empleado->codper),
            'rol_id'     => 2,
            'estatus_id' => 1,
            'organizacion_id' => 9,
        ]);

        // Si usas Spatie Permissions:
        if (method_exists($user, 'assignRole')) {
            $user->assignRole('empleado');
        }

        Auth::login($user);

        Log::info("Usuario activado con éxito: " . $user->email);

        session()->forget(['register_email', 'register_codper']);

        return redirect()->route('home')->with('success', '¡Bienvenido(a) ' . $user->name . '!');

    } catch (\Exception $e) {
        Log::error("Error en User::create: " . $e->getMessage());
        return back()->withErrors(['email' => 'Error al crear la cuenta: ' . $e->getMessage()]);
    }
}
}
