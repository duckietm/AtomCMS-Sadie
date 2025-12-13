<?php

namespace App\Http\Requests;

use App\Rules\GoogleRecaptchaRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use RyanChandler\LaravelCloudflareTurnstile\Rules\Turnstile;

class RegisterFormRequest extends FormRequest
{
    protected $errorBag = 'register';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'username' => ['required', 'string', sprintf('regex:%s', setting('username_regex')), 'max:25', Rule::unique('players', 'username')],
            'mail' => ['required', 'string', 'email', 'max:255', Rule::unique('players', 'email')],
            'password' => ['required', 'string', 'confirmed', 'min:8'],
            'terms' => ['required', 'accepted'],
        ];

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
