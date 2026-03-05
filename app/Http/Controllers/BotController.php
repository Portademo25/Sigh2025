<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class BotController extends Controller
{
    public function handle(Request $request)
    {
        $update = Telegram::getWebhookUpdate();
        $chatId = $update->getMessage()->getChat()->getId();
        $texto = $update->getMessage()->getText();

        // 1. Buscamos si el usuario ya vinculó su Telegram
        $user = User::where('telegram_id', $chatId)->first();

        if (!$user) {
            return $this->vincularUsuario($chatId, $texto);
        }

        // 2. IA: Analizamos qué quiere el usuario (Gemini API)
        $intencion = $this->analizarIntencionConIA($texto);

        if ($intencion == 'DESCARGAR_RECIBO') {
            return $this->enviarRecibo($chatId, $user);
        }

        // 3. Respuesta genérica de la IA para otras dudas
        $respuestaIA = $this->consultarIA($texto, $user);
        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => $respuestaIA
        ]);
    }

    private function enviarRecibo($chatId, $user)
    {
        // Aquí llamamos a la lógica que ya tienes en descargarRecibo()
        // pero en lugar de hacer 'stream', enviamos el archivo a Telegram
        Telegram::sendMessage(['chat_id' => $chatId, 'text' => "Generando tu último recibo..."]);

        // Simulación de envío de documento
        // Telegram::sendDocument([...]);
    }
}
