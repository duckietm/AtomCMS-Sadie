<?php

namespace App\Observers;

use App\Models\Miscellaneous\WebsiteSetting;
use Illuminate\Support\Facades\Cache;

class WebsiteSettingObserver
{
    public function created(WebsiteSetting $websiteSetting): void
    {
        $this->clearSettingsCache();
    }

    public function updated(WebsiteSetting $websiteSetting): void
    {
        $this->clearSettingsCache();
    }

    public function deleted(WebsiteSetting $websiteSetting): void
    {
        $this->clearSettingsCache();
    }

    public function forceDeleted(WebsiteSetting $websiteSetting): void
    {
        $this->clearSettingsCache();
    }

    private function clearSettingsCache(): void
    {
        Cache::forget('website_settings');
    }
}