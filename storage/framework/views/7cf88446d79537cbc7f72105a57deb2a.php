<?php $__env->startSection('content'); ?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between bg-white p-4 rounded shadow-sm border-start border-4 border-primary">
                <div>
                    <h1 class="h3 mb-1 fw-bold text-dark">Panel de Control: Bienestar Social</h1>
                    <p class="text-muted mb-0">
                        <i class="fas fa-desktop me-2"></i>Bienvenido <?php echo e(Auth::user()->name); ?> al Sistema de Gestión de Recursos Humanos
                    </p>
                </div>
                <div class="text-end d-none d-md-block">
                    <span class="badge bg-light text-primary border border-primary px-3 py-2 rounded-pill">
                        <?php echo e(now()->format('d/m/Y')); ?>

                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-uppercase fw-bold text-primary mb-1" style="letter-spacing: 1px; font-size: 0.8rem;">Personal Activo</p>
                           <h2 class="fw-bold mb-0"><?php echo e(number_format($stats->activos, 0, ',', '.')); ?></h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                            <i class="fas fa-user-check fa-2x text-primary"></i>
                        </div>
                    </div>
                    <div class="mt-3 text-muted small">Datos sincronizados con SIGESP</div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-uppercase fw-bold text-success mb-1" style="letter-spacing: 1px; font-size: 0.8rem;">Egresados</p>
                            <h2 class="fw-bold mb-0"><?php echo e(number_format($stats->jubilados, 0, ',', '.')); ?></h2>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-3">
                            <i class="fas fa-id-card-alt fa-2x text-success"></i>
                        </div>
                    </div>
                    <div class="mt-3 text-muted small">Estatus 3 en nómina</div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-uppercase fw-bold text-info mb-1" style="letter-spacing: 1px; font-size: 0.8rem;">Total Fichas</p>
                            <h2 class="fw-bold mb-0"><?php echo e(number_format($stats->total, 0, ',', '.')); ?></h2>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded-3">
                            <i class="fas fa-users fa-2x text-info"></i>
                        </div>
                    </div>
                    <div class="mt-3 text-muted small">Histórico general del ente</div>
                </div>
            </div>
        </div>
    </div>


<div class="row g-4 row-cols-1 row-cols-lg-3 align-items-stretch">

    <div class="col">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4">
                <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-bolt text-warning me-2"></i>Acciones Rápidas</h5>
            </div>
            <div class="card-body d-flex flex-column gap-3 px-4 pb-4">
                <a href="<?php echo e(route('rrhh.personal.index')); ?>" class="btn btn-primary py-3 rounded-3 fw-semibold shadow-sm">
                    <i class="fas fa-search me-2"></i> Buscar para ARC
                </a>
                <a href="<?php echo e(route('rrhh.personal.pagos.index')); ?>" class="btn btn-outline-dark py-3 rounded-3 fw-semibold">
                    <i class="fas fa-file-invoice me-2"></i> Gestionar Recibos
                </a>
                <a href="<?php echo e(route('rrhh.personal.constancias.index')); ?>" class="btn btn-success py-3 rounded-3 text-white fw-semibold shadow-sm">
                    <i class="fas fa-file-signature me-2"></i> Generar Constancia
                </a>
                <a href="<?php echo e(route('rrhh.constancias.validar')); ?>" class="btn btn-outline-primary py-3 rounded-3 fw-semibold">
                    <i class="fas fa-clipboard-check me-2"></i> Validar Emitidas
                </a>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4">
                <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-balance-scale text-primary me-2"></i>Beneficios de Ley</h5>
            </div>
            <div class="card-body d-flex flex-column justify-content-between px-4 pb-4 text-center">
                <div class="py-2">
                    <div class="bg-warning-subtle rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class="fas fa-utensils text-warning fs-3"></i>
                    </div>
                    <h6 class="fw-bold mb-1">Cestaticket</h6>
                    <p class="text-muted small">Monto actual: <strong>Bs. 13.000,00</strong></p>
                    <span class="badge bg-light text-dark border fw-normal text-wrap">Última actualización: <?php echo e(date('d/m/Y')); ?></span>
                </div>
                <button class="btn btn-warning w-100 py-3 rounded-3 fw-bold mt-3 shadow-sm">
                    <i class="fas fa-edit me-1"></i> Actualizar Monto
                </button>
            </div>
        </div>
    </div>

  <div class="col">
    <div class="card border-0 shadow-sm rounded-4 h-100">
        <div class="card-header bg-white border-0 pt-4">
            <h5 class="fw-bold mb-0 text-dark">Últimos Ingresos</h5>
        </div>
        <div class="card-body px-0 d-flex flex-column">
            <?php if(isset($ultimosIngresos) && count($ultimosIngresos) > 0): ?>
                <div class="list-group list-group-flush">
                    <?php $__currentLoopData = $ultimosIngresos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ingreso): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="list-group-item border-0 px-4 py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div style="max-width: 70%;">
                                     <p class="mb-0 fw-bold small text-uppercase text-truncate">
                                        <?php echo e($ingreso->nomper); ?> <?php echo e($ingreso->apeper); ?>

                                     </p>
                                    <small class="text-muted">C.I. <?php echo e(number_format($ingreso->cedper, 0, '', '.')); ?></small>
                                </div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                        <?php echo e(\Carbon\Carbon::parse($ingreso->fecingper)->format('d/m/y')); ?>

                    </span>
                 </div>
            </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="d-flex flex-column align-items-center justify-content-center h-100 py-5">
                    <i class="fas fa-users-slash text-muted mb-2 fs-2"></i>
                    <p class="text-muted small">No se encontraron registros recientes.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    /* Efecto para que todos los botones de acción se vean iguales */
    .transition-btn {
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .transition-btn:hover {
        transform: scale(1.02);
    }
    /* Mantiene la altura perfecta en pantallas grandes */
    @media (min-width: 992px) {
        .h-100 { height: 100% !important; }
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/Laravel/Sigh2025/resources/views/rrhh/dashboard.blade.php ENDPATH**/ ?>