<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">📜 Historial de Conexiones</h2>
                    <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-secondary btn-sm">
                        ← Tablero de Administración
                    </a>
                </div>

                <div class="card-body">
                    <form action="<?php echo e(route('admin.users.connections')); ?>" method="GET" class="mb-4">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control"
                                   placeholder="Buscar por nombre, email o IP..."
                                   value="<?php echo e($search); ?>">
                            <button class="btn btn-primary" type="submit">Buscar</button>
                            <?php if($search): ?>
                                <a href="<?php echo e(route('admin.users.connections')); ?>" class="btn btn-outline-secondary">Limpiar</a>
                            <?php endif; ?>
                        </div>
                    </form>

                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>IP de Conexión</th>
                                <th>Fecha y Hora</th>
                            </tr>
                        </thead>
                        <tbody>
                          <?php $__currentLoopData = $connections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $connection): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
    <td><?php echo e($connection->user->name ?? 'Usuario Eliminado'); ?></td>
    <td><?php echo e($connection->user->email ?? 'N/A'); ?></td>
    <td><code><?php echo e($connection->ipconexion); ?></code></td>
    <td>
        
        <?php echo e(\Carbon\Carbon::parse($connection->fechaconexion)->format('d/m/Y')); ?>

        <span class="text-muted">|</span>
        <?php echo e($connection->horaconexion); ?>

    </td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-center mt-3">
                        <?php echo e($connections->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/Laravel/Sigh2025/resources/views/admin/connection_history.blade.php ENDPATH**/ ?>