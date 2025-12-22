<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Institucional - RRHH</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Public Sans', sans-serif; color: #334155; }
        .hero-section { background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: white; padding: 100px 0; }
        .feature-icon { font-size: 2rem; color: #3b82f6; margin-bottom: 1rem; }
        .card { border: none; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); transition: 0.3s; }
        .card:hover { transform: translateY(-5px); }

        /* Estilos del Loading (Oculto por defecto) */
        #loader-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: #f8fafc; display: none; justify-content: center;
            align-items: center; z-index: 9999; flex-direction: column;
        }
        .spinner {
            width: 50px; height: 50px; border: 5px solid #e2e8f0;
            border-top: 5px solid #1e3a8a; border-radius: 50%;
            animation: spin 1s linear infinite; margin-bottom: 20px;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>

    <div id="loader-overlay">
        <div class="spinner"></div>
        <h5 class="text-muted">Conectando con el Portal de Personal...</h5>
    </div>

    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="https://fona.gob.ve">Fondo Nacional Antidrogas</a>
            <button class="btn btn-primary" onclick="showLoading()">Iniciar Sesión</button>
        </div>
    </nav>

    <header class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold">Bienvenido al Portal de Personal</h1>
            <p class="lead">Gestión transparente, eficiente y al servicio de nuestros trabajadores.</p>
        </div>
    </header>

    <section class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2>Nuestra Institución</h2>
                    <p class="text-muted">Somos un ente comprometido con el desarrollo del país, enfocados en la excelencia operativa y el bienestar de cada uno de nuestros integrantes.</p>
                </div>
                <div class="col-md-6">
                    <div class="p-4 bg-light rounded shadow-sm">
                        <h5>Misión de RRHH</h5>
                        <p class="small">Optimizar la gestión del talento humano mediante procesos automatizados (SIGESP) para garantizar que cada trabajador reciba atención oportuna en sus trámites administrativos y beneficios.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-light py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2>¿Cómo trabaja Recursos Humanos?</h2>
                <p>Nuestros procesos están digitalizados para tu comodidad</p>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="card h-100 p-4 text-center">
                        <div class="feature-icon">📁</div>
                        <h5>Sincronización SIGESP</h5>
                        <p class="small text-muted">Tus datos se actualizan directamente desde el sistema central apenas se cierra la nómina.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 p-4 text-center">
                        <div class="feature-icon">🧾</div>
                        <h5>Recibos Digitales</h5>
                        <p class="small text-muted">Accede a tus constancias de pago de forma inmediata y segura desde cualquier lugar.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 p-4 text-center">
                        <div class="feature-icon">📧</div>
                        <h5>Notificaciones</h5>
                        <p class="small text-muted">Recibe alertas por correo electrónico cuando tus documentos estén listos para descargar.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-4 text-center border-top">
          <p class="text-muted small">&copy; {{ date('Y') }} Hecho por la Oficina de Tecnología de la Información y Comunicacion</p>
    </footer>
    </footer>

    <script>
        function showLoading() {
            // Mostrar el overlay de carga
            document.getElementById('loader-overlay').style.display = 'flex';

            // Simular carga de 2 segundos antes de ir al login
            setTimeout(function() {
                window.location.href = "{{ route('login') }}";
            }, 2000);
        }
    </script>

</body>
</html>
