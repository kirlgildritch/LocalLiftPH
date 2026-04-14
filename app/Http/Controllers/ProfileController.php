<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
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
   public function update(ProfileUpdateRequest $request): RedirectResponse
{
    $user = $request->user();

    $validated = $request->validated();

    // Check current password first before changing anything
    if ($request->filled('password')) {
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Current password is incorrect.'
            ])->withInput();
        }

        $user->password = Hash::make($request->password);
    }

    // Update only basic fields
    $user->name = $validated['name'];
    $user->email = $validated['email'];

    // Reset email verification if changed
    if ($user->isDirty('email')) {
        $user->email_verified_at = null;
    }

    // Handle profile image
    if ($request->hasFile('profile_image')) {
        if (! $request->file('profile_image')->isValid()) {
            return back()->withErrors([
                'profile_image' => 'The profile image failed to upload.'
            ])->withInput();
        }

        $imagePath = $request->file('profile_image')->store('profile_images', 'public');
        $user->profile_image = $imagePath;
    }

    $user->save();

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
}
