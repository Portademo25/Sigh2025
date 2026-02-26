<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold"><i class="bi bi-shield-check text-primary me-2"></i>Centro de Seguridad</h2>
            <p class="text-muted">Gestione la integridad del portal, el acceso de los empleados y la auditoría de procesos.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 fw-bold">
                    <i class="bi bi-broadcast me-2"></i>Disponibilidad del Sistema
                </div>
                <div class="card-body">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h6 class="mb-0 fw-bold">Modo Mantenimiento</h6>
            <small class="text-muted">Desactiva el acceso al portal para usuarios no administrativos.</small>
        </div>
        <div class="form-check form-switch">
            <form action="<?php echo e(route('admin.security.toggle-maintenance')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn <?php echo e(($config['site_offline'] ?? '0') == '1' ? 'btn-danger' : 'btn-outline-secondary'); ?> btn-sm">
                    <?php if(($config['site_offline'] ?? '0') == '1'): ?>
                        <i class="bi bi-pause-btn-fill me-1"></i> Desactivar Mantenimiento
                    <?php else: ?>
                        <i class="bi bi-play-btn-fill me-1"></i> Activar Mantenimiento
                    <?php endif; ?>
                </button>
            </form>
        </div>
    </div>

    <?php if(($config['site_offline'] ?? '0') == '1'): ?>
        <div class="alert alert-warning py-2 small mb-0">
            <i class="bi bi-exclamation-triangle me-1"></i> El sistema se encuentra actualmente fuera de línea para los usuarios.
        </div>
    <?php endif; ?>
</div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 fw-bold">
                    <i class="bi bi-lock me-2"></i>Políticas de Acceso
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Bloqueo por intentos fallidos
                            <span class="badge bg-primary rounded-pill"><?php echo e($config['intentos_maximos'] ?? '3'); ?> intentos</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Cierre de sesión automático
                            <span class="badge bg-secondary rounded-pill"><?php echo e($config['duracion_bloqueo'] ?? '15'); ?> min</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Cierre de sesión automático por inactividad
                            <span class="badge bg-secondary rounded-pill"> <?php echo e($config['expiracion_sesion'] ?? '120'); ?> min</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Cifrado de datos (SSL)
                             <?php if(request()->isSecure()): ?>
                                <span class="text-success"><i class="bi bi-shield-fill-check"></i> Activo</span>
                                     <?php else: ?>
                                         <span class="text-warning"><i class="bi bi-shield-slash"></i> No seguro</span>
                                    <?php endif; ?>
                        </li>
                    </ul>

                        <a href="<?php echo e(route('admin.security.policies')); ?>" class="btn btn-light btn-sm w-100 mt-3 border">
                            Editar Políticas
                        </a>

                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="bi bi-journal-text me-2"></i>Eventos Críticos de Seguridad</span>
                    <button class="btn btn-sm btn-outline-secondary">Descargar Log Completo</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="small text-uppercase">
                                    <th class="ps-3">Evento</th>
                                    <th>Usuario</th>
                                    <th>Dirección IP</th>
                                    <th>Fecha y Hora</th>
                                    <th class="text-end pe-3">Gravedad</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                <tr>
                                    <td class="ps-3">
                                        <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                                        Intento de login fallido
                                    </td>
                                    <td>25.523.307</td>
                                    <td>192.168.1.45</td>
                                    <td>Hoy, 10:15 AM</td>
                                    <td class="text-end pe-3"><span class="badge bg-warning text-dark">Media</span></td>
                                </tr>
                                <tr>
                                    <td class="ps-3">
                                        <i class="bi bi-trash text-danger me-2"></i>
                                        Eliminación de registros
                                    </td>
                                    <td>Admin_Luis</td>
                                    <td>201.243.10.5</td>
                                    <td>Ayer, 04:30 PM</td>
                                    <td class="text-end pe-3"><span class="badge bg-danger">Alta</span></td>
                                </tr>
                                <tr>
                                    <td class="ps-3">
                                        <i class="bi bi-key text-info me-2"></i>
                                        Cambio de contraseña
                                    </td>
                                    <td>10.455.122</td>
                                    <td>186.24.55.12</td>
                                    <td>12 Ene 2026</td>
                                    <td class="text-end pe-3"><span class="badge bg-info text-dark">Baja</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white text-center py-3">
                    <p class="small text-muted mb-0">Mostrando los últimos 15 eventos de seguridad registrados localmente.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0 bg-dark text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1"><i class="bi bi-terminal me-2"></i>Mantenimiento de Base de Datos</h5>
                        <p class="small mb-0 opacity-75">Optimice las tablas locales y purgue archivos temporales para mejorar el rendimiento.</p>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-outline-light btn-sm">Optimizar Tablas</button>
                        <button class="btn btn-outline-light btn-sm">Limpiar Caché</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalMantenimiento" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo e(route('admin.security.action')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="toggle_maintenance">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirmar Cierre del Portal</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro de activar el <strong>Modo Mantenimiento</strong>?</p>
                    <ul class="small">
                        <li>Los empleados no podrán iniciar sesión.</li>
                        <li>Las descargas de constancias y recibos quedarán inhabilitadas.</li>
                        <li>Usted podrá seguir trabajando si usa el enlace secreto.</li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Sí, activar mantenimiento</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/Laravel/Sigh2025/resources/views/admin/security/index.blade.php ENDPATH**/ ?>