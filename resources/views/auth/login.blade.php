@extends('layouts.auth')

@section('title','Login')

@section('content')

    <div class="auth-wrapper">

        <div class="auth-card">

            <div class="auth-header">

                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="auth-logo-img">

                <h2 class="auth-title">
                    Welcome back
                </h2>

                <p class="auth-subtitle">
                    Sign in to continue
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

            <form method="POST" action="{{ route('login') }}" class="auth-form">
                @csrf

                <div class="auth-group">
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="ios-input @error('email') is-invalid @enderror"
                        placeholder="Email"
                    >
                    @error('email')
                    <span class="ios-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="auth-group">
                    <input
                        type="password"
                        name="password"
                        class="ios-input @error('password') is-invalid @enderror"
                        placeholder="Password"
                    >
                    @error('password')
                    <span class="ios-error">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="ios-button">
                    Continue
                </button>

            </form>

            <div class="auth-divider">
                <span>or</span>
            </div>

            <a href="{{ route('google.redirect') }}" class="ios-google-btn">
                <i class="bi bi-google sidebar-icon"></i>
                Continue with Google
            </a>

{{--            <div class="auth-footer">--}}
{{--                <span>Don’t have an account?</span>--}}
{{--                <a href="{{ route('register') }}" class="auth-link">--}}
{{--                    Sign up--}}
{{--                </a>--}}
{{--            </div>--}}

        </div>

    </div>

@endsection
