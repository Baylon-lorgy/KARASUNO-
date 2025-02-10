@extends('layouts.dashboardlayout')

@section('title', 'Profile account')

@section('content')


<section class="container mx-auto p-6 bg-white dark:bg-gray-800 shadow-lg rounded-lg text-center">
    <div class="card-body">
        <img src="https://scontent.fcgy1-3.fna.fbcdn.net/v/t39.30808-6/466458716_869630271997402_9083455242095747570_n.jpg?_nc_cat=109&ccb=1-7&_nc_sid=6ee11a&_nc_ohc=dNEe5EZGq5gQ7kNvgHUbXVU&_nc_oc=AdiK6xyn3f9rhAh5RUvNyPF_1lVwgpI7fvjAIku_rdZAAf_XeldRO7rpn7eGd9dBf75CPO4fsxIvpIIzPWkYGsNU&_nc_zt=23&_nc_ht=scontent.fcgy1-3.fna&_nc_gid=ACngSlc3OEVAckWpcnA64Qd&oh=00_AYAJ_1ftU1H9QIQAD22G7GW2wJbSDL3YrSKg33xbkkDVpA&oe=67ACD92D" alt="Logo" width="100" class="mb-4">
        <h4 class="font-weight-bolder text-black">Rainwater Catch Basin</h4>
        
        <form method="post" action="{{ route('profile.update') }}" class="mt-4">
            @csrf
            @method('patch')
            
            <div class="input-group input-group-outline mb-3">
                <label class="form-label">Username</label>
                <input type="text" id="username" name="username" class="form-control" 
                    value="{{ old('username', Auth::user()->username ?? Auth::user()->name) }}" required>
            </div>
            
            <div class="input-group input-group-outline mb-3">
                <label class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" 
                    value="{{ old('email', Auth::user()->email) }}" required>
            </div>
            
            <div class="input-group input-group-outline mb-3">
                <label class="form-label">Phone Number</label>
                <input type="text" id="phone_number" name="phone_number" class="form-control" 
                    value="{{ old('phone_number', Auth::user()->phone_number ?? '') }}">
            </div>
            
            <div class="input-group input-group-outline mb-3">
                <label class="form-label">Location</label>
                <input type="text" id="location" name="location" class="form-control" 
                    value="{{ old('location', Auth::user()->location ?? '') }}">
            </div>
            
            <div class="input-group input-group-outline mb-3">
                <label class="form-label">Language</label>
                <input type="text" id="language" name="language" class="form-control" 
                    value="{{ old('language', Auth::user()->language ?? 'English') }}">
            </div>
            
            <div class="input-group input-group-outline mb-3">
                <label class="form-label">New Password</label>
                <input type="password" id="password" name="password" class="form-control">
            </div>
            
            <div class="input-group input-group-outline mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
            </div>
            
            <div class="text-center">
                <button type="submit" class="btn btn-lg bg-gradient-dark w-100 mt-4 mb-0">Save</button>
            </div>
        </form>
        
        <div class="mt-6 border-top pt-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Delete Account</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Permanently delete your account.</p>
        </div>
        
        <form method="post" action="{{ route('account.destroy') }}" class="mt-4">
            @csrf
            @method('delete')
            
            <div class="input-group input-group-outline mb-3">
                <label class="form-label">Password</label>
                <input type="password" id="delete_password" name="password" class="form-control">
            </div>
            
            <div class="text-center">
                <button type="submit" class="btn btn-lg bg-gradient-danger w-100 mt-4 mb-0">Delete Account</button>
            </div>
        </form>
    </div>
</section>


@endsection