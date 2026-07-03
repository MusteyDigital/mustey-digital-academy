<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DemoAccountRestrictions
{
    const DEMO_EMAILS = [
        'demo-student@musteydigitalacademy.online',
        'demo-instructor@musteydigitalacademy.online',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && in_array($user->email, self::DEMO_EMAILS)) {
            if ($request->isMethod('POST') || $request->isMethod('PUT') || $request->isMethod('PATCH')) {
                if (str_contains($request->path(), 'profile') || str_contains($request->path(), 'password')) {
                    return redirect()->back()->with('error', '⚠️ This action is disabled in demo mode.');
                }
            }
        }

        return $next($request);
    }
}
