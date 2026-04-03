<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\LiveSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LiveSessionController extends Controller
{
    public function start(Course $course)
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, ['admin', 'instructor'])) {
            abort(403);
        }

        if ($user->role === 'instructor' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        $existing = $course->liveSessions()
            ->where('status', 'live')
            ->latest()
            ->first();

        if ($existing) {
            return redirect()->route('live-sessions.show', $existing->id)
                ->with('success', 'A live session is already active.');
        }

        $session = LiveSession::create([
            'course_id' => $course->id,
            'instructor_id' => $user->id,
            'title' => 'Live Session - ' . $course->title,
            'room_name' => 'nexdus-course-' . $course->id . '-' . Str::lower(Str::random(8)),
            'status' => 'live',
            'starts_at' => now(),
        ]);

        return redirect()->route('live-sessions.show', $session->id)
            ->with('success', 'Live session started successfully.');
    }

    public function show(LiveSession $liveSession)
    {
        if (!Auth::check()) {
            abort(403);
        }

        $liveSession->load(['course', 'instructor']);

        if ($liveSession->status === 'ended') {
            return redirect()->route('courses.show', $liveSession->course_id)
                ->with('error', 'This live session has ended.');
        }

        return view('live-sessions.show', compact('liveSession'));
    }

    public function end(LiveSession $liveSession)
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, ['admin', 'instructor'])) {
            abort(403);
        }

        if ($user->role === 'instructor' && $liveSession->instructor_id !== $user->id) {
            abort(403);
        }

        $liveSession->update([
            'status' => 'ended',
            'ended_at' => now(),
        ]);

        return redirect()->route('courses.show', $liveSession->course_id)
            ->with('success', 'Live session ended.');
    }
}
