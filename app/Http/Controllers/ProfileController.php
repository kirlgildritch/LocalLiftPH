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
    public function buyerEdit(Request $request)
{
    return view('buyer.profile', [
        'user' => $request->user(),
    ]);
}

public function buyerUpdate(Request $request)
{
    $user = $request->user();

    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:255',
        'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'current_password' => 'nullable|required_with:password',
        'password' => 'nullable|confirmed|min:6',
    ]);

    if ($request->filled('password')) {
        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Current password is incorrect.'
            ])->withInput();
        }

        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
    }

    if ($request->hasFile('profile_image')) {
        $path = $request->file('profile_image')->store('profile_images', 'public');
        $user->profile_image = $path;
    }

    $user->name = $request->name;
    $user->email = $request->email;
    $user->phone = $request->phone;
    $user->address = $request->address;

    $user->save();

    return back()->with('success', 'Profile updated successfully.');
    }

}
