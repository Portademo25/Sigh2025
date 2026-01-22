@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Configuración General</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Configuraciones</a></li>
        <li class="breadcrumb-item active">General</li>
    </ol>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-primary text-white py-3">
            <h5 class="card-title mb-0"><i class="bi bi-info-circle me-2"></i>Información de la Institución</h5>
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
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Dirección Fiscal</label>
                                <textarea name="institucion_direccion" class="form-control" rows="3">{{ $config['institucion_direccion'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 text-center border-start">
                        <label class="form-label fw-bold d-block mb-3">Logo del Sistema</label>
                        <div class="mb-3">
                            <div class="p-3 border rounded bg-light d-inline-block">
                                <img id="logo-preview"
                                     src="{{ isset($config['logo_path']) ? asset('storage/'.$config['logo_path']) : asset('img/default-logo.png') }}"
                                     alt="Logo" style="max-height: 150px; width: auto;">
                            </div>
                        </div>
                        <div class="input-group input-group-sm mb-3">
                            <input type="file" name="logo_archivo" class="form-control" id="input-logo" accept="image/*">
                        </div>
                        <small class="text-muted">Se recomienda PNG con fondo transparente.</small>
                    </div>
                </div>
                <br>
                <div class="row g-4">
    <div class="col-md-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-primary text-white py-3">
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
                <button type="button" id="btn-test-local" class="btn btn-outline-primary btn-sm w-100 mt-2">
                    Probar Conexión Local
                </button>
                <div id="result-local" class="text-center mt-2"></div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card h-100 border-0 shadow-sm">
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
                        <input type="text" name="db_sigesp_port" class="form-control" value="{{ $config['db_sigesp_port'] ?? '1521' }}">
                    </div>
                </div>
                <div class="mb-2">
                    <label class="small fw-bold">Nombre de la Base de Datos (SID/ServiceName)</label>
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
        <div class="alert alert-info mt-3 small">
            <i class="bi bi-info-circle-fill me-2"></i>
            <strong>Nota:</strong> Estos valores sobrescribirán temporalmente la configuración del archivo .env para las consultas de sincronización.
        </div>
    </div>
</div>
                <div class="mt-4 pt-3 border-top text-end">
                    <button type="submit" class="btn btn-success px-4 shadow-sm">
                        <i class="bi bi-check-circle me-2"></i>Actualizar Configuración
                    </button>
                </div>
            </form>
        </div>
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
                    <p class="mt-2 small text-muted">Sincronizando lista...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditEmail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-2">
                <h6 class="modal-title">Actualizar Correo</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formQuickEmail">
                <div class="modal-body">
                    <p class="small text-muted mb-2">Usuario: <strong id="modal-user-name"></strong></p>
                    <input type="hidden" id="modal-user-id">
                    <div class="mb-2">
                        <label class="small fw-bold">Nuevo Email:</label>
                        <input type="email" id="modal-user-email" class="form-control form-control-sm" required>
                    </div>
                </div>
                <div class="modal-footer py-1">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-sm btn-primary" id="btnSaveEmail">Guardar</button>
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

// Función para buscar mientras escribes
window.delaySearch = function() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(function() {
        const query = document.getElementById('user-search').value;
        window.loadUsers(1, query);
    }, 500);
};

// Función de carga mejorada
window.loadUsers = function(page = 1, search = '') {
    const container = document.getElementById('users-table-container');

    // Mostramos un mini-spinner en el contenedor para feedback visual
    container.style.opacity = '0.5';

    fetch(`{{ route("admin.settings.fetch_users") }}?page=${page}&search=${encodeURIComponent(search)}`)
    .then(res => res.text())
    .then(html => {
        container.innerHTML = html;
        container.style.opacity = '1';

        // Re-vincular eventos de paginación para que no pierdan la búsqueda
        document.querySelectorAll('#user-pagination a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                let pageNumber = new URL(this.href).searchParams.get('page');
                window.loadUsers(pageNumber, document.getElementById('user-search').value);
            });
        });
    })
    .catch(err => {
        console.error("Error:", err);
        container.style.opacity = '1';
    });
};

// Hacer que el botón de la lupa también funcione al hacer clic
document.addEventListener('DOMContentLoaded', function() {
    const btnSearch = document.getElementById('btn-search-trigger');
    if(btnSearch) {
        btnSearch.addEventListener('click', function() {
            const query = document.getElementById('user-search').value;
            window.loadUsers(1, query);
        });
    }
});
// Función para abrir el modal de edición de email
window.editEmail = function(id, email, name) {
    console.log("Intentando editar:", name);

    try {
        // 1. Asignar valores
        document.getElementById('modal-user-id').value = id;
        document.getElementById('modal-user-email').value = email;
        document.getElementById('modal-user-name').innerText = name;

        // 2. Abrir la modal (Método compatible con BS5)
        const modalEl = document.getElementById('modalEditEmail');

        // Verificamos si bootstrap existe
        if (typeof bootstrap !== 'undefined') {
            const modalInst = bootstrap.Modal.getOrCreateInstance(modalEl);
            modalInst.show();
        } else {
            console.error("Bootstrap JS no detectado. Revisa tus scripts.");
            alert("Error: Bootstrap no está cargado.");
        }
    } catch (err) {
        console.error("Error en editEmail:", err);
    }
};
// Función para cargar la tabla de usuarios por AJAX
window.loadUsers = function(page = 1) {
    const container = document.getElementById('users-table-container');
    if (!container) return;

    fetch('{{ route("admin.settings.fetch_users") }}?page=' + page)
    .then(res => res.text())
    .then(html => {
        container.innerHTML = html;

        // Re-vincular eventos de los links de paginación
        document.querySelectorAll('#user-pagination a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                let pageNumber = new URL(this.href).searchParams.get('page');
                window.loadUsers(pageNumber);
            });
        });
    })
    .catch(err => console.error("Error cargando usuarios:", err));
};


/**
 * 2. INICIALIZACIÓN CUANDO EL DOM ESTÁ LISTO
 */
document.addEventListener('DOMContentLoaded', function() {

    // A. Cargar usuarios inicialmente
    window.loadUsers();

    // B. Lógica para guardar el email editado (AJAX)
    const formQuickEmail = document.getElementById('formQuickEmail');
    if (formQuickEmail) {
        formQuickEmail.addEventListener('submit', function(e) {
            e.preventDefault();

            const id = document.getElementById('modal-user-id').value;
            const newEmail = document.getElementById('modal-user-email').value;
            const btn = document.getElementById('btnSaveEmail');

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

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
                    const celdaEmail = document.getElementById(`email-display-${id}`);
                    if(celdaEmail) celdaEmail.innerText = newEmail;

                    const modalInst = bootstrap.Modal.getInstance(document.getElementById('modalEditEmail'));
                    if (modalInst) modalInst.hide();
                    alert("¡Correo actualizado con éxito!");
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => alert("Error de conexión al servidor"))
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = 'Guardar';
            });
        });
    }

    // C. Función Maestra para pruebas de conexión
    function probarConexion(btnId, resultId, route, inputs) {
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
    }

    // D. Inicializar pruebas de conexión
    probarConexion('btn-test-local', 'result-local', '{{ route("admin.settings.test_local") }}',
        ['db_local_host', 'db_local_name', 'db_local_user', 'db_local_pass', 'db_local_port']);

    probarConexion('btn-test-sigesp', 'result-sigesp', '{{ route("admin.settings.test_sigesp") }}',
        ['db_sigesp_host', 'db_sigesp_port', 'db_sigesp_name', 'db_sigesp_user', 'db_sigesp_pass']);

});
</script>
@endsection
