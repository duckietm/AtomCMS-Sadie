<?php

namespace App\Http\Controllers\Shop;

use App\Actions\SendCurrency;
use App\Actions\SendFurniture;
use App\Http\Controllers\Controller;
use App\Models\Game\Badge; // <- make sure you have this model for `badges`
use App\Models\Shop\WebsiteShopArticle;
use App\Models\Shop\WebsiteShopCategory;
use App\Models\User;
use App\Models\User\PlayerRole;
use App\Services\RconService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ShopController extends Controller
{
    public function __construct(private readonly RconService $rconService) {}

    public function __invoke(?WebsiteShopCategory $category)
    {
        $packages = WebsiteShopArticle::orderBy('position');

        if ($category && $category->exists) {
            $packages = $category->articles()->orderBy('position');
        }

        return view('shop.shop', [
            'articles' => $packages
                ->with(['role:id,name', 'features']) // make sure WebsiteShopArticle has role() relation
                ->get(),
            'categories' => WebsiteShopCategory::whereHas('articles')->get(),
        ]);
    }

    private function giveBadges(User $user, string $badges): void
    {
        // incoming string: "NL388;NL390;NL391"
        $badgeCodes = array_values(array_filter(array_map('trim', explode(';', $badges))));
        if (empty($badgeCodes)) {
            return;
        }

        // Which badge codes does the user already own? (new structure)
        $ownedCodes = $user->badges()
            ->with('badge:id,code')
            ->get()
            ->pluck('badge.code')
            ->filter()
            ->unique()
            ->values()
            ->all();

        foreach ($badgeCodes as $code) {
            if (in_array($code, $ownedCodes, true)) {
                continue;
            }

            // Resolve badge_id from badges table
            $badgeId = Badge::query()->where('code', $code)->value('id');
            if (! $badgeId) {
                continue; // badge code not found in badges table
            }

            // If RCON is available, use it
            if ($this->rconService->isConnected) {
                $this->rconService->giveBadge($user, $code);
                continue;
            }

            // Otherwise insert into player_badges
            $user->badges()->updateOrCreate(
                [
                    'player_id' => $user->id,
                    'badge_id'  => $badgeId,
                ],
                [
                    'slot' => 0, // keep 0 = in inventory, >0 = equipped
                ]
            );
        }
    }

    public function purchase(WebsiteShopArticle $package, Request $request, SendCurrency $sendCurrency): Response
    {
        $buyer = Auth::user();
        $receiver = $buyer;

        if ($request->filled('receiver')) {
            if (! $package->is_giftable) {
                return to_route('shop.index')->withErrors(['message' => __('This package is not giftable')]);
            }

            $receiver = User::query()
                ->where('username', $request->input('receiver'))
                ->first();

            if (! $receiver) {
                return to_route('shop.index')->withErrors(['message' => __('Recipient not found')]);
            }
        }

        // Rank check (uses your accessor getRankAttribute())
        if ($package->give_rank && $receiver->rank >= (int) $package->give_rank) {
            $message = $receiver->id === $buyer->id
                ? __('You are already this or a higher rank')
                : __('The recipient is already this or a higher rank');

            return to_route('shop.index')->withErrors(['message' => $message]);
        }

        // If no RCON, receiver must be offline (your User::getOnlineAttribute() returns bool)
        if (! $this->rconService->isConnected && $receiver->online) {
            return to_route('shop.index')->withErrors(['message' => __('Please logout before purchasing a package')]);
        }

        if ($buyer->website_balance < $package->price()) {
            return to_route('shop.index')->withErrors([
                'message' => __('You need to top-up your account with another $:amount to purchase this package', [
                    'amount' => ($package->price() - $buyer->website_balance),
                ]),
            ]);
        }

        $buyer->decrement('website_balance', $package->price());

        $sendCurrency->execute($receiver, 'credits', (int) $package->credits);
        $sendCurrency->execute($receiver, 'duckets', (int) $package->duckets);
        $sendCurrency->execute($receiver, 'diamonds', (int) $package->diamonds);

        // Give rank (NEW structure: player_roles)
        if ($package->give_rank) {
            $newRank = (int) $package->give_rank;

            if ($this->rconService->isConnected) {
                $this->rconService->setRank($receiver, $newRank);
                $this->rconService->disconnectUser($receiver);
            } else {
                PlayerRole::query()->updateOrCreate(
                    ['player_id' => $receiver->id],
                    ['role_id' => $newRank]
                );
            }
        }

        if (! empty($package->badges)) {
            $this->giveBadges($receiver, $package->badges);
        }

        if (! empty($package->furniture)) {
            $this->handleFurniture(json_decode($package->furniture, true));
        }

        $message = $receiver->id === $buyer->id
            ? __('You have successfully purchased the package :name', ['name' => $package->name])
            : __('You have successfully purchased the package :name for :username', [
                'name' => $package->name,
                'username' => $receiver->username,
            ]);

        return to_route('shop.index')->with('success', $message);
    }

    public function handleFurniture(array $furniture): void
    {
        $sendFurniture = app(SendFurniture::class);
        $sendFurniture->execute(Auth::user(), $furniture);
    }
}
