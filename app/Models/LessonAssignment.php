<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonAssignment extends Model
{
    protected $fillable = [
        'lesson_id',
        'title',
        'instructions',
        'due_at',
        'max_score',
        'attachment_path',
        'attachment_name',
    ];

    protected $casts = [
        'due_at' => 'datetime',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }
}
