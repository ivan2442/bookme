<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OwnerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! in_array($request->user()->role, ['owner', 'admin'], true)) {
            return redirect()->route('auth.login')->with('error', 'Potrebné je prihlásenie ako prevádzka.');
        }

        return $next($request);
    }
}
