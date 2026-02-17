<?php

namespace App\Mail;

use App\Models\Course;
use App\Models\User;
use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CourseCompleted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Course $course,
        public Certificate $certificate
    ) {}

    public function build()
    {
        return $this
            ->subject("🎉 Congratulations! You completed {$this->course->title}")
            ->markdown('emails.course-completed');
    }
}
