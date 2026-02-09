<?php $__env->startSection('content'); ?>
<div class="container">
    <h2>Tablero de Administración</h2>
    <hr>

    <div class="row">
        
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-danger">
                <div class="card-header">Usuarios Bloqueados</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo e($lockedUsersCount); ?> Cuentas</h5>
                    <p class="card-text">Pendientes de desbloqueo manual.</p>
                    <a href="<?php echo e(route('admin.users.locked')); ?>" class="btn btn-sm btn-light">Ir a Desbloquear</a>
                </div>
            </div>
        </div>

        
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-primary">
                <div class="card-header">Historial de Conexiones</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo e($totalUsers); ?> Usuarios Totales</h5>
                    <p class="card-text">Ver detalle de IPs y tiempos de conexión.</p>
                    <a href="<?php echo e(route('admin.users.connections')); ?>" class="btn btn-sm btn-light">Ver Historial</a>
                </div>
            </div>
        </div>

        
        <div class="col-md-4 mb-4">
          <div class="card text-white bg-success">
               <div class="card-header">Usuarios en Línea</div>
                  <div class="card-body">
                      <h5 class="card-title"><?php echo e($activeUsersCount); ?> Activos</h5>
                      <p class="card-text">Usuarios navegando en el sitio ahora.</p>
                      <a href="<?php echo e(route('admin.users.active')); ?>" class="btn btn-sm btn-light">Ver quiénes son</a>
                 </div>
             </div>
        </div>

    
    <div class="card mt-4">
        <div class="card-header">Últimas 5 Conexiones</div>
        <div class="card-body">
            <ul class="list-group">
                <?php $__currentLoopData = $recentConnections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $connection): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="list-group-item">
                        **<?php echo e($connection->user->name ?? 'Usuario Eliminado'); ?>** (<?php echo e($connection->user->email ?? 'N/A'); ?>) se conectó desde IP **<?php echo e($connection->ip_address); ?>** el <?php echo e($connection->created_at->format('d/m/Y H:i:s')); ?>.
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/Sigh2025/resources/views/admin/index.blade.php ENDPATH**/ ?>