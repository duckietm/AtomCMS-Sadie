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

        return Cache::rememberForever('website_settings', function () {
			return WebsiteSetting::query()->pluck('value', 'key');
		});
    }

    public function getOrDefault(string $settingName, mixed $default = null): mixed
    {
        $value = $this->settings->get($settingName);

        if ($value === null || $value === '') {
            return $default;
        }

        return $value;
    }
	
	public function refresh(): void
	{
		Cache::forget('website_settings');
		$this->settings = $this->loadSettings();
	}
}
