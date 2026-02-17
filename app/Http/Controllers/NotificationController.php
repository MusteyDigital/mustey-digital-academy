<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $notifications = $user->notifications()->latest()->paginate(15);

        return view('notifications.index', compact('notifications'));
    }

    public function read($id)
    {
        $notification = Auth::user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        return back();
    }

    public function readAll()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back();
    }
}
