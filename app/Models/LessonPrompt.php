<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonPrompt extends Model
{
    protected $fillable = ['lesson_id', 'type', 'prompt'];
}
