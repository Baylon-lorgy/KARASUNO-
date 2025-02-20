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
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
            'mobile' => 'nullable|string|max:20', // Ensure this allows updates
            'location' => 'nullable|string|max:255',
        ]);
        

        $user = Auth::user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        
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