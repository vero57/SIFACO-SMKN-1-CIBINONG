<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViolationPoint extends Model
{
    protected $fillable = ['student_id', 'rule_id', 'attendance_id', 'date'];

    public function rule()
    {
        return $this->belongsTo(ViolationRule::class, 'rule_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class, 'attendance_id');
    }
}
