<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->checkRateLimit();

        // First check if the user exists
        $user = User::where('email', $this->email)->first();
        
        if (!$user) {
            $this->incrementAttempts();
            throw ValidationException::withMessages([
                'error' => 'Invalid admin credentials. Please try again.',
            ]);
        }

        // Then attempt authentication
        if (!Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            $this->incrementAttempts();
            throw ValidationException::withMessages([
                'error' => 'Invalid admin credentials. Please try again.',
            ]);
        }

        $this->clearAttempts();
    }

    /**
     * Check if the login attempts are within limits.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function checkRateLimit(): void
    {
        $key = 'login_attempts_'.$this->throttleKey();
        $attempts = Cache::get($key, 0);

        if ($attempts >= 5) {
            $seconds = 60;
            event(new Lockout($this));

            throw ValidationException::withMessages([
                'error' => 'Too many login attempts. Please try again in ' . ceil($seconds / 60) . ' minutes.',
            ]);
        }
    }

    /**
     * Increment the login attempts for the user.
     */
    protected function incrementAttempts(): void
    {
        $key = 'login_attempts_'.$this->throttleKey();
        $attempts = Cache::get($key, 0);
        Cache::put($key, $attempts + 1, 60);
    }

    /**
     * Clear the login attempts for the user.
     */
    protected function clearAttempts(): void
    {
        Cache::forget('login_attempts_'.$this->throttleKey());
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('email')).'|'.$this->ip());
    }
}
