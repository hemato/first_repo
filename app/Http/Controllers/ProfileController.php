<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'profile_image' => 'nullable|image|max:2048',
            'profile_description' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('profile_image')) {
            // Eski profil resmini sil
            if ($user->profile_image) {
                Storage::delete('public/' . $user->profile_image);
            }
            // Yeni resmi kaydet
            $data['profile_image'] = $request->file('profile_image')->store('profile_images', 'public');
        }

        $user->update($data);

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }
}
