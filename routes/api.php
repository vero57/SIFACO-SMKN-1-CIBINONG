<?php

use App\Http\Controllers\API\TelegramWebhookController;
use App\Http\Middleware\CheckApiKey; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/telegram/webhook', [TelegramWebhookController::class, 'handle'])
    ->name('telegram.webhook');




Route::middleware([CheckApiKey::class])->group(function () {
    
    // endpoint data absen
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

    // Endpoint user dengan relasi role, classes, dan students di setiap class
    Route::get('/users', function (Request $request) {
        return \App\Models\User::with([
            'role',
            'classes.students'
        ])->get();
    });

    // Endpoint untuk get semua data classes beserta walas dan students
    Route::get('/classes', function () {
        return \App\Models\ClassModel::with(['walas', 'students'])->get();
    });

    // Endpoint untuk get semua data class_student
    Route::get('/class-students', function () {
        return \App\Models\ClassStudent::with([
            'class' => function ($query) {
                $query->select('id', 'name');
            },
            'student' => function ($query) {
                $query->select('id', 'name');
            }
        ])->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'class_name' => $item->class->name ?? null,
                'student_name' => $item->student->name ?? null,
            ];
        });
    });

});
