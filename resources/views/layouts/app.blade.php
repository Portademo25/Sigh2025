<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="session-lifetime" content="{{ config('session.lifetime') }}">

    <title>{{ config('app.name', '') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
    #session-warning {
        z-index: 9999;
        min-width: 320px;
        border-left: 5px solid #ffc107; /* Color amarillo de advertencia */
        background-color: #fff;
        color: #333;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
        border-radius: 8px;
    }
    .timer-badge {
        background-color: #fff3cd;
        color: #856404;
        padding: 2px 8px;
        border-radius: 4px;
        font-family: monospace;
        font-weight: bold;
    }
    @keyframes pulse {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.05); opacity: 0.8; }
    100% { transform: scale(1); opacity: 1; }
}
</style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
               <img src="{{ asset('images/fona.jpeg') }}" alt="FONA Logo" style="height:40px; width:40px; margin-right:10px;">
                <a class="navbar-brand" href="{{ url('/') }}">
                       {{ config('app.name') }}
                </a>
                    @if(DB::table('settings')->where('key', 'site_offline')->value('value') == '1')
            <div class="d-none d-md-block ms-3">
                <span class="badge bg-danger border border-light shadow-sm py-2 px-3" style="animation: pulse 2s infinite;">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    MODO MANTENIMIENTO ACTIVO
                </span>
            </div>
        @endif
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            {{-- @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif --}}

                            {{-- @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif --}}
                        @else
                         @auth
                       <li class="nav-item dropdown">
                           <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" ">
                               {{ Auth::user()->name }} {{ auth()->user()->apellido }}
                                  @if(Auth::user()->hasRole('admin'))
                                       <span class="badge bg-danger">Admin</span>
                                            @elseif(Auth::user()->hasRole('empleado'))
                                <span class="badge bg-primary">Empleado</span>
            @endif
        </a>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a></li>
            <li>
                <a class="dropdown-item" href="{{ route('logout') }}"
                   onclick="event.preventDefault();
                   document.getElementById('logout-form').submit();">
                    Cerrar Sesión
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>
        </ul>
    </li>
@endauth
                            {{-- <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div> --}}
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <div id="session-warning" class="alert position-fixed bottom-0 end-0 m-4 d-none">
    <div class="d-flex align-items-start">
        <div class="me-3">
            <i class="bi bi-clock-history text-warning" style="font-size: 2rem;"></i>
        </div>
        <div>
            <h6 class="alert-heading fw-bold mb-1">Aviso de Inactividad</h6>
            <p class="mb-2" style="font-size: 0.9rem;">
                Tu sesión se cerrará en <span id="session-timer" class="timer-badge">120</span> segundos.
            </p>
            <div class="d-flex gap-2">
                <button onclick="window.location.reload()" class="btn btn-warning btn-sm fw-bold shadow-sm">
                    Mantener sesión activa
                </button>
                <button onclick="window.location.href='{{ route('logout') }}'" class="btn btn-light btn-sm border">
                    Salir
                </button>
            </div>
        </div>
    </div>
</div>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

   <script>
    (function() {
        // 1. Obtener configuración
        const sessionLifetimeMin = parseInt(document.querySelector('meta[name="session-lifetime"]').content);
        if (!sessionLifetimeMin) return;

        const warningBeforeSec = 120; // Mostrar aviso 120 segundos antes
        let warningTimer;
        let countdownInterval;

        function resetTimers() {
            // Ocultar alerta si estaba visible
            document.getElementById('session-warning').classList.add('d-none');

            // Limpiar timers anteriores
            clearTimeout(warningTimer);
            clearInterval(countdownInterval);

            // Calcular tiempo hasta el aviso (Total - Margen de aviso)
            const timeToWarningMs = (sessionLifetimeMin * 60 * 1000) - (warningBeforeSec * 1000);

            warningTimer = setTimeout(showWarning, timeToWarningMs > 0 ? timeToWarningMs : 1000);
        }

        function showWarning() {
            const warningEl = document.getElementById('session-warning');
            const timerEl = document.getElementById('session-timer');
            warningEl.classList.remove('d-none');

            let timeLeft = warningBeforeSec;
            timerEl.innerText = timeLeft;

            countdownInterval = setInterval(() => {
                timeLeft--;
                timerEl.innerText = timeLeft;

                if (timeLeft <= 0) {
                    clearInterval(countdownInterval);
                    // Cerrar sesión enviando al logout
                    document.getElementById('logout-form').submit();
                }
            }, 1000);
        }

        // Reiniciar cuando el usuario interactúe (Throttle para no saturar el navegador)
        let lastActivity = Date.now();
        const activityEvents = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'];

        activityEvents.forEach(event => {
            document.addEventListener(event, () => {
                if (Date.now() - lastActivity > 5000) { // Solo resetear cada 5 segundos de actividad
                    lastActivity = Date.now();
                    resetTimers();
                }
            }, true);
        });

        resetTimers();
    })();
</script>
</body>

</html>
