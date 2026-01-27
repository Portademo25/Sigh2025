@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Configuración General</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Configuraciones</a></li>
        <li class="breadcrumb-item active">General</li>
    </ol>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-primary text-white py-3">
            <h5 class="card-title mb-0"><i class="bi bi-info-circle me-2"></i>Información de la Institución y Nómina</h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.settings.general.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-lg-8">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Nombre del Organismo</label>
                                <input type="text" name="institucion_nombre" class="form-control" value="{{ $config['institucion_nombre'] ?? 'Mi Institución' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">RIF</label>
                                <input type="text" name="institucion_rif" class="form-control" value="{{ $config['institucion_rif'] ?? 'G-00000000-0' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Siglas / Nombre Corto</label>
                                <input type="text" name="institucion_siglas" class="form-control" value="{{ $config['institucion_siglas'] ?? 'SIGESP' }}">
                            </div>

                            <div class="col-md-12 mt-4">
                                <div class="p-3 bg-light border-start border-success border-4 rounded shadow-sm">
                                    <h6 class="text-success fw-bold mb-3"><i class="bi bi-cash-stack me-2"></i>Parámetros Globales de Nómina</h6>
                                    <div class="row align-items-center">
                                        <div class="col-md-7">
                                            <label class="form-label small fw-bold text-dark">Monto Mensual Cestaticket (Bs.)</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white text-success fw-bold">Bs.</span>
                                                <input type="number" step="0.01" name="monto_cestaticket"
                                                       class="form-control form-control-lg border-success text-success fw-bold"
                                                       value="{{ $config['monto_cestaticket'] ?? '0.00' }}">
                                            </div>
                                            <div class="form-text text-muted">Este valor se aplicará automáticamente en todos los cálculos y reportes del portal.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 mt-3">
                                <label class="form-label fw-bold">Dirección Fiscal</label>
                                <textarea name="institucion_direccion" class="form-control" rows="3">{{ $config['institucion_direccion'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 text-center border-start">
                        <label class="form-label fw-bold d-block mb-3">Logo del Sistema</label>
                        <div class="mb-3">
                            <div class="p-3 border rounded bg-light d-inline-block shadow-sm">
                                <img id="logo-preview"
                                     src="{{ isset($config['logo_path']) ? asset('storage/'.$config['logo_path']) : asset('img/default-logo.png') }}"
                                     alt="Logo" style="max-height: 150px; width: auto;">
                            </div>
                        </div>
                        <div class="input-group input-group-sm mb-3 px-3">
                            <input type="file" name="logo_archivo" class="form-control" id="input-logo" accept="image/*">
                        </div>
                        <small class="text-muted d-block px-3">Se recomienda PNG con fondo transparente.</small>
                    </div>
                </div>

                <hr class="my-5">

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm bg-light">
                            <div class="card-header bg-dark text-white py-3">
                                <h6 class="mb-0"><i class="bi bi-hdd-fill me-2"></i>Base de Datos Portal (Local)</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <label class="small fw-bold">Host / IP</label>
                                    <input type="text" name="db_local_host" class="form-control" value="{{ $config['db_local_host'] ?? '127.0.0.1' }}">
                                </div>
                                <div class="mb-2">
                                    <label class="small fw-bold">Nombre de la Base de Datos</label>
                                    <input type="text" name="db_local_name" class="form-control" value="{{ $config['db_local_name'] ?? '' }}">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="small fw-bold">Usuario</label>
                                        <input type="text" name="db_local_user" class="form-control" value="{{ $config['db_local_user'] ?? '' }}">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="small fw-bold">Contraseña</label>
                                        <input type="password" name="db_local_pass" class="form-control" placeholder="••••••••">
                                    </div>
                                </div>
                                <button type="button" id="btn-test-local" class="btn btn-outline-dark btn-sm w-100 mt-2">
                                    Probar Conexión Local
                                </button>
                                <div id="result-local" class="text-center mt-2"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm bg-light">
                            <div class="card-header bg-danger text-white py-3">
                                <h6 class="mb-0"><i class="bi bi-database-fill-lock me-2"></i>Base de Datos SIGESP </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8 mb-2">
                                        <label class="small fw-bold">Host / IP</label>
                                        <input type="text" name="db_sigesp_host" class="form-control" value="{{ $config['db_sigesp_host'] ?? '' }}">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="small fw-bold">Puerto</label>
                                        <input type="text" name="db_sigesp_port" class="form-control" value="{{ $config['db_sigesp_port'] ?? '5432' }}">
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label class="small fw-bold">Nombre de la Base de Datos</label>
                                    <input type="text" name="db_sigesp_name" class="form-control" value="{{ $config['db_sigesp_name'] ?? '' }}">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="small fw-bold">Usuario SIGESP</label>
                                        <input type="text" name="db_sigesp_user" class="form-control" value="{{ $config['db_sigesp_user'] ?? '' }}">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="small fw-bold">Contraseña SIGESP</label>
                                        <input type="password" name="db_sigesp_pass" class="form-control" placeholder="••••••••">
                                    </div>
                                </div>
                                <button type="button" id="btn-test-sigesp" class="btn btn-outline-danger btn-sm w-100 mt-2">
                                    Probar Conexión SIGESP
                                </button>
                                <div id="result-sigesp" class="text-center mt-2"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top text-end">
                    <button type="submit" class="btn btn-success px-5 shadow-sm">
                        <i class="bi bi-save me-2"></i>Guardar Cambios Globales
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary fw-bold">
                <i class="bi bi-people-fill me-2"></i> Gestión de Trabajadores
            </h5>
            <div class="input-group input-group-sm w-auto">
                <input type="text" id="user-search" class="form-control" placeholder="Cédula o nombre..." onkeyup="delaySearch()">
                <button class="input-group-text bg-light" id="btn-search-trigger" style="cursor:pointer">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>

        <div id="users-table-container">
            <div class="text-center p-5">
                <div class="spinner-border text-primary spinner-border-sm" role="status"></div>
                <p class="mt-2 small text-muted">Cargando trabajadores...</p>
            </div>
        </div>
    </div>
</div>
        </div>
</div>

<!-- Modal Edición Rápida de Email -->
<div class="modal fade" id="modalEditEmail" tabindex="-1" aria-labelledby="modalEditEmailLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalEditEmailLabel">
                    <i class="bi bi-envelope-at-fill me-2"></i>Actualizar Correo
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formQuickEmail">
                <div class="modal-body p-4">
                    <input type="hidden" id="modal-user-id" name="user_id">

                    <div class="mb-3">
                        <label class="form-label fw-bold mb-1">Trabajador:</label>
                        <div id="modal-user-name" class="form-control-plaintext text-primary fw-bold py-0 mb-3"></div>

                        <label class="form-label fw-bold">Nuevo Correo Electrónico:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary">
                                <i class="bi bi-envelope-fill"></i>
                            </span>
                            <input type="email" id="modal-user-email" name="email"
                                   class="form-control border-start-0"
                                   placeholder="ejemplo@correo.com" required>
                        </div>
                        <div class="form-text mt-2 small">
                            <i class="bi bi-info-circle me-1"></i> Ingrese la dirección institucional o personal activa.
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" id="btnSaveEmail" class="btn btn-primary px-4 shadow-sm">
                        <i class="bi bi-check2-circle me-1"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
/**
 * 1. FUNCIONES GLOBALES
 * (Se definen fuera para que los botones cargados por AJAX las encuentren)
 */
let searchTimer;

// Función para buscar mientras escribes (Debounce)
window.delaySearch = function() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(function() {
        const query = document.getElementById('user-search').value;
        window.loadUsers(1, query);
    }, 500);
};

// UNIFICADA: Función de carga única que soporta paginación y búsqueda
window.loadUsers = function(page = 1, search = '') {
    const container = document.getElementById('users-table-container');
    if (!container) return;

    // Feedback visual
    container.style.opacity = '0.5';

    // Si no se pasa búsqueda, intentamos tomarla del input directamente
    if (search === '') {
        const input = document.getElementById('user-search');
        search = input ? input.value : '';
    }

    fetch(`{{ route("admin.settings.fetch_users") }}?page=${page}&search=${encodeURIComponent(search)}`)
    .then(res => res.text())
    .then(html => {
        container.innerHTML = html;
        container.style.opacity = '1';

        // Re-vincular eventos de paginación
        document.querySelectorAll('#user-pagination a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                let pageNumber = new URL(this.href).searchParams.get('page');
                window.loadUsers(pageNumber, search);
            });
        });
    })
    .catch(err => {
        console.error("Error cargando usuarios:", err);
        container.style.opacity = '1';
    });
};

