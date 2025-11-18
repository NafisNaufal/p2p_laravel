<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class P2PAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('user_session_id')) {
            return redirect()->route('pin.setup');
        }

        $userPin = \App\Models\UserPin::where('session_id', session('user_session_id'))
            ->where('is_active', true)
            ->first();

        if (!$userPin) {
            session()->forget('user_session_id');
            return redirect()->route('pin.setup');
        }

        return $next($request);
    }
}
