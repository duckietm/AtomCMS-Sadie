<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User\BannedIpAddress;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BannedController extends Controller
{
    public function __invoke(): View
    {
        $ipBan = BannedIpAddress::active()
            ->where('ip_address', request()->ip())
            ->orderByDesc('id')
            ->first();

        $accountBan = Auth::check() ? Auth::user()->ban : null;

        $ban = $ipBan ?? $accountBan;

        return view('banned', [
            'ban' => $ban,
            'ipBan' => $ipBan,
            'accountBan' => $accountBan,
        ]);
    }
}
