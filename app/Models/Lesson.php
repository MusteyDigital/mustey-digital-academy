<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'course_id',
        'module_id',
        'title',
        'duration',
        'content',
        'video_url',
        'starts_at',
        'order',
        'enable_drab',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'enable_drab' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function completions()
    {
        return $this->hasMany(LessonCompletion::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function resources()
    {
        return $this->hasMany(LessonResource::class)->latest();
    }

    public function notes()
    {
        return $this->hasMany(LessonNote::class);
    }

    public function assignment()
    {
        return $this->hasOne(LessonAssignment::class);
    }


    public function liveSessions()
    {
        return $this->hasMany(LiveSession::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class)->latest();
    }

    public function discussionMessages()
    {
        return $this->hasMany(\App\Models\LessonDiscussionMessage::class)
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->orderByDesc('is_pinned')
            ->orderByDesc('is_answer')
            ->latest();
    }
}
