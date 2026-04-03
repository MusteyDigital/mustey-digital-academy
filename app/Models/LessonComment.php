<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonComment extends Model
{
    protected $table = 'lesson_discussion_messages';
    protected $fillable = [
        'lesson_id',
        'user_id',
        'parent_id',
        'comment',
        'type',
        'votes',
        'is_pinned',
        'is_best_answer',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_best_answer' => 'boolean',
        'votes' => 'integer',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(self::class, 'parent_id')->with('user')->latest();
    }
}
