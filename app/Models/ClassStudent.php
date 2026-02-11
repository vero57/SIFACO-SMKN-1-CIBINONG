<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassStudent extends Model
{
    protected $table = 'class_student';
    protected $fillable = ['class_id', 'student_id'];

    public function class()
    {
        return $this->belongsTo(\App\Models\ClassModel::class, 'class_id');
    }

    public function student()
    {
        return $this->belongsTo(\App\Models\User::class, 'student_id');
    }
}
