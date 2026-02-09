<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0">Dashboard - Empleado</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card text-white bg-primary mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Mi Perfil</h5>
                                    <p class="card-text">Ver y editar mi perfil</p>
                                    <a href="<?php echo e(route('empleado.perfil')); ?>" class="btn btn-light">Acceder</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card text-white bg-secondary mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Reportes</h5>
                                    <p class="card-text">Ver reportes disponibles</p>
                                    <a href="<?php echo e(route('empleado.reportes.menu')); ?>" class="btn btn-light">Acceder</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/Sigh2025/resources/views/empleado/dashboard.blade.php ENDPATH**/ ?>