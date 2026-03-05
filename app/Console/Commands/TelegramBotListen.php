<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class TelegramBotListen extends Command
{
    // Asegúrate de que aquí no haya caracteres extraños
    protected $signature = 'bot:run';
    protected $description = 'Inicia el asistente virtual de SIGH';

    public function handle()
    {
        $this->info("🤖 Asistente SIGH iniciado...");

        while (true) {
            try {
                $updates = Telegram::getUpdates([
                    'offset' => Cache::get('telegram_last_update_id', 0) + 1,
                    'timeout' => 20
                ]);

                foreach ($updates as $update) {
                    $message = $update->getMessage();
                    if (!$message) continue;

                    $chatId = $message->getChat()->getId();
                    $texto = $message->getText();
                    $nombre = $message->getChat()->getFirstName();

                    $this->info("Mensaje de {$nombre}: {$texto}");
                    $this->procesarMensaje($chatId, $texto, $nombre);

                    Cache::put('telegram_last_update_id', $update->getUpdateId());
                }
            } catch (\Exception $e) {
                $this->error("Error en el bucle: " . $e->getMessage());
            }
            sleep(1);
        }
    }

    private function procesarMensaje($chatId, $texto, $nombre)
    {
        $user = User::where('telegram_id', $chatId)->first();

        if (!$user) {
            return $this->gestionarVinculacion($chatId, $texto);
        }

        // Respuesta rápida sin IA para probar si el bot "vive"
        if (strtolower($texto) == 'recibo') {
            return Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => "Hola {$user->name}, estoy preparando tu PDF..."
            ]);
        }

        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => "¡Hola! Escribe 'recibo' para obtener tu pago."
        ]);
    }

    private function gestionarVinculacion($chatId, $texto)
    {
        if (is_numeric($texto) && strlen($texto) >= 7) {
            $user = User::where('cedula', $texto)->first();
            if ($user) {
                $user->update(['telegram_id' => $chatId]);
                return Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => "✅ Vinculado como: {$user->name}"
                ]);
            }
            return Telegram::sendMessage(['chat_id' => $chatId, 'text' => "❌ Cédula no encontrada."]);
        }
        return Telegram::sendMessage(['chat_id' => $chatId, 'text' => "Por favor, envía tu CÉDULA."]);
    }
}
