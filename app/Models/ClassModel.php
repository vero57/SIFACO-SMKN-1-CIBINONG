<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    protected $table = 'classes';
    protected $fillable = ['name', 'walas_id'];

    public function students()
    {
        return $this->belongsToMany(
            User::class,
            'class_student',
            'class_id',
            'student_id'
        );
    }

    public function subjects()
    {
        return $this->belongsToMany(
            Subject::class,
            'class_subject',
            'class_id',
            'subject_id'
        );
    }

    public function walas()
    {
        return $this->belongsTo(User::class, 'walas_id');
    }

    public function attendanceSchedule()
    {
        return $this->hasOne(\App\Models\AttendanceSchedule::class, 'class_id');
    }
}
