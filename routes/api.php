<?php

use App\Http\Middleware\CheckApiKey; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');




Route::middleware([CheckApiKey::class])->group(function () {
    
    Route::get('/data-absen', function (Request $request) {
        return DB::table('attendances')
            ->join('users', 'attendances.student_id', '=', 'users.id')
            ->join('classes', 'attendances.class_id', '=', 'classes.id')
            ->join('attendance_statuses', 'attendances.status_id', '=', 'attendance_statuses.id')
            ->select(
                'attendances.*', 
                'users.name as student_name', 
                'classes.name as class_name',
                'attendance_statuses.name as status_name',
                DB::raw("CONCAT('" . url('/') . "/', attendances.photo) as photo_url")
            )
            ->get();
    });

    // Endpoint user + role, pakai token
    Route::get('/users', function (Request $request) {
        return \App\Models\User::with('role')->get();
    });

});