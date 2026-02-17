<?php

namespace App\Mail;

use App\Models\Course;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EnrollmentConfirmed extends Mailable implements ShouldQueue

{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Course $course
    ) {}

    public function build()
    {
        return $this
            ->subject('Enrollment Confirmed: ' . $this->course->title)
            ->view('emails.enrollment-confirmed')
            ->with([
                'user' => $this->user,
                'course' => $this->course,
            ]);
    }
}
