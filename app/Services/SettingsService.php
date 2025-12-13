<?php

namespace App\Services;

use App\Models\Miscellaneous\WebsiteSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

class SettingsService
{
    public Collection $settings;

    public function __construct()
    {
        $this->settings = $this->loadSettings();
    }

    private function loadSettings(): Collection
    {
        if (! Schema::hasTable('website_settings')) {
            return collect();
        }

        try {
            return Cache::rememberForever('website_settings', function () {
                return WebsiteSetting::query()->pluck('value', 'key');
            });
        } catch (Throwable $e) {
            return Cache::get('website_settings', collect());
        }
    }

    public function getOrDefault(string $settingName, mixed $default = null): mixed
    {
        $value = $this->settings->get($settingName);

        if ($value === null || $value === '') {
            return $default;
        }

        return $value;
    }
}
