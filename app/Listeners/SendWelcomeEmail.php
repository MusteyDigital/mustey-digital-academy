<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail
{
    public function handle(Registered $event): void
    {
        $user = $event->user;

        Mail::to($user->email)->queue(new WelcomeEmail($user));
    }
}
