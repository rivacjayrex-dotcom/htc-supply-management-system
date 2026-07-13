<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Handle Profile Photo Upload
        if ($request->hasFile('profile_photo')) {
            $request->validate([
                'profile_photo' => ['nullable', 'image', 'max:2048'], // Max 2MB
            ]);

            // Delete old photo if it exists
            if ($request->user()->profile_photo) {
                Storage::disk('public')->delete($request->user()->profile_photo);
            }

            // Store the new photo
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $request->user()->profile_photo = $path;
        }

        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
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

        public function pendingUsers()
        {
            // Fetch all users where is_approved is 0 (false)
            // and exclude the SMO themselves so they don't see their own account
            $users = \App\Models\User::where('is_approved', false)
                        ->where('id', '!=', auth()->id())
                        ->get();

            return view('admin.pending-users', compact('users'));
        }

    public function approveUser($id) {
        $user = \App\Models\User::findOrFail($id);
        $user->update(['is_approved' => true]);

        // Notify the user via the internal system (they will see it when they log in)
        \App\Models\Notification::create([
            'user_id' => $user->id,
            'title' => 'Account Approved',
            'message' => 'Welcome to HTC Supply System! Your account is now active.',
            'icon' => 'check-circle',
            'type' => 'success'
        ]);

        return back()->with('success', "Access granted to {$user->name}.");
    }
}
