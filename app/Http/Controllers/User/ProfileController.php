<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Game\Guild\GuildMember;
use App\Models\User\PlayerFriendship;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function __invoke(User $user)
    {
        $user = $this->loadUserRelations($user);

        $friends = $this->getUserFriends($user->id);
        $groups  = $this->getUserGroups($user->id);

        return view('user.profile', [
            'user'       => $user,
            'friends'    => $friends,
            'groups'     => $groups,
            'guestbook'  => $user->profileGuestbook()->with('user')->latest()->limit(5)->get(),
            'photos'     => $user->photos()->limit(3)->get(),
        ]);
    }

    private function loadUserRelations(User $user): User
    {
        return $user->load([
            'badges' => function ($badges) {
                $badges->orderBy('slot')->take(5);
            },
            'rooms' => function ($rooms) {
                $rooms->select('id', 'owner_id', 'name', 'max_users_allowed', 'created_at')
                      ->orderByDesc('created_at')
                      ->orderBy('id');
            },
        ]);
    }

    private function getUserFriends(int $userId)
	{
    return PlayerFriendship::query()
        ->where('status', 1)
        ->where(function ($query) use ($userId) {
            $query->where('origin_player_id', $userId)
                  ->orWhere('target_player_id', $userId);
        })
        ->with([
            'originPlayer:id,username',
            'originPlayer.avatar:player_id,figure_code',
            'targetPlayer:id,username',
            'targetPlayer.avatar:player_id,figure_code',
        ])
        ->inRandomOrder()
        ->take(12)
        ->get();
	}

    private function getUserGroups(int $userId)
    {
        return DB::table('group_player')
            ->join('groups', 'group_player.group_id', '=', 'groups.id')
            ->select(
                'group_player.group_id',
                'group_player.player_id',
                'groups.name',
                'groups.room_id'
            )
            ->where('group_player.player_id', $userId)
            ->inRandomOrder()
            ->take(6)
            ->get();
    }
}
