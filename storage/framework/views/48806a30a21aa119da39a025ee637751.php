<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card shadow-lg border-0" style="border-radius: 20px; overflow: hidden;">
                
                
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
                    <h4 class="mb-0 fw-bold">⚙️ Configuración del Sistema</h4>
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn btn-secondary btn-sm px-3 shadow-sm border-light">
                        <i class="fas fa-chevron-left me-2"></i> Volver al Inicio
                    </a>
                </div>

                <div class="card-body p-0">
                    <div class="row g-0">
                        
                        <div class="col-md-5 bg-light border-end">
                            <div class="list-group list-group-flush" id="settings-menu">
                                
                                <a href="<?php echo e(route('admin.security.index')); ?>" class="list-group-item list-group-item-action py-4 px-4 d-flex align-items-center menu-item">
                                    <div class="icon-circle bg-white shadow-sm me-3">🛡️</div>
                                    <div>
                                        <h5 class="mb-0 fw-bold">Seguridad</h5>
                                        <small class="text-muted">Bitácoras y registros de acceso</small>
                                    </div>
                                </a>

                                <a href="<?php echo e(route('admin.settings.general')); ?>" class="list-group-item list-group-item-action py-4 px-4 d-flex align-items-center menu-item">
                                    <div class="icon-circle bg-white shadow-sm me-3">💻</div>
                                    <div>
                                        <h5 class="mb-0 fw-bold">General</h5>
                                        <small class="text-muted">Configuración global del sitio</small>
                                    </div>
                                </a>

                                <a href="<?php echo e(route('admin.settings.roles')); ?>" class="list-group-item list-group-item-action py-4 px-4 d-flex align-items-center menu-item">
                                    <div class="icon-circle bg-white shadow-sm me-3">👥</div>
                                    <div>
                                        <h5 class="mb-0 fw-bold">Roles y Permisos</h5>
                                        <small class="text-muted">Gestión de usuarios y niveles</small>
                                    </div>
                                </a>

                                <a href="<?php echo e(route('admin.settings.sigesp')); ?>" class="list-group-item list-group-item-action py-4 px-4 d-flex align-items-center menu-item">
                                    <div class="icon-circle bg-white shadow-sm me-3">🔗</div>
                                    <div>
                                        <h5 class="mb-0 fw-bold">Sincronización SIGESP</h5>
                                        <small class="text-muted">Importar datos del servidor central</small>
                                    </div>
                                </a>

                                
                                <a href="<?php echo e(route('admin.settings.nominas')); ?>" class="list-group-item list-group-item-action py-4 px-4 d-flex align-items-center menu-item">
                                    <div class="icon-circle bg-white shadow-sm me-3">💵</div>
                                    <div>
                                        <h5 class="mb-0 fw-bold">Configuración de Nóminas</h5>
                                        <small class="text-muted">Parámetros de cálculo y conceptos</small>
                                    </div>
                                </a>

                                <a href="<?php echo e(route('admin.settings.mail')); ?>" class="list-group-item list-group-item-action py-4 px-4 d-flex align-items-center menu-item">
                                    <div class="icon-circle bg-white shadow-sm me-3">📧</div>
                                    <div>
                                        <h5 class="mb-0 fw-bold">Configuración de Correo</h5>
                                        <small class="text-muted">Servidor SMTP y notificaciones</small>
                                    </div>
                                </a>

                            </div>
                        </div>

                        
                        <div class="col-md-7 d-flex align-items-center justify-content-center bg-white p-5">
                            <div class="text-center">
                                <div class="icon-circle bg-light mb-4 mx-auto" style="width: 100px; height: 100px; font-size: 3rem; display: flex; align-items: center; justify-content: center;">
                                    ⚙️
                                </div>
                                <h3 class="fw-bold">¡Hola, <?php echo e(Auth::user()->name); ?>!</h3>
                                <p class="text-muted">Bienvenido al núcleo de configuración de <strong>Sigh2025</strong>.</p>
                                <hr class="my-4 mx-auto" style="width: 50px;">
                                <p class="small text-muted">Desde aquí puedes supervisar el enlace con SIGESP,<br> gestionar privilegios, configurar nóminas y auditar el sistema.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .icon-circle {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .menu-item {
        transition: all 0.2s ease;
        border-bottom: 1px solid #eee !important;
    }

    .menu-item:hover {
        background-color: #e9ecef !important;
        padding-left: 2rem !important;
    }

    .btn-secondary:hover {
        transform: translateX(-3px);
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/Laravel/Sigh2025/resources/views/admin/settings/index.blade.php ENDPATH**/ ?>