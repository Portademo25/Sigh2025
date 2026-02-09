<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
    <div class="card-header py-3 bg-primary d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-white">
            <i class="fas fa-history mr-2"></i>Historial de Constancias Emitidas (Auditoría)
        </h6>

        <a href="<?php echo e(route('admin.reportes.menu')); ?>" class="btn btn-light btn-sm shadow-sm text-primary font-weight-bold">
            <i class="fas fa-arrow-left mr-1"></i> Volver al Menú
        </a>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="bg-light">
                    <tr>
                        <th>Fecha/Hora</th>
                        <th>Cédula</th>
                        <th>Trabajador</th>
                        <th>Unidad</th>
                        <th>Sueldo (Bs.)</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $reporte; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="align-middle"><?php echo e(\Carbon\Carbon::parse($item->fecha_generacion)->format('d/m/Y h:i A')); ?></td>
                        <td class="align-middle"><?php echo e(number_format($item->cedula, 0, '', '.')); ?></td>
                        <td class="align-middle">
                            <div class="font-weight-bold"><?php echo e($item->nombre_completo); ?></div>
                            <small class="text-muted"><?php echo e($item->cargo); ?></small>
                        </td>
                        <td class="align-middle small"><?php echo e($item->unidad); ?></td>
                        <td class="align-middle text-right font-weight-bold">
                            <?php echo e(number_format($item->sueldo_integral, 2, ',', '.')); ?>

                        </td>
                        <td class="text-center align-middle">
                            <a href="<?php echo e(route('constancia.verificar', $item->token)); ?>"
                               target="_blank"
                               class="btn btn-sm btn-info shadow-sm">
                               <i class="fas fa-search"></i> Validar
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">No hay registros de constancias generadas aún.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4 d-flex justify-content-center">
            <?php echo e($reporte->links()); ?>

        </div>
    </div>
</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/Laravel/Sigh2025/resources/views/admin/reportes/index_constancias.blade.php ENDPATH**/ ?>