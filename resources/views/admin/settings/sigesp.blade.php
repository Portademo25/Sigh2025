@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-dark text-white d-flex align-items-center justify-content-between py-3">
                    {{-- BOTÓN REGRESAR BIEN DEFINIDO --}}
                    <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary btn-sm px-3 shadow-sm border-light">
                        <i class="fas fa-chevron-left me-2"></i> Volver al Panel
                    </a>
                    
                    <h5 class="mb-0 text-uppercase tracking-wider small fw-bold">
                        <i class="fas fa-database me-2 text-info"></i> Control SIGESP
                    </h5>
                    
                    <span class="badge rounded-pill bg-success px-3">En Línea</span>
                </div>

                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <div class="display-5 text-primary mb-3">
                            <i class="fas fa-sync-alt fa-spin-hover"></i>
                        </div>
                        <h3 class="fw-bold text-dark">Sincronización Maestra</h3>
                        <p class="text-muted px-4">
                            Se importarán datos de <strong>Empresas, Trabajadores, Nóminas y Periodos</strong> desde la base de datos central.
                        </p>
                    </div>

                    {{-- CUADRO DE ESTADO --}}
                    <div class="card bg-light border-0 mb-4 mx-auto" style="max-width: 400px;">
                        <div class="card-body py-3">
                            <span class="text-muted small d-block mb-1 text-uppercase">Última actualización registrada</span>
                            <h5 class="fw-bold mb-0 {{ $lastSync ? 'text-primary' : 'text-danger' }}">
                                <i class="far fa-calendar-check me-2"></i>
                                {{ $lastSync ?? 'SIN REGISTROS' }}
                            </h5>
                        </div>
                    </div>

                    <form action="{{ route('admin.settings.sigesp.sync') }}" method="POST" id="syncForm">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg px-5 py-3 shadow" id="btnSync">
                            <i class="fas fa-play-circle me-2"></i> Iniciar Proceso de Sincronización
                        </button>
                    </form>

                    <div class="mt-4 p-3 border-top">
                        <p class="small text-muted mb-0">
                            <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                            <strong>Nota:</strong> Evite interrumpir la conexión mientras la barra de progreso esté activa.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById('syncForm').onsubmit = function(e) {
        const btn = document.getElementById('btnSync');
        btn.disabled = true;
        btn.classList.add('btn-secondary');
        btn.innerHTML = '<i class="fas fa-circle-notch fa-spin me-2"></i> Sincronizando...';

        Swal.fire({
            title: '<strong>Procesando Datos SIGESP</strong>',
            icon: 'info',
            html: `
                <div class="text-start mt-3">
                    <p class="mb-2">El sistema está realizando las siguientes tareas:</p>
                    <ul class="small text-muted">
                        <li>Conectando con el servidor central...</li>
                        <li>Validando integridad de nóminas...</li>
                        <li>Actualizando historial de personal...</li>
                    </ul>
                    <div class="progress" style="height: 20px; border-radius: 10px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
                             role="progressbar" style="width: 100%"></div>
                    </div>
                    <p class="mt-3 text-center text-danger fw-bold">¡No cierre esta pestaña!</p>
                </div>
            `,
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });
    };
</script>

<style>
    .fa-spin-hover:hover { animation: fa-spin 1.5s infinite linear; }
    .card { border-radius: 20px; overflow: hidden; }
    .btn-primary { border-radius: 12px; font-weight: 600; letter-spacing: 0.5px; }
    .btn-secondary { border-radius: 8px; transition: all 0.3s; }
    .btn-secondary:hover { background-color: #dee2e6 !important; color: #000 !important; transform: translateX(-3px); }
    .tracking-wider { letter-spacing: 1px; }
</style>
@endsection