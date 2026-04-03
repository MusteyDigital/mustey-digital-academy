<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseChatMessage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'course_id',
        'user_id',
        'parent_id',
        'body',
        'is_pinned',
        'edited_at',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'edited_at' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
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
        return $this->hasMany(self::class, 'parent_id')
            ->with('user')
            ->orderBy('created_at');
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }
}
