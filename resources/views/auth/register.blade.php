@extends('layouts.auth')

@section('title','Register')

@section('content')

    <div class="auth-wrapper">

        <div class="auth-card">

            <div class="auth-header">

                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="auth-logo-img">

                <h2 class="auth-title">
                    Create account
                </h2>

                <p class="auth-subtitle">
                    Get started to continue
                </p>

            </div>

            @if($errors->any())
                <div class="auth-error">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="auth-form">
                @csrf

                <div class="auth-group input-wrap">
                    <i class="bi bi-person input-icon"></i>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        class="ios-input with-icon @error('name') is-invalid @enderror"
                        placeholder="Full name"
                    >
                    @error('name')
                    <span class="ios-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="auth-group input-wrap">
                    <i class="bi bi-envelope input-icon"></i>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="ios-input with-icon @error('email') is-invalid @enderror"
                        placeholder="Email"
                    >
                    @error('email')
                    <span class="ios-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="auth-group input-wrap">
                    <i class="bi bi-lock input-icon"></i>
                    <input
                        type="password"
                        name="password"
                        class="ios-input with-icon @error('password') is-invalid @enderror"
                        placeholder="Password"
                    >
                    @error('password')
                    <span class="ios-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="auth-group input-wrap">
                    <i class="bi bi-shield-lock input-icon"></i>
                    <input
                        type="password"
                        name="password_confirmation"
                        class="ios-input with-icon"
                        placeholder="Confirm password"
                    >
                </div>

                <button type="submit" class="ios-button">
                    Create Account
                </button>

            </form>

            <div class="auth-divider">
                <span>or</span>
            </div>

            <a href="{{ route('google.redirect') }}" class="ios-google-btn">
                <i class="bi bi-google"></i>
                Continue with Google
            </a>

            <div class="auth-footer">
                <span>Already have an account?</span>
                <a href="{{ route('login') }}" class="auth-link">
                    Sign in
                </a>
            </div>

        </div>

    </div>

@endsection
