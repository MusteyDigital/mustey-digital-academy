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

    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && in_array($user->email, $this->demoEmails, true)) {
            if (!in_array($request->method(), ['GET', 'HEAD'], true)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'This is a demo account — write actions are disabled.',
                    ], 403);
                }

                return back()->with('error', '🚫 This is a demo account. Creating, editing, and deleting is disabled — explore freely, but changes are not saved.');
            }
        }

        return $next($request);
    }
}
