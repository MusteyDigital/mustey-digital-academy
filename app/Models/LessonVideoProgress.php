<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonVideoProgress extends Model
{
    protected $table = 'lesson_video_progress';

    protected $fillable = [
        'lesson_id',
        'user_id',
        'watched_seconds',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
