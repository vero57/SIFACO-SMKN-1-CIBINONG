<?php

namespace App\Listeners;

use App\Events\AttendanceCreated;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendAttendanceWhatsapp
{
    public function handle(AttendanceCreated $event): void
    {
        $attendance = $event->attendance;
        $student = $attendance->student;

        Log::info('Attendance Data:', $attendance->toArray());
        Log::info('Student Data:', $student ? $student->toArray() : ['student' => null]);

        if (!$student || !$student->phone_number) {
            Log::warning('No student or phone number found for attendance ID: ' . $attendance->id);
            return;
        }

        // Format nomor WA
        $phone  = '62' . ltrim($student->phone_number, '0');
        $chatId = $phone . '@c.us';

        // Pesan
        $message = <<<MSG
Halo {$student->name} 👋
Absensi kamu berhasil tercatat.

📅 Tanggal: {$attendance->date}
⏰ Jam    : {$attendance->time_in}
📌 Status : {$attendance->status->name}

Tetap semangat belajar 💪
MSG;

        // ===============================
        // 1️⃣ KIRIM PESAN TEKS
        // ===============================
        $responseText = Http::withHeaders([
            'X-API-KEY' => config('services.waha.api_key'),
        ])->post(
            config('services.waha.url') . '/api/sendText',
            [
                'session' => 'default',
                'chatId'  => $chatId,
                'text'    => $message,
            ]
        );

        Log::info('WAHA response (text):', [
            'status' => $responseText->status(),
            'body'   => $responseText->body(),
        ]);

        // ===============================
        // 2️⃣ KIRIM FOTO + CAPTION
        // ===============================
        // if ($attendance->photo) {
        //     $photoPath = public_path($attendance->photo);

        //     if (!file_exists($photoPath)) {
        //         Log::warning('Photo file not found: ' . $photoPath);
        //         return;
        //     }

        //     $responseImage = Http::withHeaders([
        //         'X-API-KEY' => config('services.waha.api_key'),
        //     ])
        //     ->asMultipart()
        //     ->attach(
        //         'image',
        //         file_get_contents($photoPath),
        //         basename($photoPath)
        //     )
        //     ->post(
        //         config('services.waha.url') . '/api/sendImage',
        //         [
        //             'session' => 'default',
        //             'chatId'  => $chatId,
        //             'caption' => 'Foto absensi ' . $student->name,
        //         ]
        //     );

        //     Log::info('WAHA response (image):', [
        //         'status' => $responseImage->status(),
        //         'body'   => $responseImage->body(),
        //     ]);
        // }
    }
}
