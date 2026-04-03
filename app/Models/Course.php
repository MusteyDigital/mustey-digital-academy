<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'price',
        'thumbnail',
        'meeting_url',
        'starts_at',
        'instructor_id',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
    ];

    // Instructor relationship
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    // Modules (ordered)
    public function modules()
    {
        return $this->hasMany(Module::class)->orderBy('order');
    }

    // Lessons
    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    // Enrollments
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    // Students (many-to-many)
    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments')
            ->withPivot('status')
            ->withTimestamps();
    }

    // Quizzes
    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    // Live sessions
    public function liveSessions()
    {
        return $this->hasMany(LiveSession::class);
    }

    public function activeLiveSession()
    {
        return $this->hasOne(LiveSession::class)
            ->where('status', 'live')
            ->latestOfMany();
    }

    public function courseChatMessages()
    {
        return $this->hasMany(\App\Models\CourseChatMessage::class)
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->orderByDesc('is_pinned')
            ->latest();
    }
}
