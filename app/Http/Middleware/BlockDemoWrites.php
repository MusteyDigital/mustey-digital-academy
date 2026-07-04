<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlockDemoWrites
{
    protected array $demoEmails = [
        'demo-student@musteydigitalacademy.online',
        'demo-instructor@musteydigitalacademy.online',
    ];

    /**
     * Write actions that ONLY touch the demo user's own progress data
     * (never real course structure, never other users' data) are safe
     * to allow — this is what makes "watch lessons, take quizzes" work
     * in the demo.
     */
    protected array $allowedWriteRoutes = [
        'lessons.complete',
        'lessons.notes.store',
        'lessons.video-progress.store',
        'quizzes.start',
        'quizzes.submit',
    ];

    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && in_array($user->email, $this->demoEmails, true)) {
            $isWrite = !in_array($request->method(), ['GET', 'HEAD'], true);
            $routeName = $request->route()?->getName();

            if ($isWrite && !in_array($routeName, $this->allowedWriteRoutes, true)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'This is a demo account — that action is disabled.',
                    ], 403);
                }

                return back()->with('error', '🚫 This is a demo account. That action is disabled — but you can still complete lessons and take quizzes freely.');
            }
        }

        return $next($request);
    }
}
