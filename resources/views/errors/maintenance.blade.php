<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mantenimiento | {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7f6;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .maintenance-container {
            max-width: 500px;
            text-align: center;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }
        .gear-icon {
            font-size: 80px;
            color: #6c757d;
            animation: spin 4s linear infinite;
        }
        @keyframes spin {
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <div class="gear-icon">⚙️</div>
        <h1 class="mt-4 fw-bold">Página en Mantenimiento</h1>
        <p class="text-muted">
            Estamos realizando mejoras en el **{{ config('app.name') }}**. 
            Volveremos a estar en línea muy pronto.
        </p>
        <hr>
        <p class="small text-secondary">Si eres administrador, puedes intentar acceder aquí:</p>
        <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Iniciar Sesión</a>
    </div>
</body>
</html>