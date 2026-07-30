<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BantayEskwela')</title>
    @vite(['resources/css/app.css',  'resources/js/app.js'])
    @stack('styles')
</head>

<div class="wrapper d-flex">
    <aside id="lmsSidebar" class="lms-sidebar">
        <x-main.sidebar />
    </aside>

    <div class="lms-content flex-grow-1">
        <x-main.topbar />
        <main class="container-fluid py-4 px-4">
            @yield('content')
        </main>
    </div>
</div>

<x-alerts />
<x-ios-confirm />
@include('components.footer')
@stack('scripts')
<script>

    document.addEventListener('DOMContentLoaded',()=>{

        const toggle=document.getElementById('sidebarToggle');

        const sidebar=document.getElementById('lmsSidebar');

        if(toggle && sidebar){

            toggle.addEventListener('click',()=>{

                sidebar.classList.toggle('show');

            });

        }

    });

</script>

</body>

</html>


