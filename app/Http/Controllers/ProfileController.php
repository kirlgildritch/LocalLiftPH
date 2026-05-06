<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\AdminActivityNotification;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
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
   public function update(ProfileUpdateRequest $request): RedirectResponse
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
        $this->notifyAdmins(
            new AdminActivityNotification(
                'seller_review',
                'Seller profile updated',
                ($user->name ?? 'A seller') . ' updated their seller profile: ' . $this->formatFieldList($changedFields) . '.',
                'admin.sellers',
            )
        );
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

public function buyerUpdate(Request $request)
{
    $user = $request->user();

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:255',
        'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'current_password' => 'nullable|required_with:password',
        'password' => 'nullable|confirmed|min:8',
    ]);

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

    return back()->with('success', 'Profile updated successfully.');
    }

    private function notifyAdmins(AdminActivityNotification $notification): void
    {
        User::query()
            ->where(function ($query) {
                $query->where('is_admin', true)
                    ->orWhere('role', 'admin');
            })
            ->get()
            ->each
            ->notify($notification);
    }

    private function formatFieldList(array $fields): string
    {
        $fields = array_values(array_unique($fields));
        $count = count($fields);

        if ($count === 0) {
            return 'details';
        }

        if ($count === 1) {
            return $fields[0];
        }

        if ($count === 2) {
            return $fields[0] . ' and ' . $fields[1];
        }

        $lastField = array_pop($fields);

        return implode(', ', $fields) . ', and ' . $lastField;
    }

}
