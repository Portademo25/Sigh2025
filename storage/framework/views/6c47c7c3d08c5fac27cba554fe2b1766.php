<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-dark text-white d-flex justify-content-between">
                    <h4 class="mb-0">⚙️ Configuración del Sistema</h4>
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn btn-outline-light btn-sm">Regresar</a>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('admin.settings.update')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-4 border-end">
                                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#security"><a href="<?php echo e(route('admin.security.index')); ?>" class="btn btn-light">🛡️ Seguridad</a></button>
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#general"><a href="<?php echo e(route('admin.settings.general')); ?>" class="btn btn-light">💻 General</a></button>
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#roles"><a href="<?php echo e(route('admin.settings.roles')); ?>" class="btn btn-light">👥 Roles y Permisos</a></button>
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#sigesp"><a href="<?php echo e(route('admin.settings.sigesp')); ?>" class="btn btn-light">🔗 Sincronización con SIGESP</a></button>
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#mail"><a href="<?php echo e(route('admin.settings.mail')); ?>" class="btn btn-light"> Configuracion de para Enviar Correo</a></button>
                                </div>
                            </div>

                            <div class="col-md-8">
                                

                                    
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/Sigh2025/resources/views/admin/settings/index.blade.php ENDPATH**/ ?>