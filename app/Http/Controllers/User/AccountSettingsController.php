<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccountSettingsFormRequest;
use App\Services\RconService;
use App\Services\User\SessionService;
use App\Services\User\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AccountSettingsController extends Controller
{
    public function __construct(
        private readonly SessionService $sessionService,
        private readonly UserService $userService,
        private readonly RconService $rconService
    ) {}

    public function edit(): View
    {
        return view('user.settings.account', [
            'user' => Auth::user(),
        ]);
    }

    public function sessionLogs(Request $request): View
    {
        $sessions = $this->sessionService->fetchSessionLogs($request);

        return view('user.settings.session-logs', [
            'logs' => $sessions,
        ]);
    }

    public function update(AccountSettingsFormRequest $request): RedirectResponse
    {
        $user = Auth::user();

        if (! $user) {
            return back()->withErrors(['message' => 'User not found']);
        }

        if (! $this->rconService->isConnected() && $user->online) {
            return back()->withErrors(['message' => __('You must be offline to change your account settings')]);
        }

        if ($user->email !== $request->input('mail')) {
			$this->userService->updateField($user, 'email', $request->input('mail'));
		}


        $newMotto = $request->input('motto');
        $currentMotto = $user->motto;

        if ($currentMotto !== $newMotto) {
            if ($this->rconService->isConnected()) {
                $this->rconService->setMotto($user, $newMotto);
            }

            $user->avatar()->updateOrCreate(
                ['player_id' => $user->id],
                ['motto' => $newMotto]
            );
        }

        return to_route('settings.account.show')
            ->with('success', __('Your account settings has been updated'));
    }

    public function twoFactor(): View
    {
        return view('user.settings.two-factor');
    }
}
