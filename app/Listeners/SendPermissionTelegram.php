<?php

namespace App\Listeners;

use App\Events\PermissionCreated;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SendPermissionTelegram
{
    public function handle(PermissionCreated $event): void
    {
        $permission = $event->permission->loadMissing('student');
        $student = $permission->student;

        $botToken = config('services.telegram.bot_token');
        $chatId = $student?->telegram_chat_id ?: config('services.telegram.chat_id');

        if (!$botToken) {
            Log::warning('[Telegram] Bot token belum diatur.', [
                'permission_id' => $permission->id,
            ]);
            return;
        }

        if (!$student) {
            Log::warning('[Telegram] Data student izin tidak ditemukan.', [
                'permission_id' => $permission->id,
            ]);
            return;
        }

        if (!$chatId) {
            Log::warning('[Telegram] User belum menghubungkan akun Telegram untuk izin.', [
                'permission_id' => $permission->id,
                'user_id' => $student->id,
            ]);
            return;
        }

        $mapsLink = $permission->location_lat && $permission->location_lng
            ? "https://www.google.com/maps?q={$permission->location_lat},{$permission->location_lng}"
            : '-';

        $message = implode("\n", [
            'NOTIFIKASI PENGAJUAN IZIN SISWA',
            '',
            "Nama: {$student->name}",
            'Tipe: ' . ucfirst($permission->type),
            "Status: {$permission->status}",
            "Nama Orang Tua/Wali: {$permission->parent_name}",
            "Deskripsi: {$permission->description}",
            'Tanggal Pengajuan: ' . $permission->created_at?->format('Y-m-d H:i:s'),
            "Lokasi: {$mapsLink}",
            '',
            'Jika terdapat kesalahan data, harap segera menghubungi pihak Admin',
            '',
            'Pesan ini dikirim oleh sistem SIFACO.',
        ]);

        try {
            $photoDiskPath = $this->publicDiskPath($permission->photo);
            $photoFullPath = $photoDiskPath ? Storage::disk('public')->path($photoDiskPath) : null;

            if ($photoDiskPath && $photoFullPath && is_file($photoFullPath)) {
                $response = Http::timeout(20)
                    ->attach('photo', fopen($photoFullPath, 'r'), basename($photoFullPath))
                    ->post(
                        "https://api.telegram.org/bot{$botToken}/sendPhoto",
                        [
                            'chat_id' => $chatId,
                            'caption' => $message,
                        ]
                    );
            } else {
                $response = Http::timeout(10)->post(
                    "https://api.telegram.org/bot{$botToken}/sendMessage",
                    [
                        'chat_id' => $chatId,
                        'text' => $message,
                        'disable_web_page_preview' => true,
                    ]
                );
            }

            if ($response->failed()) {
                Log::error('[Telegram] Gagal mengirim notifikasi izin.', [
                    'permission_id' => $permission->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return;
            }

            Log::info('[Telegram] Notifikasi izin terkirim.', [
                'permission_id' => $permission->id,
                'status' => $response->status(),
            ]);
        } catch (\Throwable $e) {
            Log::error('[Telegram] Error saat mengirim notifikasi izin: ' . $e->getMessage(), [
                'permission_id' => $permission->id,
            ]);
        }
    }

    private function publicDiskPath(?string $photo): ?string
    {
        if (!$photo) {
            return null;
        }

        return str_starts_with($photo, 'storage/')
            ? substr($photo, strlen('storage/'))
            : $photo;
    }
}
