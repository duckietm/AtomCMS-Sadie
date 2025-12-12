<?php

namespace App\Actions\Fortify;

use App\Actions\Fortify\Rules\PasswordValidationRules;
use App\Models\Miscellaneous\WebsiteBetaCode;
use App\Models\User;
use App\Models\User\PlayerAvatarData;
use App\Models\User\PlayerData;
use App\Models\User\PlayerRole;
use App\Models\User\PlayerWebsiteData;
use App\Rules\BetaCodeRule;
use App\Rules\GoogleRecaptchaRule;
use App\Rules\WebsiteWordfilterRule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use RyanChandler\LaravelCloudflareTurnstile\Rules\Turnstile;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input)
    {
        if ((setting('disable_registration') ?: '0') == '1') {
            throw ValidationException::withMessages([
                'registration' => __('Registration is disabled.'),
            ]);
        }

        $ip = request()?->ip();
        if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6)) {
            throw ValidationException::withMessages([
                'registration' => __('Your IP address seems to be invalid'),
            ]);
        }

        $matchingIpCount = PlayerWebsiteData::query()
            ->where('initial_ip', '=', $ip)
            ->orWhere('last_ip', '=', $ip)
            ->count();

        if ($matchingIpCount >= (int) (setting('max_accounts_per_ip') ?: 99)) {
            throw ValidationException::withMessages([
                'registration' => __('You have reached the max amount of allowed account'),
            ]);
        }

        $this->validate($input);

        $now = now();

        $user = User::create([
            'username' => $input['username'],
            'email' => $input['mail'],
            'password' => Hash::make($input['password']),
            'created_at' => $now,
        ]);

        $user->update([
            'referral_code' => sprintf('%s%s', $user->id, Str::random(8)),
        ]);

        PlayerAvatarData::create([
            'player_id' => $user->id,
            'motto' => setting('start_motto') ?: 'Welcome to the hotel!',
            'gender' => 'M',
            'figure_code' => setting('start_look')
                ?: 'hr-100-61.hd-180-1.ch-210-66.lg-270-110.sh-305-62',
            'chat_bubble_id' => 0,
        ]);

        PlayerData::create([
            'player_id' => $user->id,
            'home_room_id' => (int) (setting('hotel_home_room') ?: 0),
            'credit_balance' => (int) (setting('start_credits') ?: 0),
            'pixel_balance' => 0,
            'seasonal_balance' => 0,
            'gotw_points' => 0,
            'respect_points' => 0,
            'respect_points_pet' => 0,
            'achievement_score' => 0,
            'allow_friend_requests' => 1,
            'is_online' => 0,
            'last_online' => null,
        ]);

        PlayerRole::create([
            'player_id' => $user->id,
            'role_id' => 1,
        ]);

        PlayerWebsiteData::create([
            'player_id' => $user->id,
            'initial_ip' => $ip,
            'last_ip' => $ip,
            'last_login' => $now,
        ]);

        if (setting('requires_beta_code')) {
            WebsiteBetaCode::where('code', '=', $input['beta_code'])->update([
                'user_id' => $user->id,
            ]);
        }

        if (! empty($input['referral_code'])) {
            $referralUser = User::query()
                ->where('referral_code', $input['referral_code'])
                ->first();

            if ($referralUser) {
                $referralUser->load('website');

                $sameIp =
                    ($referralUser->website?->initial_ip === $ip) ||
                    ($referralUser->website?->last_ip === $ip);

                if (! $sameIp) {
                    $referralUser->referrals()->updateOrCreate(
                        ['user_id' => $referralUser->id],
                        [
                            'referrals_total' => ($referralUser->referrals?->referrals_total ?? 0) + 1,
                        ],
                    );

                    $referralUser->userReferrals()->create([
                        'referred_user_id' => $user->id,
                        'referred_user_ip' => $ip,
                    ]);
                }
            }
        }

        if (setting('enable_discord_webhook') === '1') {
            $this->sendDiscordWebhook($user->username, $ip, $user->email);
        }

        return $user;
    }

    private function validate(array $inputs): array
    {
        $rules = [
            'username' => [
                'required',
                'string',
                sprintf('regex:%s', setting('username_regex') ?: '/^[a-zA-Z0-9_.-]+$/'),
                'max:25',
                Rule::unique('players', 'username'),
                new WebsiteWordfilterRule,
            ],
            'mail' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('players', 'email'),
            ],
            'password' => $this->passwordRules(),
            'beta_code' => ['sometimes', 'string', new BetaCodeRule],
            'terms' => ['required', 'accepted'],
            'g-recaptcha-response' => ['sometimes', 'string', new GoogleRecaptchaRule],
            'cf-turnstile-response' => [app(Turnstile::class)],
        ];

        $messages = [
            'g-recaptcha-response.required' => __('The Google recaptcha must be completed'),
            'g-recaptcha-response.string' => __('The google recaptcha was submitted with an invalid type'),
        ];

        return Validator::make($inputs, $rules, $messages)->validate();
    }

    private function sendDiscordWebhook(string $username, string $ip, string $email): void
    {
        if (setting('discord_webhook_url') === '') {
            Log::error('Discord webhook url not provided', [
                'Please provide a discord webhook url before being able to send any webhook requests.',
            ]);

            return;
        }

        $request = Http::asJson()->post(setting('discord_webhook_url'), [
            'username' => sprintf('%s Bot', setting('hotel_name')),
            'content' => "User: {$username} has just registered, with the IP: {$ip} and E-mail: {$email}",
        ]);

        if (! $request->successful()) {
            Log::error('Failed to send Discord webhook notification', [
                'username' => $username,
                'ip' => $ip,
                'email' => $email,
                'response_status' => $request->status(),
                'response_body' => $request->body(),
            ]);
        }
    }
}
