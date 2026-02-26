<?php $__env->startSection('content'); ?>
<div class="container mt-4">
    
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="text-secondary font-weight-bold mb-0">
                    <i class="fas fa-cogs mr-2"></i>Tablero de Administración
                </h2>
                <p class="text-muted mb-0">Gestión de seguridad, estados de cuenta y monitoreo de red.</p>
            </div>
            <a href="<?php echo e(route('home')); ?>" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-chevron-left mr-1"></i> Volver al Menú
            </a>
        </div>
        <div class="col-12">
            <hr>
        </div>
    </div>

    <div class="row">
        
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-danger shadow-sm h-100">
                <div class="card-header font-weight-bold">Usuarios Bloqueados</div>
                <div class="card-body">
                    <h5 class="card-title display-4" style="font-size: 1.8rem;"><?php echo e($lockedUsersCount); ?> Cuentas</h5>
                    <p class="card-text">Usuarios que excedieron intentos de login.</p>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="<?php echo e(route('admin.users.locked')); ?>" class="btn btn-sm btn-light btn-block">Ir a Desbloquear</a>
                </div>
            </div>
        </div>

        
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-primary shadow-sm h-100">
                <div class="card-header font-weight-bold">Historial de Conexiones</div>
                <div class="card-body">
                    <h5 class="card-title display-4" style="font-size: 1.8rem;"><?php echo e($totalUsers); ?> Totales</h5>
                    <p class="card-text">Auditoría detallada de IPs y accesos.</p>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="<?php echo e(route('admin.users.connections')); ?>" class="btn btn-sm btn-light btn-block">Ver Historial</a>
                </div>
            </div>
        </div>

        
        <div class="col-md-4 mb-4">
          <div class="card text-white bg-success shadow-sm h-100">
               <div class="card-header font-weight-bold">Usuarios en Línea</div>
                  <div class="card-body">
                      <h5 class="card-title display-4" style="font-size: 1.8rem;"><?php echo e($activeUsersCount); ?> Activos</h5>
                      <p class="card-text">Sesiones concurrentes detectadas ahora.</p>
                 </div>
                 <div class="card-footer bg-transparent border-0">
                      <a href="<?php echo e(route('admin.users.active')); ?>" class="btn btn-sm btn-light btn-block">Ver quiénes son</a>
                 </div>
             </div>
        </div>
    </div>

    
    <div class="card shadow-sm mt-2">
        <div class="card-header bg-white font-weight-bold">
            <i class="fas fa-history mr-2 text-primary"></i>Últimas 5 Conexiones
        </div>
        <div class="card-body p-0">
            <ul class="list-group list-group-flush">
                <?php $__empty_1 = true; $__currentLoopData = $recentConnections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $connection): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fas fa-user-circle mr-2 text-muted"></i>
                            <strong><?php echo e($connection->user->name ?? 'Usuario Eliminado'); ?></strong>
                            <small class="text-muted">(<?php echo e($connection->user->email ?? 'N/A'); ?>)</small>
                        </span>
                        <span class="badge badge-light border p-2" style="color: black;">
                            IP: <?php echo e($connection->ip_address); ?> | <?php echo e($connection->created_at->diffForHumans()); ?>

                        </span>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <li class="list-group-item text-center text-muted">No hay registros recientes.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/Laravel/Sigh2025/resources/views/admin/index.blade.php ENDPATH**/ ?>