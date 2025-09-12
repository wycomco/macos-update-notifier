<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'macOS Update Notifier'))</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 min-h-screen text-white"
          style="font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;">
        
        <!-- Toast Notifications -->
        <x-toast />
        
        <!-- Floating particles for visual interest -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-1/4 left-1/4 w-2 h-2 bg-purple-400/30 rounded-full animate-pulse"></div>
            <div class="absolute top-3/4 right-1/4 w-1 h-1 bg-blue-400/40 rounded-full animate-pulse delay-700"></div>
            <div class="absolute top-1/2 left-3/4 w-3 h-3 bg-indigo-400/20 rounded-full animate-pulse delay-1000"></div>
        </div>

        <div class="min-h-screen flex flex-col relative z-10">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-gradient-to-r from-white/5 to-white/10 backdrop-blur-sm border-b border-white/10">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset
            @hasSection('header')
                <header class="bg-gradient-to-r from-white/5 to-white/10 backdrop-blur-sm border-b border-white/10">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        @yield('header')
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="flex-1">
                @isset($slot)
                    {{ $slot }}
                @endisset
                @hasSection('content')
                    <div class="py-8">
                        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                            @yield('content')
                        </div>
                    </div>
                @endif
            </main>
        </div>
    </body>
</html>
    </body>
</html>
