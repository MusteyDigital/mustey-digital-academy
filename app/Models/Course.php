<?php

namespace App\Models;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'meeting_url',
        'starts_at',
        'instructor_id',
    ];

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }
public function students()
{
    return $this->belongsToMany(User::class, 'enrollments')
        ->withPivot('status')
        ->withTimestamps();
}


    public function coursesEnrolled()
{
    return $this->belongsToMany(Course::class, 'enrollments')
        ->withPivot('status')
        ->withTimestamps();
}

public function coursesTaught()
{
    return $this->hasMany(Course::class, 'instructor_id');
}
public function enrollments()
{
    return $this->hasMany(Enrollment::class);
}

}
