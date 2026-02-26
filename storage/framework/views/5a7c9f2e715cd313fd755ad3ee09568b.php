<?php $__env->startSection('content'); ?>
<?php if(session('error')): ?>
    <div class="alert alert-danger shadow-sm border-0 mb-4">
        <i class="fas fa-exclamation-circle me-2"></i> <?php echo e(session('error')); ?>

    </div>
<?php endif; ?>
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('rrhh.personal.pagos.index')); ?>">Personal</a></li>
            <li class="breadcrumb-item active">Gestión de Pagos</li>
        </ol>
    </nav>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body bg-primary text-white rounded">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-user-circle fa-4x"></i>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h4 class="mb-0 text-uppercase"><?php echo e($personal->nomper); ?> <?php echo e($personal->apeper); ?></h4>
                    <p class="mb-0 opacity-75"><?php echo e($personal->descar); ?> | C.I: <?php echo e($personal->cedper); ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="fas fa-file-invoice-dollar me-2 text-success"></i> Generar Recibo de Pago
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('rrhh.recibo.descargar')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="cedper" value="<?php echo e($personal->cedper); ?>">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Año</label>
                            <select name="ano" class="form-select border-primary">
                                <?php for($i=date('Y'); $i>=2023; $i--): ?>
                                    <option value="<?php echo e($i); ?>"><?php echo e($i); ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Mes</label>
                            <select name="mes" class="form-select border-primary">
                                <?php $__currentLoopData = $meses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num => $nombre): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($num); ?>" <?php echo e(date('n') == $num ? 'selected' : ''); ?>><?php echo e($nombre); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold d-block">Quincena / Periodo</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="periodo" id="q1" value="1" checked>
                                <label class="btn btn-outline-primary" for="q1">1ra Quincena</label>

                                <input type="radio" class="btn-check" name="periodo" id="q2" value="2">
                                <label class="btn btn-outline-primary" for="q2">2da Quincena</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-2 fw-bold shadow-sm">
                            <i class="fas fa-download me-2"></i> DESCARGAR RECIBO
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="fas fa-history me-2 text-info"></i> Otros Reportes
                </div>
                <div class="card-body">
                    <p class="text-muted small">Generar ARC acumulado para este trabajador:</p>
                    <div class="input-group">
                        <select id="ano_arc" class="form-select">
                            <option value="2026">Año 2026</option>
                        </select>
                        <button onclick="descargarARC()" class="btn btn-info text-white">Descargar ARC</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function descargarARC() {
        const ano = document.getElementById('ano_arc').value;
        const url = "<?php echo e(route('rrhh.arc.generar', [$personal->cedper, ':ano'])); ?>".replace(':ano', ano);
        window.location.href = url;
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/Laravel/Sigh2025/resources/views/rrhh/personal/pagos.blade.php ENDPATH**/ ?>