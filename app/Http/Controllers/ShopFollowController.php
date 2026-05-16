<?php

namespace App\Http\Controllers;

use App\Models\ShopFollow;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopFollowController extends Controller
{
    public function store(Request $request, User $user): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        abort_unless($user->isSeller() && $user->sellerProfile?->isMarketplaceVisible(), 404);

        if ((int) $user->id === (int) Auth::id()) {
            return $this->respond($request, $user, false, 'You cannot follow your own shop.');
        }

        ShopFollow::firstOrCreate([
            'user_id' => Auth::id(),
            'seller_user_id' => $user->id,
        ]);

        return $this->respond($request, $user, true, 'Shop followed.');
    }

    public function destroy(Request $request, User $user): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        ShopFollow::query()
            ->where('user_id', Auth::id())
            ->where('seller_user_id', $user->id)
            ->delete();

        return $this->respond($request, $user, false, 'Shop unfollowed.');
    }

    private function respond(Request $request, User $seller, bool $isFollowing, string $message): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $followerCount = $seller->shopFollowers()->count();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'is_following' => $isFollowing,
                'follower_count' => $followerCount,
            ]);
        }

        return back()->with('success', $message);
    }
}
