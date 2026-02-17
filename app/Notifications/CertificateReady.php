<?php

namespace App\Notifications;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CertificateReady extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Course $course,
        public string $serial,
        public string $verifyUrl,
        public string $downloadUrl
    ) {}

    public function via($notifiable): array
    {
        // database for bell icon + mail for email (optional)
        return ['database'];
        // If later you want both:
        // return ['database', 'mail'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'certificate_ready',
            'title' => 'Certificate Ready ✅',
            'message' => "Your certificate for {$this->course->title} is ready.",
            'course_id' => $this->course->id,
            'serial' => $this->serial,
            'verify_url' => $this->verifyUrl,
            'download_url' => $this->downloadUrl,
        ];
    }

    // Only needed if you enable mail in via()
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Certificate is Ready ✅')
            ->greeting("Hello {$notifiable->name},")
            ->line("Your certificate for {$this->course->title} is ready.")
            ->action('Download Certificate', $this->downloadUrl)
            ->line("Verify link: {$this->verifyUrl}");
    }
}
