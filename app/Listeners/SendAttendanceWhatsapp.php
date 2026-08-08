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

        if (!$student || !$student->phone_number) {
            Log::warning('Data student atau nomor HP tidak ditemukan untuk absen ID: ' . $attendance->id);
            return;
        }

        // Format Nomor WhatsApp (Indonesian Standard)
        $phone  = '62' . ltrim($student->phone_number, '0');
        $chatId = $phone . '@c.us';

        // Buat Link Google Maps dari Koordinat di Controller
        $googleMapsLink = "https://www.google.com/maps?q={$attendance->location_lat},{$attendance->location_lng}";

        // 3. Pesan untuk Orang Tua
        $message = <<<MSG
*NOTIFIKASI KEHADIRAN SISWA* 🏫

Yth. Bapak/Ibu Wali Murid dari:
*{$student->name}*

Menginformasikan bahwa putra/putri Bapak/Ibu telah melakukan absensi masuk sekolah pada:

📅 *Tanggal* : {$attendance->date}
⏰ *Jam* : {$attendance->time_in} WIB
📌 *Status* : {$attendance->status->name}
📍 *Lokasi* : $googleMapsLink

Terima kasih atas kerja sama Bapak/Ibu dalam menjaga kedisiplinan putra/putri kita.

_Pesan ini dikirim otomatis oleh Sistem Absensi Sekolah._
MSG;

        // 4. KIRIM PESAN KE WAHA
        try {
            $response = Http::withHeaders([
                'X-API-KEY' => config('services.waha.api_key'),
            ])->post(
                config('services.waha.url') . '/api/sendText',
                [
                    'session' => 'default',
                    'chatId'  => $chatId,
                    'text'    => $message,
                ]
            );

            Log::info('WAHA status:', ['status' => $response->status()]);
        } catch (\Exception $e) {
            Log::error('Gagal mengirim WA: ' . $e->getMessage());
        }
    }
}