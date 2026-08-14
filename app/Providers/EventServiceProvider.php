<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\AttendanceCreated;
use App\Events\PermissionCreated;
use App\Listeners\SendAttendanceWhatsapp;
use App\Listeners\SendAttendanceTelegram;
use App\Listeners\SendPermissionTelegram;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        AttendanceCreated::class => [
            SendAttendanceTelegram::class,
            // SendAttendanceWhatsapp::class,
        ],
        PermissionCreated::class => [
            SendPermissionTelegram::class,
        ],
    ];

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }

    public function boot(): void
    {
        //
    }
}
