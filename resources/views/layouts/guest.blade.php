<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'macOS Update Notifier') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 text-white min-h-screen"
          style="font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;">
        
        <!-- Toast Notifications -->
        <x-toast />
        
        <!-- Floating particles for visual interest -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-1/4 left-1/4 w-2 h-2 bg-purple-400/30 rounded-full animate-pulse"></div>
            <div class="absolute top-3/4 right-1/4 w-1 h-1 bg-blue-400/40 rounded-full animate-pulse delay-700"></div>
            <div class="absolute top-1/2 left-3/4 w-3 h-3 bg-indigo-400/20 rounded-full animate-pulse delay-1000"></div>
        </div>

        <div class="relative isolate min-h-screen">
            <!-- Header Navigation -->
            <header class="relative z-20 border-b border-white/10 backdrop-blur-sm bg-white/5">
                <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
                    <a href="/" class="flex items-center gap-3 group">
                        <div class="p-2 rounded-xl bg-gradient-to-br from-purple-500 to-blue-600 shadow-lg group-hover:shadow-xl transition-all duration-300">
                            <x-application-logo class="w-6 h-6 text-white" />
                        </div>
                        <span class="text-lg font-bold tracking-tight bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent">
                            {{ config('app.name', 'macOS Update Notifier') }}
                        </span>
                    </a>
                    
                    <nav class="flex items-center gap-4">
                        @if (Route::has('register') && !Request::routeIs('register'))
                            <a href="{{ route('register') }}" 
                               class="px-6 py-2.5 rounded-lg bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-500 hover:to-blue-500 text-white font-semibold text-sm shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                                Register
                            </a>
                        @endif
                        @if (Route::has('login') && !Request::routeIs('login'))
                            <a href="{{ route('login') }}" 
                               class="px-6 py-2.5 rounded-lg border border-white/20 hover:border-white/40 bg-white/10 hover:bg-white/20 transition-all duration-300 font-medium text-sm backdrop-blur-sm">
                                Log in
                            </a>
                        @endif
                    </nav>
                </div>
            </header>

            <main class="relative z-10 flex items-center justify-center min-h-[calc(100vh-80px)] px-6 py-12">
                <div class="w-full max-w-md">
                    <div class="rounded-2xl border border-white/10 bg-gradient-to-br from-white/10 to-white/5 backdrop-blur-xl shadow-2xl p-8">
                        {{ $slot }}
                    </div>
                    
                    @if (Route::has('magic-link.form') && !Request::routeIs('magic-link.form'))
                        <div class="mt-6 text-center">
                            <p class="text-sm text-slate-400">
                                Prefer passwordless authentication? 
                                <a href="{{ route('magic-link.form') }}" 
                                   class="text-purple-400 hover:text-purple-300 underline transition-colors">
                                    Use a magic link
                                </a>
                            </p>
                        </div>
                    @endif
                </div>
            </main>
        </div>
    </body>
</html>
