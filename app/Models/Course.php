<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'thumbnail',
        'meeting_url',
        'starts_at',
        'instructor_id',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
    ];

    // Course belongs to an instructor (user)
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    // Course has many lessons
    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    // Course has many enrollments rows
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    // Students enrolled in this course (many-to-many via enrollments)
    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments')
            ->withPivot('status')
            ->withTimestamps();
    }
}
