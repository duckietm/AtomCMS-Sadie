<?php

namespace App\Http\Requests;

use App\Rules\GoogleRecaptchaRule;
use App\Rules\WebsiteWordfilterRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use RyanChandler\LaravelCloudflareTurnstile\Rules\Turnstile;

class AccountSettingsFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()?->id;

        $rules = [
            'username' => ['sometimes', 'string', sprintf('regex:%s', setting('username_regex')), 'min:3', 'max:25', Rule::unique('players', 'username')->ignore($userId), new WebsiteWordfilterRule],
            'mail' => ['required', 'email', Rule::unique('players', 'email')->ignore($userId), new WebsiteWordfilterRule],
            'motto' => ['nullable', 'string', 'max:127', new WebsiteWordfilterRule]];

        if (setting('google_recaptcha_enabled')) {
            $rules['g-recaptcha-response'] = ['required', 'string', new GoogleRecaptchaRule];
        } else {
            $rules['g-recaptcha-response'] = ['sometimes', 'nullable'];
        }

        if (setting('cloudflare_turnstile_enabled')) {
            $rules['cf-turnstile-response'] = ['required', 'string', app(Turnstile::class)];
        } else {
            $rules['cf-turnstile-response'] = ['sometimes', 'nullable'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'g-recaptcha-response.required' => __('The Google recaptcha must be completed'),
            'g-recaptcha-response.string' => __('The google recaptcha was submitted with an invalid type'),
            'cf-turnstile-response.required' => __('Please complete the captcha.'),
        ];
    }
}
