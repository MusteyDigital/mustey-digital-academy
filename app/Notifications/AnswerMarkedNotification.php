<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AnswerMarkedNotification extends Notification
{
    use Queueable;

    public $comment;
    public $lessonTitle;
    public $url;

    public function __construct($comment, string $lessonTitle, string $url)
    {
        $this->comment = $comment;
        $this->lessonTitle = $lessonTitle;
        $this->url = $url;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "Your question was marked as the answer in \"{$this->lessonTitle}\"",
            'comment_id' => $this->comment->id,
            'url' => $this->url,
        ];
    }
}
