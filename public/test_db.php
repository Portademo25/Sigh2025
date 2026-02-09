<?php
echo "Usuario detectado por Apache: " . getenv('DB_USERNAME');
echo "<br>Usuario en la config de Laravel: " . shell_exec('php ../artisan config:get database.connections.pgsql.username');
?>

