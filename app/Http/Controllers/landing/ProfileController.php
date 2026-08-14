<?php

namespace App\Http\Controllers\landing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user->telegram_link_token) {
            $user->forceFill([
                'telegram_link_token' => Str::random(48),
            ])->save();
        }

        $telegramBotUsername = config('services.telegram.bot_username');
        $telegramConnectUrl = $telegramBotUsername
            ? 'https://t.me/' . ltrim($telegramBotUsername, '@') . '?start=' . $user->telegram_link_token
            : null;

        return view('landing.profile.index', compact('user', 'telegramConnectUrl'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($request->only('name', 'phone_number', 'email'));

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui.',
            'user' => $user
        ]);
    }
}
