<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrabAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'lesson_id',
        'difficulty',
        'total_tasks',
        'correct_tasks',
        'accuracy',
        'results_json',
    ];

    protected $casts = [
        'accuracy' => 'decimal:2',
        'results_json' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
