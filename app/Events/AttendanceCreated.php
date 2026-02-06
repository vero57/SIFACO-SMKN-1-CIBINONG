<?php

namespace App\Events;

use App\Models\Attendance;

class AttendanceCreated
{
    public function __construct(
        public Attendance $attendance
    ) {}
}
