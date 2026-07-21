<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonDoubtMessage extends Model
{
    protected $fillable = [
        'lesson_id',
        'course_id',
        'user_id',
        'role',
        'body',
        'status',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
