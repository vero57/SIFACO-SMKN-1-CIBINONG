<?php

namespace App\Listeners;

use App\Events\AttendanceCreated;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SendAttendanceTelegram
{
    public function handle(AttendanceCreated $event): void
    {
        $attendance = $event->attendance->loadMissing(['student', 'status']);
        $student = $attendance->student;

        $botToken = config('services.telegram.bot_token');
        $chatId = $student?->telegram_chat_id ?: config('services.telegram.chat_id');

        if (!$botToken) {
            Log::warning('[Telegram] Bot token belum diatur.', [
                'attendance_id' => $attendance->id,
            ]);
            return;
        }

        if (!$student) {
            Log::warning('[Telegram] Data student tidak ditemukan.', [
                'attendance_id' => $attendance->id,
            ]);
            return;
        }

        if (!$chatId) {
            Log::warning('[Telegram] User belum menghubungkan akun Telegram.', [
                'attendance_id' => $attendance->id,
                'user_id' => $student->id,
            ]);
            return;
        }

        $mapsLink = $attendance->location_lat && $attendance->location_lng
            ? "https://www.google.com/maps?q={$attendance->location_lat},{$attendance->location_lng}"
            : '-';

        $message = implode("\n", [
            'NOTIFIKASI KEHADIRAN SISWA',
            '',
            "Nama: {$student->name}",
            "Tanggal: {$attendance->date}",
            "Jam Masuk: {$attendance->time_in} WIB",
            'Status: ' . ($attendance->status?->name ?? '-'),
            "Lokasi: {$mapsLink}",
            '',
            'Jika terdapat kesalahan data, harap segera menghubungi pihak Admin',
            '',
            'Pesan ini dikirim otomatis oleh sistem SIFACO.',
        ]);

        try {
            $photoDiskPath = $this->publicDiskPath($attendance->photo);
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
                Log::error('[Telegram] Gagal mengirim notifikasi absen.', [
                    'attendance_id' => $attendance->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return;
            }

            Log::info('[Telegram] Notifikasi absen terkirim.', [
                'attendance_id' => $attendance->id,
                'status' => $response->status(),
            ]);
        } catch (\Throwable $e) {
            Log::error('[Telegram] Error saat mengirim notifikasi absen: ' . $e->getMessage(), [
                'attendance_id' => $attendance->id,
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
