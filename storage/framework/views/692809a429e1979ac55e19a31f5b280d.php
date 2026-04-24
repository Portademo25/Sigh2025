<?php if($cumpleaneros->count() > 0): ?>
    <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(90deg, #4e54c8 0%, #8f94fb 100%); border-radius: 12px; color: white;">
        <div class="card-body py-2 px-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <span style="font-size: 2rem;" class="me-3">🎂</span>
                <div>
                    <h6 class="mb-0 fw-bold">Cumpleañeros de <?php echo e(now()->translatedFormat('F')); ?></h6>
                    <small>Hay <?php echo e($cumpleaneros->count()); ?> compañeros de festejo.</small>
                </div>
            </div>
            <button class="btn btn-light btn-sm rounded-pill px-3 fw-bold text-primary" 
                    data-bs-toggle="modal" data-bs-target="#modalCumple">
                Ver Lista <i class="fas fa-chevron-right ms-1"></i>
            </button>
        </div>
    </div>

    <div class="modal fade" id="modalCumple" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; color: #333;">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold"><i class="fas fa-cake-candles me-2"></i> Celebraciones</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0" style="max-height: 400px; overflow-y: auto;">
                    <ul class="list-group list-group-flush">
                        <?php $__currentLoopData = $cumpleaneros; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cumple): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light text-primary d-flex align-items-center justify-content-center fw-bold rounded-circle border" style="width: 40px; height: 40px;">
                                        <?php echo e(substr($cumple->nomper, 0, 1)); ?><?php echo e(substr($cumple->apeper, 0, 1)); ?>

                                    </div>
                                    <div class="ms-3">
                                        <span class="d-block fw-bold text-uppercase small"><?php echo e($cumple->nomper); ?> <?php echo e($cumple->apeper); ?></span>
                                        <small class="text-muted small">Día: <?php echo e($cumple->dia); ?></small>
                                    </div>
                                </div>
                                <?php if(intval($cumple->dia) == intval(now()->day)): ?>
    <span class="badge bg-warning text-dark bounce-animation shadow-sm">¡HOY!</span>
<?php endif; ?>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?><?php /**PATH /var/www/html/Laravel/Sigh2025/resources/views/rrhh/widgets/notificacion_cumpleanos.blade.php ENDPATH**/ ?>