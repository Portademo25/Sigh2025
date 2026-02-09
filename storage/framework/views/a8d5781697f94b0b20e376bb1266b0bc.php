<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light">
            <tr>
                <th>Trabajador</th>
                <th>Cédula</th>
                <th>Correo Electrónico</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td>
                    <div class="fw-bold"><?php echo e($user->name); ?></div>
                    <small class="text-muted text-uppercase"><?php echo e($user->codper); ?></small>
                </td>
                <td><?php echo e($user->cedula); ?></td>
                <td>
                    <span id="email-display-<?php echo e($user->id); ?>" class="text-lowercase"><?php echo e($user->email); ?></span>
                </td>
                <td class="text-center">
                    <button class="btn btn-outline-primary btn-sm px-3 shadow-sm d-inline-flex align-items-center"
        onclick="editEmail('<?php echo e($user->id); ?>', '<?php echo e($user->email); ?>', '<?php echo e($user->name); ?>')">
    <i class="bi bi-envelope-plus me-2"></i> Editar Correo
</button>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="4" class="text-center p-4 text-muted">No se encontraron trabajadores registrados.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-center mt-3" id="user-pagination">
    <?php echo e($users->links()); ?>

</div>
<?php /**PATH /var/www/html/Laravel/Sigh2025/resources/views/admin/settings/partials/users_table.blade.php ENDPATH**/ ?>