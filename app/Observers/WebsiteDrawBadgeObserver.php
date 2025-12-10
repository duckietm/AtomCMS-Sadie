<?php

namespace App\Observers;

use App\Models\WebsiteDrawBadge;
use Illuminate\Support\Facades\DB;

class WebsiteDrawBadgeObserver
{
    public function updated(WebsiteDrawBadge $websiteDrawBadge): void
    {
        if (! $websiteDrawBadge->wasChanged() || ! $websiteDrawBadge->badge_path) {
            return;
        }

        $badgeCode = pathinfo($websiteDrawBadge->badge_path, PATHINFO_FILENAME);

        if (! $websiteDrawBadge->published) {
            $badgeId = DB::table('badges')->where('code', $badgeCode)->value('id');

            if ($badgeId) {
                DB::table('player_badges')
                    ->where('player_id', $websiteDrawBadge->user_id)
                    ->where('badge_id', $badgeId)
                    ->delete();
            }

            $this->updateExternalTexts(false, $badgeCode);

            return;
        }

        $badgeId = DB::table('badges')->where('code', $badgeCode)->value('id');

        if (! $badgeId) {
            $badgeId = DB::table('badges')->insertGetId([
                'code' => $badgeCode,
            ]);
        }

        $exists = DB::table('player_badges')
            ->where('player_id', $websiteDrawBadge->user_id)
            ->where('badge_id', $badgeId)
            ->exists();

        if (! $exists) {
            DB::table('player_badges')->insert([
                'player_id' => $websiteDrawBadge->user_id,
                'badge_id'  => $badgeId,
                'slot'      => 0,
            ]);
        }

        $this->updateExternalTexts(true, $badgeCode, $websiteDrawBadge->badge_name, $websiteDrawBadge->badge_desc);
    }

    protected function updateExternalTexts(bool $add, string $badgeCode, ?string $name = null, ?string $desc = null): void
    {
        $filePath = DB::table('website_settings')
            ->where('key', 'nitro_external_texts_file')
            ->value('value');

        if (! $filePath || ! file_exists($filePath) || ! is_writable($filePath)) {
            return;
        }

        $json = json_decode(file_get_contents($filePath), true);

        if ($add) {
            $json = array_merge($json, [
                "badge_name_{$badgeCode}" => $name,
                "badge_desc_{$badgeCode}" => $desc,
            ]);
        } else {
            unset($json["badge_name_{$badgeCode}"]);
            unset($json["badge_desc_{$badgeCode}"]);
        }

        file_put_contents($filePath, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
