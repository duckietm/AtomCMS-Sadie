<?php

namespace App\Models\Miscellaneous;

use Illuminate\Database\Eloquent\Model;

class WebsiteSetting extends Model
{
    protected $table = 'website_settings';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'key',
        'value',
        'comment',
    ];

    protected $casts = [
        'maintenance_enabled'              => 'boolean',
        'google_recaptcha_enabled'         => 'boolean',
        'cloudflare_turnstile_enabled'     => 'boolean',
        'vpn_block_enabled'                => 'boolean',
        'website_wordfilter_enabled'       => 'boolean',
        'requires_beta_code'               => 'boolean',
        'disable_registration'             => 'boolean',
        'give_hc_on_register'              => 'boolean',
        'enable_discord_webhook'           => 'boolean',
        'force_staff_2fa'                  => 'boolean',

        // Integer settings
        'drawbadge_currency_value'         => 'integer',
        'start_credits'                    => 'integer',
        'start_duckets'                    => 'integer',
        'start_diamonds'                   => 'integer',
        'start_points'                     => 'integer',
        'referrals_needed'                 => 'integer',
        'referral_reward_amount'           => 'integer',
        'point_currency_number'            => 'integer',
        'min_staff_rank'                   => 'integer',
        'min_rank_to_see_hidden_staff'     => 'integer',
        'min_maintenance_login_rank'       => 'integer',
        'min_housekeeping_rank'            => 'integer',
        'max_accounts_per_ip'              => 'integer',
        'max_comment_per_article'          => 'integer',
        'max_guestbook_posts_per_profile'  => 'integer',
        'hotel_home_room'                  => 'integer',
        'hc_on_register_duration'          => 'integer',

        'value' => 'string',
    ];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        return $setting?->value ?? $default;
    }
}