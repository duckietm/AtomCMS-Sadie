<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Models\User\PlayerData;
use App\Services\Community\StaffService;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    protected array $staffIds = [];

    public function __construct(private readonly StaffService $staffService)
    {
        $this->staffIds = $this->staffService->fetchEmployeeIds();
    }

    public function __invoke(): View
    {
        $topCredits = PlayerData::query()
            ->whereNotIn('player_id', $this->staffIds)
            ->orderByDesc('credit_balance')
            ->take(9)
            ->with([
                'player:id,username',
                'player.avatar:player_id,figure_code',
            ])
            ->get();

        $getBalanceTop = function (string $column) {
            return PlayerData::query()
                ->whereNotIn('player_id', $this->staffIds)
                ->orderByDesc($column)
                ->take(9)
                ->with([
                    'player:id,username',
                    'player.avatar:player_id,figure_code',
                ])
                ->get();
        };

        return view('leaderboard', [
            'credits' => $topCredits,
            'duckets' => $getBalanceTop('pixel_balance'),
            'diamonds' => $getBalanceTop('seasonal_balance'),
            'gotw' => $this->retrieveStats('gotw_points'),
            'respectsReceived' => $this->retrieveStats('respect_points'),
            'achievementScores' => $this->retrieveStats('achievement_score'),
        ]);
    }

    private function retrieveStats(string $column)
    {
        return PlayerData::select('player_id', $column)
            ->whereNotIn('player_id', $this->staffIds)
            ->orderByDesc($column)
            ->take(9)
            ->with([
                'player:id,username',
                'player.avatar:player_id,figure_code',
            ])
            ->get();
    }
}
