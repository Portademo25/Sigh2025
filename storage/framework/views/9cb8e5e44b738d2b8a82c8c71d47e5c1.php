<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold"><i class="bi bi-shield-lock-fill"></i> Control de Roles y Accesos</h3>
            <p class="text-muted small">Cambia los privilegios de los usuarios registrados en el sistema.</p>
        </div>
        <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn btn-secondary btn-sm">
            <i class="bi bi-house"></i> Volver al Panel
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success border-0 shadow-sm mb-3">
            <i class="bi bi-check-circle-fill me-2"></i> <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if($errors->has('error')): ?>
        <div class="alert alert-danger border-0 shadow-sm mb-3">
            <i class="bi bi-exclamation-octagon-fill me-2"></i> <?php echo e($errors->first('error')); ?>

        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">Usuario</th>
                            <th>Cédula</th>
                            <th>Nivel de Acceso</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $usuarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold"><?php echo e($user->name); ?></div>
                                <div class="text-muted small"><?php echo e($user->email); ?></div>
                            </td>
                            <td><?php echo e($user->cedula); ?></td>
                            <td>
                                <?php if($user->rol_id == 1): ?>
                                    <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger">
                                        Administrador
                                    </span>
                                <?php else: ?>
                                    <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary">
                                        Empleado (Consulta)
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <form action="<?php echo e(route('admin.settings.roles.update', $user)); ?>" method="POST" class="d-flex justify-content-center gap-2">
                                    <?php echo csrf_field(); ?>
                                    <select name="rol_id" class="form-select form-select-sm" style="width: 140px;">
                                        <option value="1" <?php echo e($user->rol_id == 1 ? 'selected' : ''); ?>>Admin</option>
                                        <option value="2" <?php echo e($user->rol_id == 2 ? 'selected' : ''); ?>>Empleado</option>
                                    </select>
                                    <button type="submit" class="btn btn-dark btn-sm" title="Actualizar Rol">
                                        <i class="bi bi-save"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            <?php echo e($usuarios->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/Laravel/Sigh2025/resources/views/admin/settings/roles.blade.php ENDPATH**/ ?>