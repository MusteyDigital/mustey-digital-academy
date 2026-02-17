<?php

namespace App\Mail;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CertificateIssued extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Course $course,
        public Certificate $certificate,
        public string $verifyUrl,
        public string $downloadUrl
    ) {}

    public function build()
    {
        return $this->subject('Your Certificate is Ready 🎓')
            ->markdown('emails.certificate-issued');
    }
}
