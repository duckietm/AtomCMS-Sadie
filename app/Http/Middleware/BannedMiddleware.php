<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BannedMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $authenticated = Auth::check();

        if ($request->is('logout')) {
            return $next($request);
        }

        if (! $authenticated) {
            if ($request->is('banned')) {
                return to_route('login');
            }

            return $next($request);
        }

        $accountBan = $request->user()?->ban;

        if ($accountBan && ! $request->is('banned')) {
            return to_route('banned.show');
        }

        if (! $accountBan && $request->is('banned')) {
            return to_route('me.show');
        }

        return $next($request);
    }
}
