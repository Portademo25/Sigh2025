<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecimiento de Contraseña - SIGH</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f4f4f4; padding-bottom: 40px; }
        .main-table { width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; border-spacing: 0; color: #333333; border-radius: 8px; overflow: hidden; margin-top: 20px; }
        .header { background-color: #ffffff; padding: 30px; text-align: center; border-bottom: 3px solid #0056b3; }
        .content { padding: 30px; line-height: 1.6; }
        .button-container { text-align: center; padding: 20px 0; }
        .button { background-color: #0056b3; color: #ffffff !important; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block; }
        .footer { padding: 20px; text-align: center; font-size: 12px; color: #777777; background-color: #f9f9f9; }
    </style>
</head>
<body>
    <div class="wrapper">
        <table class="main-table">
            <tr>
                <td class="header">
                    {{-- El método que nos funcionó --}}
                    <img src="{{ $message->embed(public_path('images/logo_fona.png')) }}" alt="Logo FONA" style="height: 90px; width: auto;">
                    <h2 style="color: #0056b3; margin-top: 10px;">SIGH</h2>
                    <p style="margin: 0; font-size: 14px; color: #666;">Sistema de Gestión de la Dirección de Gestión Humana</p>
                </td>
            </tr>
            <tr>
                <td class="content">
                    <p>Estimado(a) <strong>{{ $nombre }}</strong>,</p>
                    <p>Se ha solicitado un restablecimiento de contraseña para su cuenta en nuestra plataforma institucional.</p>
                    <div class="button-container">
                        <a href="{{ $url }}" class="button">Restablecer Contraseña</a>
                    </div>
                    <p>Este enlace es válido por <strong>60 minutos</strong>. Si usted no solicitó este cambio, puede ignorar este mensaje de forma segura.</p>
                </td>
            </tr>
            <tr>
                <td class="footer">
                    Atentamente,<br>
                    <strong>Oficina de Tecnologías de la Información - FONA</strong><br>
                    <hr style="border: 0; border-top: 1px solid #ddd; margin: 10px 0;">
                    <small>Si tiene problemas con el botón, copie y vea esta URL: <br> {{ $url }}</small>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
