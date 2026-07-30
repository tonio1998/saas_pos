<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>

    @vite(['resources/css/app.css','resources/js/app.js'])
    @include('theme')
    @include('pages.schools.scanner.style')
</head>
<body>

<div class="container-flui">
    <main class="col-md-12 ms-sm-auto">
        @yield('content')
    </main>
</div>

<x-alerts />

@include('components.footer')
@include('pages.schools.scanner.script')
@stack('scripts')
<script>
    window.analyticsRoutes = {
        overview: "{{ route('sa.platform-analytics.overview-data') }}",
        login: "{{ route('sa.platform-analytics.login-trends') }}",
        security: "{{ route('sa.platform-analytics.security-trends') }}",
        device: "{{ route('sa.platform-analytics.device-analytics') }}",
        school: "{{ route('sa.platform-analytics.school-analytics') }}"
    }
</script>
</body>
</html>
