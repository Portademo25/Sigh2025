<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Institucional - RRHH</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Public Sans', sans-serif; color: #334155; }
        :root {
            --fona-blue: #1e3a8a;
            --fona-cyan: #06b6d4;
            --fona-dark: #0f172a;
        }

        body { font-family: 'Public Sans', sans-serif; background-color: #f8fafc; color: #334155; }

        /* NAVBAR PREMIUM */
        .navbar { background: rgba(255, 255, 255, 0.8) !important; backdrop-filter: blur(10px); border-bottom: 1px solid rgba(0,0,0,0.05); }
        .navbar-brand img { height: 50px; transition: 0.3s; }
        .navbar-brand:hover img { transform: scale(1.05); }

        /* HERO SECTION BRUTAL */
        .hero-section {
            position: relative;
            background: var(--fona-dark);
            min-height: 500px;
            display: flex;
            align-items: center;
            overflow: hidden;
            color: white;
            padding: 80px 0;
        }

        /* Animación de esferas de luz al fondo */
        .hero-section::before {
            content: "";
            position: absolute;
            top: -10%; left: -10%;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.3) 0%, transparent 70%);
            border-radius: 50%;
            filter: blur(50px);
            animation: move 15s infinite alternate;
        }

        @keyframes move {
            from { transform: translate(0, 0); }
            to { transform: translate(100px, 50px); }
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .btn-fona {
            background: linear-gradient(90deg, #06b6d4, #3b82f6);
            border: none; color: white; padding: 12px 30px;
            border-radius: 12px; font-weight: 700;
            transition: all 0.3s ease;
            box-shadow: 0 10px 15px -3px rgba(6, 182, 212, 0.3);
        }

        .btn-fona:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 25px -5px rgba(6, 182, 212, 0.4);
            color: white;
        }

        /* FEATURES SECTION */
        .feature-box {
            background: white; border-radius: 20px; padding: 30px;
            border: 1px solid #e2e8f0; transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .feature-box:hover {
            transform: scale(1.05); border-color: var(--fona-cyan);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .icon-circle {
            width: 60px; height: 60px; background: #eff6ff;
            border-radius: 15px; display: flex; align-items: center;
            justify-content: center; margin-bottom: 20px; color: #3b82f6;
        }

        /* LOADER */
        #loader-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: var(--fona-dark); display: none; justify-content: center;
            align-items: center; z-index: 9999; flex-direction: column;
        }
        .custom-loader {
            width: 60px; height: 60px; border-radius: 50%;
            background: conic-gradient(#0000 10%,#06b6d4);
            -webkit-mask: radial-gradient(farthest-side,#0000 calc(100% - 8px),#000 0);
            animation: s3 1s infinite linear;
        }
        @keyframes s3 { 100% { transform: rotate(1turn); } }
        .card { border: none; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); transition: 0.3s; }
        .card:hover { transform: translateY(-5px); }

        /* Estilos del Loading (Oculto por defecto) */
        
    </style>
</head>
<body>

    <div id="loader-overlay">
        <div class="custom-loader"></div>
        <h5 class="text-white mt-4" style="letter-spacing: 2px;">SINCRONIZANDO CON SIGESP...</h5>
    </div>

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="https://www.fona.gob.ve" target="_blank">
                <img src="{{ asset('images/logo_fona.png') }}" alt="Logo FONA" class="me-2">
                <div class="d-none d-md-block">
                    <span class="d-block fs-6 fw-extrabold text-primary mb-0">FONA</span>
                    <small class="text-muted" style="font-size: 10px;">FONDO NACIONAL ANTIDROGAS</small>
                </div>
            </a>
            <button class="btn btn-fona" onclick="showLoading()">
                <i class="fas fa-sign-in-alt me-2"></i> INGRESAR AL PORTAL
            </button>
        </div>
    </nav>
   <header class="hero-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <div class="glass-card">
                        <span class="badge rounded-pill bg-info mb-3 px-3 py-2 text-uppercase fw-bold" style="font-size: 11px;">Oficina de Tecnología e Información</span>
                        <h1 class="display-3 fw-extrabold mb-4">Bienvenido al <span class="text-info">Portal de Personal</span></h1>
                        <p class="lead mb-5 text-light opacity-75">
                            Tu espacio digital para la autogestión de nómina, constancias y beneficios. 
                            Tecnología al servicio del servidor público.
                        </p>
                        <div class="d-flex justify-content-center gap-3">
                            <button class="btn btn-fona btn-lg" onclick="showLoading()">
                                <i class="fas fa-fingerprint me-2"></i> COMENZAR AHORA
                            </button>
                        </div>
                    </div>
                </div>
            </div>
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
