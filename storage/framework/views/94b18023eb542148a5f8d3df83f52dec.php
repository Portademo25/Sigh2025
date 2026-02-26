<?php $__env->startSection('title', 'Inicio'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><?php echo e(__('Dashboard')); ?></div>

                <div class="card-body">
                    <?php if(session('status')): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo e(session('status')); ?>

                        </div>
                    <?php endif; ?>

                    <div class="alert alert-info">
                        <h4>Bienvenido al Sistema</h4>
                        <p>Esta es la página de inicio. Si ves esto, significa que no tienes un rol específico asignado.</p>

                        <?php if(auth()->guard()->check()): ?>
                        <p>
                            <strong>Tu usuario:</strong> <?php echo e(auth()->user()->name); ?><br>
                            <strong>Email:</strong> <?php echo e(auth()->user()->email); ?><br>
                            <strong>Roles:</strong>
                            <?php if(auth()->user()->roles->count() > 0): ?>
                                <?php $__currentLoopData = auth()->user()->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="badge bg-primary"><?php echo e($role->name); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <span class="badge bg-warning">Sin rol asignado</span>
                            <?php endif; ?>
                        </p>
                        <?php endif; ?>
                    </div>

                    <?php if(auth()->guard()->guest()): ?>
                    <div class="text-center">
                        <p>Por favor, inicia sesión para acceder al sistema.</p>
                        <a href="<?php echo e(route('login')); ?>" class="btn btn-primary">Iniciar Sesión</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php /**PATH /var/www/html/Laravel/Sigh2025/resources/views/home.blade.php ENDPATH**/ ?>