// Función para abrir el modal de edición de email
window.editEmail = function(id, email, name) {
    try {
        document.getElementById('modal-user-id').value = id;
        document.getElementById('modal-user-email').value = email;
        document.getElementById('modal-user-name').innerText = name;

        const modalEl = document.getElementById('modalEditEmail');
        if (typeof bootstrap !== 'undefined') {
            const modalInst = bootstrap.Modal.getOrCreateInstance(modalEl);
            modalInst.show();
        } else {
            alert("Error: Bootstrap no está cargado.");
        }
    } catch (err) {
        console.error("Error en editEmail:", err);
    }
};

/**
 * 2. INICIALIZACIÓN (DOM READY)
 */
document.addEventListener('DOMContentLoaded', function() {

    // A. Carga inicial
    window.loadUsers();

    // B. Lupa de búsqueda
    const btnSearch = document.getElementById('btn-search-trigger');
    if(btnSearch) {
        btnSearch.addEventListener('click', function() {
            const query = document.getElementById('user-search').value;
            window.loadUsers(1, query);
        });
    }

    // C. Guardar Email por AJAX
    const formQuickEmail = document.getElementById('formQuickEmail');
    if (formQuickEmail) {
        formQuickEmail.addEventListener('submit', function(e) {
            e.preventDefault();

            const id = document.getElementById('modal-user-id').value;
            const newEmail = document.getElementById('modal-user-email').value;
            const btn = document.getElementById('btnSaveEmail');

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';

            // Ruta ajustada a tu estructura de controlador
            fetch(`/admin/users/${id}/update-email`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email: newEmail })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    // Actualizar texto en la tabla
                    const celdaEmail = document.getElementById(`email-display-${id}`);
                    if(celdaEmail) celdaEmail.innerText = newEmail;

                    // Cerrar modal correctamente
                    const modalEl = document.getElementById('modalEditEmail');
                    const modalInst = bootstrap.Modal.getInstance(modalEl);
                    if (modalInst) modalInst.hide();

                    alert("¡Correo actualizado con éxito!");
                } else {
                    alert('Error: ' + (data.message || 'No se pudo actualizar'));
                }
            })
            .catch(error => alert("Error de conexión al servidor"))
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = 'Guardar';
            });
        });
    }

    // D. Función Maestra para pruebas de conexión
    window.probarConexion = function(btnId, resultId, route, inputs) {
        const btn = document.getElementById(btnId);
        const resultDiv = document.getElementById(resultId);
        if (!btn) return;

        btn.addEventListener('click', function() {
            btn.disabled = true;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Probando...';
            resultDiv.innerHTML = '';

            const formData = new FormData();
            inputs.forEach(name => {
                const input = document.querySelector(`input[name="${name}"]`);
                if (input) formData.append(name, input.value);
            });

            fetch(route, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    resultDiv.innerHTML = `<div class="alert alert-success mt-2 py-1 px-2 small"><i class="bi bi-check-circle-fill"></i> ${data.message}</div>`;
                } else {
                    resultDiv.innerHTML = `<div class="alert alert-danger mt-2 py-1 px-2 small"><i class="bi bi-exclamation-triangle-fill"></i> ${data.message}</div>`;
                }
            })
            .catch(err => {
                resultDiv.innerHTML = `<div class="alert alert-warning mt-2 py-1 px-2 small">Error de comunicación</div>`;
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        });
    };

    // Inicializar botones de test
    probarConexion('btn-test-local', 'result-local', '{{ route("admin.settings.test_local") }}',
        ['db_local_host', 'db_local_name', 'db_local_user', 'db_local_pass', 'db_local_port']);

    probarConexion('btn-test-sigesp', 'result-sigesp', '{{ route("admin.settings.test_sigesp") }}',
        ['db_sigesp_host', 'db_sigesp_port', 'db_sigesp_name', 'db_sigesp_user', 'db_sigesp_pass']);
});
</script>
@endsection
