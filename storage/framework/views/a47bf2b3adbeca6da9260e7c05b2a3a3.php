<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Configuración General</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.settings.index')); ?>">Configuraciones</a></li>
        <li class="breadcrumb-item active">General</li>
    </ol>

    <?php if($errors->any()): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>¡Ups! Revisa los siguientes errores:</strong>
        <ul class="mb-0 mt-2">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-primary text-white py-3">
            <h5 class="card-title mb-0"><i class="bi bi-info-circle me-2"></i>Información de la Institución y Nómina</h5>
        </div>
        <div class="card-body p-4">
            <form action="<?php echo e(route('admin.settings.general.update')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="row">
                    <div class="col-lg-8">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Nombre del Organismo <span class="text-danger">*</span></label>
                                <input type="text" name="institucion_nombre" 
                                     class="form-control <?php $__errorArgs = ['institucion_nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                     value="<?php echo e(old('institucion_nombre', $config['institucion_nombre'] ?? '')); ?>" 
                                     required>
                                <?php $__errorArgs = ['institucion_nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">RIF <span class="text-danger">*</span></label>
                                <input type="text" name="institucion_rif" 
                                        class="form-control <?php $__errorArgs = ['institucion_rif'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                        value="<?php echo e(old('institucion_rif', $config['institucion_rif'] ?? '')); ?>" 
                                        placeholder="G-12345678-9"
                                        required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Siglas / Nombre Corto</label>
                                <input type="text" name="institucion_siglas" class="form-control" value="<?php echo e($config['institucion_siglas'] ?? 'SIGESP'); ?>">
                            </div>

                            <div class="col-md-12 mt-4">
                                <div class="p-3 bg-light border-start border-success border-4 rounded shadow-sm">
                                    <h6 class="text-success fw-bold mb-3"><i class="bi bi-cash-stack me-2"></i>Parámetros Globales de Nómina</h6>
                                    <div class="row align-items-center">
                                        <div class="col-md-7">
                                           <label class="form-label small fw-bold text-dark">Monto Mensual Cestaticket (Bs.) <span class="text-danger">*</span></label>
                                           <div class="input-group">
                                               <span class="input-group-text bg-white text-success fw-bold">Bs.</span>
                                               <input type="number" step="0.01" min="0" name="monto_cestaticket"
                                                        class="form-control form-control-lg border-success text-success fw-bold <?php $__errorArgs = ['monto_cestaticket'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                        value="<?php echo e(old('monto_cestaticket', $config['monto_cestaticket'] ?? '0.00')); ?>"
                                                        required>
                                            </div>
                                            <div class="form-text text-muted small">Este valor se aplicará automáticamente en todos los cálculos.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 mt-3">
                                <label class="form-label fw-bold">Dirección Fiscal</label>
                                <textarea name="institucion_direccion" class="form-control" rows="3"><?php echo e($config['institucion_direccion'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 text-center border-start">
                        <label class="form-label fw-bold d-block mb-3">Logo del Sistema</label>
                        <div class="mb-3">
                            <div class="p-3 border rounded bg-light d-inline-block shadow-sm">
                                <img id="logo-preview"
                                     src="<?php echo e((isset($config['logo_path']) && !empty($config['logo_path'])) ? asset('storage/'.$config['logo_path']) : asset('img/default-logo.png')); ?>"
                                     alt="Logo" style="max-height: 150px; width: auto;"
                                     onerror="this.src='<?php echo e(asset('img/default-logo.png')); ?>'">
                            </div>
                        </div>
                        <div class="input-group input-group-sm mb-3 px-3">
                            <input type="file" name="logo_archivo" class="form-control" id="input-logo" accept="image/png, image/jpeg">
                        </div>
                        <small class="text-muted d-block px-3">PNG o JPG (Máx. 2MB).</small>
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
                                    <input type="text" name="db_local_host" class="form-control" value="<?php echo e($config['db_local_host'] ?? '127.0.0.1'); ?>">
                                </div>
                                <div class="mb-2">
                                    <label class="small fw-bold">Nombre de la Base de Datos</label>
                                    <input type="text" name="db_local_name" class="form-control" value="<?php echo e($config['db_local_name'] ?? ''); ?>">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="small fw-bold">Usuario</label>
                                        <input type="text" name="db_local_user" class="form-control" value="<?php echo e($config['db_local_user'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="small fw-bold">Contraseña</label>
                                        <div class="input-group">
                                            <input type="password" name="db_local_pass" class="form-control" placeholder="••••••••">
                                            <button class="btn btn-outline-secondary btn-toggle-password" type="button">
                                                <i class="bi bi-eye-slash"></i>
                                            </button>
                                        </div>
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
                                <h6 class="mb-0"><i class="bi bi-database-fill-lock me-2"></i>Base de Datos SIGESP</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8 mb-2">
                                        <label class="small fw-bold">Host / IP</label>
                                        <input type="text" name="db_sigesp_host" class="form-control" value="<?php echo e($config['db_sigesp_host'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="small fw-bold">Puerto</label>
                                        <input type="text" name="db_sigesp_port" class="form-control" value="<?php echo e($config['db_sigesp_port'] ?? '5432'); ?>">
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label class="small fw-bold">Nombre de la Base de Datos</label>
                                    <input type="text" name="db_sigesp_name" class="form-control" value="<?php echo e($config['db_sigesp_name'] ?? ''); ?>">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="small fw-bold">Usuario SIGESP</label>
                                        <input type="text" name="db_sigesp_user" class="form-control" value="<?php echo e($config['db_sigesp_user'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="small fw-bold">Contraseña SIGESP</label>
                                        <div class="input-group">
                                            <input type="password" name="db_sigesp_pass" class="form-control" placeholder="••••••••">
                                            <button class="btn btn-outline-secondary btn-toggle-password" type="button">
                                                <i class="bi bi-eye-slash"></i>
                                            </button>
                                        </div>
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
                    <button type="submit" class="btn btn-success px-5 shadow-sm rounded-pill">
                        <i class="bi bi-save me-2"></i>Guardar Cambios Globales
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm mt-5">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
            <h5 class="mb-0 text-primary fw-bold">
                <i class="bi bi-people-fill me-2"></i> Gestión de Trabajadores
            </h5>
            <div class="input-group input-group-sm w-auto">
                <input type="text" id="user-search" class="form-control" placeholder="Cédula o nombre..." onkeyup="delaySearch()">
                <button class="input-group-text bg-light" id="btn-search-trigger">
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

<div class="modal fade" id="modalEditEmail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-envelope-at-fill me-2"></i>Actualizar Correo
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formQuickEmail">
                <div class="modal-body p-4">
                    <input type="hidden" id="modal-user-id">
                    <div class="mb-3">
                        <label class="form-label fw-bold mb-1">Trabajador:</label>
                        <div id="modal-user-name" class="form-control-plaintext text-primary fw-bold py-0 mb-3 small"></div>

                        <label class="form-label fw-bold small">Nuevo Correo Electrónico:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="bi bi-envelope-fill"></i></span>
                            <input type="email" id="modal-user-email" class="form-control" placeholder="ejemplo@correo.com" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" id="btnSaveEmail" class="btn btn-primary btn-sm px-4 shadow-sm">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
/**
 * 1. FUNCIONES GLOBALES
 */
let searchTimer;

window.delaySearch = function() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(function() {
        const query = document.getElementById('user-search').value;
        window.loadUsers(1, query);
    }, 500);
};

window.loadUsers = function(page = 1, search = '') {
    const container = document.getElementById('users-table-container');
    if (!container) return;
    container.style.opacity = '0.5';

    if (search === '') {
        const input = document.getElementById('user-search');
        search = input ? input.value : '';
    }

    fetch(`<?php echo e(route("admin.settings.fetch_users")); ?>?page=${page}&search=${encodeURIComponent(search)}`)
    .then(res => res.text())
    .then(html => {
        container.innerHTML = html;
        container.style.opacity = '1';
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

window.editEmail = function(id, email, name) {
    try {
        document.getElementById('modal-user-id').value = id;
        document.getElementById('modal-user-email').value = email;
        document.getElementById('modal-user-name').innerText = name;
        const modalEl = document.getElementById('modalEditEmail');
        if (typeof bootstrap !== 'undefined') {
            const modalInst = bootstrap.Modal.getOrCreateInstance(modalEl);
            modalInst.show();
        }
    } catch (err) { console.error(err); }
};

/**
 * 2. INICIALIZACIÓN (DOM READY)
 */
document.addEventListener('DOMContentLoaded', function() {

    window.loadUsers();

    // B. Lupa de búsqueda
    const btnSearch = document.getElementById('btn-search-trigger');
    if(btnSearch) {
        btnSearch.addEventListener('click', function() {
            window.loadUsers(1, document.getElementById('user-search').value);
        });
    }

    // C. Guardar Email por AJAX
    const formQuickEmail = document.getElementById('formQuickEmail');
    if (formQuickEmail) {
        formQuickEmail.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('btnSaveEmail');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            fetch(`/admin/users/${document.getElementById('modal-user-id').value}/update-email`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email: document.getElementById('modal-user-email').value })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    const id = document.getElementById('modal-user-id').value;
                    document.getElementById(`email-display-${id}`).innerText = document.getElementById('modal-user-email').value;
                    bootstrap.Modal.getInstance(document.getElementById('modalEditEmail')).hide();
                } else { alert(data.message); }
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = 'Guardar Cambios';
            });
        });
    }

    // D. Pruebas de conexión
    window.probarConexion = function(btnId, resultId, route, inputs) {
        const btn = document.getElementById(btnId);
        const resultDiv = document.getElementById(resultId);
        if (!btn) return;

        btn.addEventListener('click', function() {
            btn.disabled = true;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            resultDiv.innerHTML = '';

            const formData = new FormData();
            inputs.forEach(name => {
                const input = document.querySelector(`input[name="${name}"]`);
                if (input) formData.append(name, input.value);
            });

            fetch(route, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Accept': 'application/json' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    resultDiv.innerHTML = `<div class="text-success mt-2 small"><i class="bi bi-check-circle-fill"></i> ${data.message}</div>`;
                } else {
                    resultDiv.innerHTML = `<div class="text-danger mt-2 small"><i class="bi bi-x-circle-fill"></i> ${data.message}</div>`;
                }
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        });
    };

    probarConexion('btn-test-local', 'result-local', '<?php echo e(route("admin.settings.test_local")); ?>', ['db_local_host', 'db_local_name', 'db_local_user', 'db_local_pass']);
    probarConexion('btn-test-sigesp', 'result-sigesp', '<?php echo e(route("admin.settings.test_sigesp")); ?>', ['db_sigesp_host', 'db_sigesp_port', 'db_sigesp_name', 'db_sigesp_user', 'db_sigesp_pass']);

    // E. GESTIÓN DE LOGO
    const inputLogo = document.getElementById('input-logo');
    const previewLogo = document.getElementById('logo-preview');
    if (inputLogo && previewLogo) {
        inputLogo.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                if (!['image/jpeg', 'image/png', 'image/jpg'].includes(file.type) || file.size > 2 * 1024 * 1024) {
                    alert("Archivo no válido o muy pesado.");
                    this.value = ''; return;
                }
                const reader = new FileReader();
                reader.onload = (e) => { previewLogo.src = e.target.result; };
                reader.readAsDataURL(file);
            }
        });
    }

    // F. MÁSCARA RIF Y VALIDACIÓN VISUAL
    const inputRif = document.querySelector('input[name="institucion_rif"]');
    if (inputRif) {
        inputRif.addEventListener('input', function(e) {
            let value = e.target.value.toUpperCase().replace(/[^GJV0-9]/g, '');
            if (value.length > 0) {
                let letter = value.charAt(0).match(/[GJV]/) ? value.charAt(0) : 'G';
                let numbers = value.substring(1).replace(/[^0-9]/g, '');
                if (numbers.length > 8) {
                    e.target.value = `${letter}-${numbers.substring(0, 8)}-${numbers.charAt(8)}`;
                } else if (numbers.length > 0) {
                    e.target.value = `${letter}-${numbers}`;
                } else { e.target.value = letter; }
            }
            const isValid = /^[VGJ]-[0-9]{8}-[0-9]$/i.test(e.target.value);
            e.target.classList.toggle('is-valid', isValid);
            e.target.classList.toggle('is-invalid', !isValid && e.target.value.length > 5);
        });
    }

    // G. VALIDACIÓN GENERAL EN TIEMPO REAL
    const inputsToValidate = ['institucion_nombre', 'monto_cestaticket', 'db_local_host'];
    inputsToValidate.forEach(name => {
        const el = document.querySelector(`input[name="${name}"]`);
        if (el) {
            el.addEventListener('input', function() {
                const isValid = el.value.trim() !== '' && (el.type !== 'number' || el.value >= 0);
                el.classList.toggle('is-valid', isValid);
                el.classList.toggle('is-invalid', !isValid);
            });
        }
    });

    // H. VALIDACIÓN ESPECÍFICA PARA SIGESP
    const sigespInputs = {
        'db_sigesp_host': /^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$|^[a-zA-Z0-9.-]+$/,
        'db_sigesp_port': /^[0-9]{2,5}$/,
        'db_sigesp_name': /.+/,
        'db_sigesp_user': /^[a-zA-Z0-9_]{3,30}$/
    };

    Object.keys(sigespInputs).forEach(name => {
        const el = document.querySelector(`input[name="${name}"]`);
        if (el) {
            el.addEventListener('input', function() {
                const pattern = sigespInputs[name];
                const isValid = pattern.test(el.value.trim());
                el.classList.toggle('is-valid', isValid);
                el.classList.toggle('is-invalid', !isValid && el.value.length > 0);
            });
        }
    });

    // ==========================================
    // I. VALIDACIÓN DE CONTRASEÑAS
    // ==========================================
    const passwordFields = ['db_local_pass', 'db_sigesp_pass'];
    passwordFields.forEach(name => {
        const el = document.querySelector(`input[name="${name}"]`);
        if (el) {
            el.addEventListener('input', function() {
                if (el.value.length > 0) {
                    const isSecure = el.value.length >= 4;
                    el.classList.toggle('is-valid', isSecure);
                    el.classList.toggle('is-invalid', !isSecure);
                } else {
                    el.classList.remove('is-valid', 'is-invalid');
                }
            });
        }
    });

    // ==========================================
    // J. FUNCIONALIDAD PARA VER CONTRASEÑAS (OJO)
    // ==========================================
    document.querySelectorAll('.btn-toggle-password').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.closest('.input-group').querySelector('input');
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            }
        });
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/Sigh2025/resources/views/admin/settings/general.blade.php ENDPATH**/ ?>