<?php

namespace App\Http\Middleware;

use App\Models\User\Ban;
use App\Models\User\BannedIpAddress;
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

        $ipBan = BannedIpAddress::active()
            ->where('ip_address', $request->ip())
            ->exists();

        if (! $authenticated) {
            if ($ipBan && ! $request->is('banned')) {
                return to_route('banned.show');
            }

            if (! $ipBan && $request->is('banned')) {
                return to_route('login');
            }

            return $next($request);
        }

        $user       = $request->user();
        $accountBan = $user?->ban;

        if (($ipBan || $accountBan) && ! $request->is('banned')) {
            return to_route('banned.show');
        }

        if (! $ipBan && ! $accountBan && $request->is('banned')) {
            return to_route('me.show');
        }

        return $next($request);
    }
}
