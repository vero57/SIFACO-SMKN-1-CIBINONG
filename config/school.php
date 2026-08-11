<?php

/**
 * Konfigurasi data sekolah.
 *
 * Nilai-nilai ini dapat diubah melalui file .env tanpa perlu deploy ulang
 * atau mengubah kode JavaScript. Radius dan koordinat dipakai untuk validasi
 * lokasi absen siswa (server-side maupun client-side).
 */
return [

    /*
    |--------------------------------------------------------------------------
    | Nama Sekolah
    |--------------------------------------------------------------------------
    */
    'name' => env('SCHOOL_NAME', 'SMKN 1 CIBINONG'),

    /*
    |--------------------------------------------------------------------------
    | Koordinat GPS Sekolah
    |--------------------------------------------------------------------------
    | Latitude dan Longitude titik pusat sekolah. Digunakan untuk menghitung
    | jarak lokasi siswa saat absen.
    */
    'latitude'  => (float) env('SCHOOL_LAT', -6.521976890944639),
    'longitude' => (float) env('SCHOOL_LNG', 106.80741031694744),

    /*
    |--------------------------------------------------------------------------
    | Radius Absen (meter)
    |--------------------------------------------------------------------------
    | Jarak maksimum dari titik pusat sekolah agar absen dapat dilakukan.
    | Default: 100 meter.
    */
    'radius' => (int) env('SCHOOL_RADIUS', 100),

];
