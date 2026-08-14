<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $message = $request->input('message') ?? $request->input('edited_message');
        $text = trim((string) data_get($message, 'text', ''));
        $chatId = data_get($message, 'chat.id');

        if (!$message || !$chatId || !str_starts_with($text, '/start')) {
            return response()->json(['ok' => true]);
        }

        $token = trim(str_replace('/start', '', $text));

        if ($token === '') {
            return response()->json([
                'ok' => true,
                'message' => 'Open the Telegram connection link from your profile page.',
            ]);
        }

        $user = User::where('telegram_link_token', $token)->first();

        if (!$user) {
            Log::warning('[Telegram] Token linking tidak valid.', [
                'chat_id' => $chatId,
                'token' => $token,
            ]);

            return response()->json(['ok' => true]);
        }

        $user->forceFill([
            'telegram_chat_id' => (string) $chatId,
            'telegram_linked_at' => now(),
        ])->save();

        Log::info('[Telegram] Akun user berhasil terhubung.', [
            'user_id' => $user->id,
            'chat_id' => $chatId,
        ]);

        return response()->json(['ok' => true]);
    }
}
