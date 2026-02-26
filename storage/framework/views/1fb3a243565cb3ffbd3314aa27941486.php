<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">
            <i class="fas fa-coins me-2 text-primary"></i> Selección de Personal para Recibos
        </h2>
        <a href="<?php echo e(route('home')); ?>" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Volver al Menú
        </a>
    </div>

    <div class="card border-0 shadow-sm p-3 mb-4">
        <form action="<?php echo e(route('rrhh.personal.pagos.index')); ?>" method="GET" class="d-flex">
            <input type="text" name="buscar" class="form-control me-2" placeholder="Buscar por cédula o nombre..." value="<?php echo e(request('buscar')); ?>">
            <button type="submit" class="btn btn-primary px-4">
                <i class="fas fa-search me-1"></i> Buscar
            </button>
        </form>
    </div>

    <div class="table-responsive bg-white rounded shadow-sm">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-3">Cédula</th>
                    <th>Nombre Completo</th>
                    <th class="text-center">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $personal; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="ps-3 fw-bold"><?php echo e(number_format($p->cedper, 0, '', '.')); ?></td>
                    <td><?php echo e($p->nomper); ?> <?php echo e($p->apeper); ?></td>
                    <td class="text-center">
                        <a href="<?php echo e(route('rrhh.personal.pagos', $p->cedper)); ?>" class="btn btn-info btn-sm text-white px-3 fw-bold shadow-sm">
                            <i class="fas fa-file-invoice-dollar me-1"></i> Ver Recibos
                        </a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="3" class="text-center py-4 text-muted">
                        No se encontraron resultados para la búsqueda.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        <?php echo e($personal->appends(['buscar' => request('buscar')])->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/Laravel/Sigh2025/resources/views/rrhh/personal/lista_pagos.blade.php ENDPATH**/ ?>