<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card shadow border-0">
       <div class="card-header bg-dark text-white py-3">
    <div class="d-flex align-items-center justify-content-between">
        <h5 class="mb-0">
            <i class="bi bi-file-earmark-bar-graph me-2"></i> Control de Planillas ARC
        </h5>

        <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn btn-sm btn-outline-light">
            <i class="bi bi-arrow-left-circle me-1"></i> Regresar al Menú
        </a>
    </div>
</div>
        <div class="card-body">
            <form action="<?php echo e(route('admin.reportes.arc')); ?>" method="GET" class="row g-3 mb-4">
                <div class="col-md-8">
                    <input type="text" name="buscar" class="form-control" placeholder="Buscar por Cédula o Nombre..." value="<?php echo e(request('buscar')); ?>">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Buscar Empleado
                    </button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Cédula</th>
                            <th>Nombre Completo</th>
                            <th>Año Fiscal</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $personal; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($p->cedper); ?></td>
                            <td><?php echo e($p->nomper); ?> <?php echo e($p->apeper); ?></td>
                            <td>
                                <select id="ano_<?php echo e($p->cedper); ?>" class="form-select form-select-sm d-inline-block w-auto">
                                    <?php $__currentLoopData = $anios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($a); ?>"><?php echo e($a); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </td>
                            <td class="text-center">
                                <button onclick="descargarArcAdmin('<?php echo e($p->cedper); ?>')" class="btn btn-sm btn-success">
                                    <i class="bi bi-file-earmark-pdf"></i> Generar ARC
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function descargarArcAdmin(cedula) {
    const ano = document.getElementById('ano_' + cedula).value;
    window.open(`/admin/reportes/arc/generar/${cedula}/${ano}`, '_blank');
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/Sigh2025/resources/views/admin/reportes/index_arc.blade.php ENDPATH**/ ?>