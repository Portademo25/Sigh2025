<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">🔒 Usuarios Bloqueados (Acceso de Administrador)</h2>
                    <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-secondary btn-sm"  style="">
                        ← Tablero de Administración
                    </a>
                </div>

                <div class="card-body">
                    
                    <?php if(session('success')): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>

                    <?php if($lockedUsers->isEmpty()): ?>
                        <div class="alert alert-info">
                            No hay usuarios bloqueados actualmente.
                        </div>
                    <?php else: ?>
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Estado</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $lockedUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($user->id); ?></td>
                                    <td><?php echo e($user->name); ?></td>
                                    <td><?php echo e($user->email); ?></td>
                                    <td>
                                        <span class="badge bg-danger text-white">Bloqueado</span>
                                    </td>
                                    <td>
                                        <form action="<?php echo e(route('admin.users.unlock', $user)); ?>" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres DESBLOQUEAR a <?php echo e($user->name); ?>?');">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-sm btn-success">
                                                Desbloquear
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>


                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/Laravel/Sigh2025/resources/views/admin/locked_users.blade.php ENDPATH**/ ?>