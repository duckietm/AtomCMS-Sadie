<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Mail;

class ForgotPasswordController extends Controller
{
    public function __invoke()
    {
        return view('auth.passwords.forget');
    }

    public function submitForgetPassword(Request $request)
    {
        $request->validate([
            'mail' => 'required|email',
        ]);

        try {
            if (User::where('email', $request->mail)->exists()) {
                $token = Str::uuid();

                PasswordResetToken::create([
                    'email' => $request->mail,
                    'token' => $token,
                ]);

                Mail::send('email.forgetPassword', ['token' => $token], function ($message) use ($request) {
                    $message->to($request->mail);
                    $message->subject('Reset Password');
                });
            }

        } catch (\Exception $e) {
            \Log::error('Mail sending failed', [
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', __(
                'Unable to send email. Please verify your mail settings in the .env file. (Host: :host, Port: :port)',
                [
                    'host' => env('MAIL_HOST', 'unknown'),
                    'port' => env('MAIL_PORT', 'unknown'),
                ],
            ));
        }

        return back()->with('success', __('We have e-mailed your password reset link!'));
    }

    public function showResetPassword(Request $request, string $token)
    {
        $prt = PasswordResetToken::select('token', 'created_at')
            ->where('token', $token)
            ->first();

        if ($prt === null) {
            return to_route('forgot.password.get')->withErrors([
                'message' => __('This token has expired!'),
            ]);
        }

        $tokenExpirationCutoff = Carbon::now()->subMinutes(config('habbo.password_reset_token_time'));

        if ($prt->created_at->lte($tokenExpirationCutoff)) {
            $prt->delete();

            return to_route('forgot.password.get')->withErrors([
                'message' => __('This token has expired!'),
            ]);
        }

        return view('auth.passwords.reset', [
            'token' => $token,
        ]);
    }

    public function submitResetPassword(Request $request, string $token)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        $prt = PasswordResetToken::select('email', 'token')
            ->where('token', $token)
            ->first();

        if ($prt === null) {
            return to_route('forgot.password.get')->withErrors([
                'message' => __('This token has expired!'),
            ]);
        }

        $prt->user->changePassword($request->password);

        $prt->delete();

        return to_route('login')->with('success', __('Your password has been successfully reset!'));
    }
}
