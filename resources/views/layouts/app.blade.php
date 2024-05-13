<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} @yield('title')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>
<body class="min-h-screen font-sans antialiased bg-base-200/50 dark:bg-base-200">
{{-- NAVBAR mobile only --}}
<x-mary-nav sticky class="lg:hidden">
    <x-slot:brand>
        <div class="ml-5 pt-5"><x-application-logo class="h-3" /></div>
    </x-slot:brand>
    <x-slot:actions>
        <label for="main-drawer" class="lg:hidden mr-3">
            <x-mary-icon name="o-bars-3" class="cursor-pointer" />
        </label>
    </x-slot:actions>
</x-mary-nav>

{{-- MAIN --}}
<x-mary-main >
    {{-- SIDEBAR --}}
    <x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-100 lg:bg-inherit">

        {{-- BRAND --}}
        <div class="ml-5 pt-5"><a href="/"><x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" /></a></div>

        {{-- MENU --}}
        <livewire:layout.sidenavigation />

    </x-slot:sidebar>

    {{-- The `$slot` goes here --}}
    <x-slot:content>
        {{ $slot }}
    </x-slot:content>
</x-mary-main>

{{-- Toast --}}
<x-mary-toast />
</body>
</html>
