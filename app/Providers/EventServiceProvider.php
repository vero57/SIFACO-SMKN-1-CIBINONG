<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\AttendanceCreated;
use App\Listeners\SendAttendanceWhatsapp;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        // AttendanceCreated::class => [
        //     SendAttendanceWhatsapp::class,
        // ],
    ];

    public function boot(): void
    {
        //
    }
}
