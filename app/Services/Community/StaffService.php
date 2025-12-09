<?php

namespace App\Services\Community;

use App\Models\User;
use App\Models\User\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class StaffService
{
    public function fetchStaffPositions(): Collection
    {
        $cacheEnabled = setting('enable_caching') === '1';

        if ($cacheEnabled && Cache::has('staff_positions')) {
            return Cache::get('staff_positions');
        }

        $currentRank = Auth::check() ? (int) Auth::user()->rank : 0;
        $minSeeHidden = (int) setting('min_rank_to_see_hidden_staff');
        $minStaffRank = (int) setting('min_staff_rank');

        $employees = Role::query()
            ->select('id', 'name', 'staff_color', 'job_description', 'hidden_rank', 'hidden_staff', 'staff_background')
            ->when($currentRank < $minSeeHidden, function ($query) {
                return $query->where('hidden_rank', false);
            })
            ->where('id', '>=', $minStaffRank)
            ->orderByDesc('id')
            ->with(['users' => function ($query) {
                $query->with([
                    'avatar:player_id,figure_code,motto',
                    'data:player_id,is_online,last_online',
                ]);
            }])
            ->get();

        if ($cacheEnabled) {
            $cacheTimer = (int) setting('cache_timer');
            Cache::put('staff_positions', $employees, now()->addMinutes($cacheTimer));
        }

        return $employees;
    }

    public function fetchEmployeeIds(): array
    {
        $cacheEnabled = setting('enable_caching') === '1';

        if ($cacheEnabled && Cache::has('staff_ids')) {
            return Cache::get('staff_ids');
        }

        $minStaffRank = (int) setting('min_staff_rank');

        $staffIds = User::query()
            ->whereHas('role', function ($query) use ($minStaffRank) {
                $query->where('role_id', '>=', $minStaffRank);
            })
            ->pluck('id')
            ->toArray();

        if ($cacheEnabled) {
            $cacheTimer = (int) setting('cache_timer');
            Cache::put('staff_ids', $staffIds, now()->addMinutes($cacheTimer));
        }

        return $staffIds;
    }
}
