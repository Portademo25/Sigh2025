<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="bi bi-file-earmark-pdf text-danger"></i> Historial de Recibos de Pago</h4>
        <a href="<?php echo e(route('empleado.dashboard')); ?>" class="btn btn-outline-secondary btn-sm">Volver</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Periodo</th>
                        <th>Desde</th>
                        <th>Hasta</th>
                        <th>Monto Neto</th>
                        <th class="text-center">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $recibos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recibo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold">Quincena: <?php echo e($recibo->codperi); ?></span>
                            <div class="small text-muted">Nómina: <?php echo e($recibo->codnom); ?></div>
                        </td>
                        <td><?php echo e(\Carbon\Carbon::parse($recibo->fecdesper)->format('d/m/Y')); ?></td>
                        <td><?php echo e(\Carbon\Carbon::parse($recibo->fechasper)->format('d/m/Y')); ?></td>
                        <td class="fw-bold text-success">
                           <?php echo e(number_format($recibo->monnetres, 2, ',', '.')); ?>

                        </td>
                        <td class="text-center">
                           <a href="<?php echo e(route('empleado.reportes.recibo_pdf', ['codnom' => $recibo->codnom, 'codperi' => $recibo->codperi])); ?>"
                                  class="btn btn-danger">
                                     <i class="fas fa-file-pdf"></i> Descargar
                                        </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No se encontraron recibos de pago registrados.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white py-3">
            <?php echo e($recibos->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/Laravel/Sigh2025/resources/views/empleado/reportes/recibos.blade.php ENDPATH**/ ?>