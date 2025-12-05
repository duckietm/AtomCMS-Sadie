<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Articles\WebsiteArticle;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MeController extends Controller
{
    public function __invoke(): View
    {
        $user = Auth::user();

        if ($user) {
            $user->load([
                'rank',
                'avatar',
                'data',
            ]);
        }

        return view('user.me', [
            'onlineFriends' => $user?->getOnlineFriends(),
            'user'          => $user,
            'articles'      => WebsiteArticle::whereHas('user')
                ->with([
                    'user:id,username',
                    'user.avatar:player_id,figure_code',
                ])
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }
}
