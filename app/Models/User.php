<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Lesson;

class User extends Authenticatable
{

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
protected $fillable = [
    'name',
    'email',
    'password',
    'role',
    'profile_picture', // optional if you have it
];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Courses this user teaches (if instructor)
public function coursesTaught()
{
    return $this->hasMany(Course::class, 'instructor_id');
}

// Enrollments for this user (if student)
public function enrollments()
{
    return $this->hasMany(Enrollment::class, 'user_id');
}

// Courses this student is enrolled in (via enrollments)
public function coursesEnrolled()
{
    return $this->belongsToMany(
        Course::class,
        'enrollments',
        'user_id',
        'course_id'
    )->withPivot('status')->withTimestamps();
}
public function completedLessons()
{
    return $this->belongsToMany(Lesson::class, 'lesson_completions')
        ->withPivot('completed_at')
        ->withTimestamps();
}
public function lessonCompletions()
{
    return $this->hasMany(\App\Models\LessonCompletion::class);
}

public function drabAttempts()
{
    return $this->hasMany(\App\Models\DrabAttempt::class);
}

public function lessonDiscussionMessages()
{
    return $this->hasMany(\App\Models\LessonDiscussionMessage::class);
}

public function courseChatMessages()
{
    return $this->hasMany(\App\Models\CourseChatMessage::class);
}
}