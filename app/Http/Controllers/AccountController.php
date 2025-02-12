<?php
// filepath: /C:/Users/USER/Desktop/IOT Raincatch Basin/System/IotSytem/app/Http/Controllers/AccountController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AccountController extends Controller
{
    public function edit()
    {
        return view('account', [
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'language' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = Auth::user();
        $user->username = $request->username;
        $user->email = $request->email;
        $user->phone_number = $request->phone_number;
        $user->location = $request->location;
        $user->language = $request->language;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request)
    {
        $user = Auth::user();

        // Perform account deletion logic here
        // For example, delete the user from the database
        $user->delete();

        // Log the user out
        Auth::logout();

        // Redirect to the home page or any other page
        return redirect('/')->with('status', 'Account deleted successfully.');
    }
}