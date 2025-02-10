<?php
// filepath: /C:/Users/USER/Desktop/IOT Raincatch Basin/System/IotSytem/app/Http/Controllers/AccountController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
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