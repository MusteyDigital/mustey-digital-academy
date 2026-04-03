<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignmentSubmission extends Model
{
    protected $fillable = [
        'lesson_assignment_id',
        'user_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'student_note',
        'score',
        'instructor_feedback',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function assignment()
    {
        return $this->belongsTo(LessonAssignment::class, 'lesson_assignment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getHumanFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return 'Unknown size';
        }

        if ($this->file_size >= 1048576) {
            return number_format($this->file_size / 1048576, 2) . ' MB';
        }

        return number_format($this->file_size / 1024, 1) . ' KB';
    }
}
