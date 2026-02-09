<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema en Mantenimiento - SIGESP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; height: 100vh; display: flex; align-items: center; }
        .maintenance-card { max-width: 500px; margin: auto; border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .icon-box { font-size: 4rem; color: #0d6efd; margin-bottom: 20px; }
    </small></style>
</head>
<body>
    <div class="container text-center">
        <div class="card maintenance-card p-5">
            <div class="icon-box">
                <i class="bi bi-tools"></i>
            </div>
            <h2 class="fw-bold text-dark">Estamos en Mantenimiento</h2>
            <p class="text-muted">
                El Portal de Autogestión <strong>SIGH</strong> está recibiendo actualizaciones para mejorar su experiencia.
                Estaremos de vuelta en unos minutos.
            </p>
            <hr>
            <div class="mt-4">
    <p class="text-muted">Si eres administrador y necesitas realizar ajustes:</p>

    <a href="{{ route('logout') }}"
       onclick="event.preventDefault(); document.getElementById('force-logout-form').submit();"
       class="btn btn-primary">
        Acceder como Administrador
    </a>

    <form id="force-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
    </div>
        </div>
        <p class="mt-4 text-secondary small">&copy; {{ date('Y') }} - Departamento de Tecnología</p>
    </div>
</body>
</html>
