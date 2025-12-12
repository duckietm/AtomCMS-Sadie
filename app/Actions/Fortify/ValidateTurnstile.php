<?php

namespace App\Actions\Fortify;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use RyanChandler\LaravelCloudflareTurnstile\Rules\Turnstile;

class ValidateTurnstile
{
    public function handle(Request $request, Closure $next)
    {
        if (setting('cloudflare_turnstile_enabled')) {
            Validator::make($request->all(), [
                'cf-turnstile-response' => ['required', app(Turnstile::class)],
            ], [
                'cf-turnstile-response.required' => __('Please complete the captcha.'),
            ])->validate();
        }

        return $next($request);
    }
}