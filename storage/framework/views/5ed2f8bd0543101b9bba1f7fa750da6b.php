<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($tipoDocumento); ?> - SIGH</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f4f4f4; padding-bottom: 40px; }
        .main-table { width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; border-spacing: 0; color: #333333; border-radius: 8px; overflow: hidden; margin-top: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { background-color: #ffffff; padding: 30px; text-align: center; border-bottom: 3px solid #0056b3; }
        .content { padding: 40px 30px; line-height: 1.6; }
        .document-box { background-color: #f8f9fa; border-left: 4px solid #0056b3; padding: 15px; margin: 20px 0; border-radius: 4px; }
        .footer { padding: 20px; text-align: center; font-size: 12px; color: #777777; background-color: #f9f9f9; }
        .text-uppercase { text-transform: uppercase; }
    </style>
</head>
<body>
    <div class="wrapper">
        <table class="main-table">
            <tr>
                <td class="header">
                    
                    <img src="<?php echo e($message->embed(public_path('images/logo_fona.png'))); ?>" alt="Logo FONA" style="height: 80px; width: auto;">
                    <h2 style="color: #0056b3; margin-top: 10px; margin-bottom: 0;">SIGH</h2>
                    <p style="margin: 0; font-size: 13px; color: #666; font-weight: bold;">Dirección de Gestión Humana</p>
                </td>
            </tr>
            <tr>
                <td class="content">
                    <p>Estimado(a) <strong class="text-uppercase"><?php echo e($nombre); ?></strong>,</p>
                    <p>Le informamos que se ha generado un nuevo documento digital desde su ficha de trabajador:</p>

                    <div class="document-box">
                        <p style="margin: 0;"><strong>Documento:</strong> <?php echo e($tipoDocumento); ?></p>
                        <p style="margin: 0;"><strong>Fecha de Emisión:</strong> <?php echo e(date('d/m/Y h:i A')); ?></p>
                    </div>

                    <p>Adjunto a este correo encontrará el archivo PDF correspondiente. Le recomendamos descargar y guardar este documento para sus archivos personales.</p>

                    <p style="font-size: 13px; color: #666;"><em>Este es un envío automático, por favor no responda a este mensaje.</em></p>
                </td>
            </tr>
            <tr>
                <td class="footer">
                    Atentamente,<br>
                    <strong>Oficina de Gestión Humana - FONA</strong><br>
                    <hr style="border: 0; border-top: 1px solid #ddd; margin: 10px 0;">
                    <small>Sistema Integrado de Gestión Humana (SIGH) &copy; <?php echo e(date('Y')); ?></small>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
<?php /**PATH /var/www/html/Laravel/Sigh2025/resources/views/emails/documento_rrhh.blade.php ENDPATH**/ ?>