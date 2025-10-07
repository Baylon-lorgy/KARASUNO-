<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/ForgotPassword', [
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Special handling for admin reset notification
        if ($request->input('email') === '2201105765@student.buksu.edu.ph') {
            try {
                Mail::raw('An admin password reset has been requested. Please check and process this request.', function($message) {
                    $message->to('2201105765@student.buksu.edu.ph')
                           ->subject('Admin Password Reset Request');
                });

                return back()->with('status', 'Reset notification sent to administrator.');
            } catch (\Exception $e) {
                throw ValidationException::withMessages([
                    'email' => ['Unable to send notification. Please try again later.'],
                ]);
            }
        }

        // Regular password reset flow
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status == Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }
}
