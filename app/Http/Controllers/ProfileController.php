<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Buyer\UpdateBuyerProfileRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Services\AdminActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    protected function storeProfileImage(Request $request, ?string $oldPath = null): ?string
    {
        if (! $request->hasFile('profile_image')) {
            return $oldPath;
        }

        if (! $request->file('profile_image')->isValid()) {
            return null;
        }

        $newPath = $request->file('profile_image')->store('profile_images', 'public');

        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return $newPath;
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
         return view('seller.profile', [
        'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
   public function update(ProfileUpdateRequest $request, AdminActivityService $adminActivity): RedirectResponse
{
    $user = $request->user();

    $validated = $request->validated();
    $originalEmail = $user->email;
    $changedFields = [];

    // Check current password first before changing anything
    if ($request->filled('password')) {
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Current password is incorrect.'
            ])->withInput();
        }

        $user->password = Hash::make($request->password);
        $changedFields[] = 'password';
    }

    // Update only basic fields
    if (($validated['name'] ?? null) !== $user->name) {
        $changedFields[] = 'name';
    }

    if (($validated['email'] ?? null) !== $originalEmail) {
        $changedFields[] = 'email';
    }

    $user->name = $validated['name'];
    $user->email = $validated['email'];

    // Reset email verification if changed
    if ($user->isDirty('email')) {
        $user->email_verified_at = null;
    }

    // Handle profile image
    if ($request->hasFile('profile_image')) {
        $imagePath = $this->storeProfileImage($request, $user->profile_image);

        if (! $imagePath) {
            return back()->withErrors([
                'profile_image' => 'The profile image failed to upload.'
            ])->withInput();
        }

        $user->profile_image = $imagePath;
        $changedFields[] = 'profile image';
    }

    if ($originalEmail !== $validated['email']) {
        $user->email_verified_at = null;
    }

    $user->save();

    if ($changedFields !== []) {
        $adminActivity->sellerProfileUpdated($user, $changedFields);
    }

    return Redirect::route('seller.profile')->with('success', 'Profile updated successfully.');
}

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
    public function buyerEdit(Request $request)
{
    return view('buyer.profile', [
        'user' => $request->user(),
    ]);
}

public function buyerUpdate(UpdateBuyerProfileRequest $request)
{
    $user = $request->user();

    $validated = $request->validated();

    $originalEmail = $user->email;

    if ($request->filled('password')) {
        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Current password is incorrect.'
            ])->withInput();
        }

        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
    }

    if ($request->hasFile('profile_image')) {
        $path = $this->storeProfileImage($request, $user->profile_image);

        if (! $path) {
            return back()->withErrors([
                'profile_image' => 'The profile image failed to upload.'
            ])->withInput();
        }

        $user->profile_image = $path;
    }

    $user->name = $validated['name'];
    $user->email = $validated['email'];
    $user->phone = $validated['phone'] ?? null;
    $user->address = $validated['address'] ?? null;

    if ($originalEmail !== $validated['email']) {
        $user->email_verified_at = null;
    }

    $user->save();

    if ($request->input('profile_context') === 'modal') {
        return Redirect::back()->with('success', 'Profile updated successfully.');
    }

    $redirectRoute = $request->routeIs('profile.*')
        ? 'profile.edit'
        : 'buyer.profile';

    return Redirect::route($redirectRoute)->with('success', 'Profile updated successfully.');
    }

}
