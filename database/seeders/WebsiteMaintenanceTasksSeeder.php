<?php

namespace Database\Seeders;

use App\Models\Miscellaneous\WebsiteMaintenanceTask;
use App\Models\User;
use App\Models\User\PlayerAvatarData;
use App\Models\User\PlayerData;
use App\Models\User\PlayerRole;
use App\Models\User\PlayerWebsiteData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class WebsiteMaintenanceTasksSeeder extends Seeder
{
    public function run(): void
    {
        $player = User::firstOrCreate(
            ['username' => 'Admin'],
            [
                'email' => 'admin@example.com',
                'password' => Hash::make(Str::password()),
                'created_at' => now(),
            ],
        );

        PlayerAvatarData::firstOrCreate(
            ['player_id' => $player->id],
            [
                'motto' => 'Atom',
                'gender' => 'M',
                'figure_code' => 'fa-201407-1324.hr-828-1035.ch-3001-1261-1408.sh-3068-92-1408.cp-9032-1308.lg-270-1281.hd-209-3',
                'chat_bubble_id' => 0,
            ],
        );

        PlayerData::firstOrCreate(
            ['player_id' => $player->id],
            [
                'home_room_id' => 0,
                'credit_balance' => 0,
                'pixel_balance' => 0,
                'seasonal_balance' => 0,
                'gotw_points' => 0,
                'respect_points' => 0,
                'respect_points_pet' => 0,
                'achievement_score' => 0,
                'allow_friend_requests' => 0,
                'is_online' => 0,
                'last_online' => null,
            ],
        );

        PlayerRole::firstOrCreate(
            [
                'player_id' => $player->id,
                'role_id' => 1,
            ],
        );

        PlayerWebsiteData::firstOrCreate(
            ['player_id' => $player->id],
            [
                'initial_ip' => '127.0.0.1',
                'last_ip' => '127.0.0.1',
                'last_login' => now(),
            ],
        );

        WebsiteMaintenanceTask::firstOrCreate(
            ['task' => 'Working on the hotel'],
            [
                'player_id' => $player->id,
                'completed' => false,
            ],
        );
    }
}
