<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RegisterController extends Controller
{
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/@gmail\.com$/', $value)) {
                        $fail('Only Gmail addresses are allowed.');
                    }
                },
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }
    
}