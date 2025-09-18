<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'macOS Update Notifier') }} - Stay Updated with macOS Releases</title>
        <meta name="description" content="Get timely notifications about new macOS updates. Stay ahead with automated alerts about the latest macOS releases and security updates.">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 min-h-screen flex flex-col"
          style="font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;">
        
        <!-- Toast Notifications -->
        <x-toast />
        
        <!-- Floating particles for visual interest -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-1/4 left-1/4 w-2 h-2 bg-purple-400/30 rounded-full animate-pulse"></div>
            <div class="absolute top-3/4 right-1/4 w-1 h-1 bg-blue-400/40 rounded-full animate-pulse delay-700"></div>
            <div class="absolute top-1/2 left-3/4 w-3 h-3 bg-indigo-400/20 rounded-full animate-pulse delay-1000"></div>
        </div>

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
                    @auth
                        <a href="{{ url('/dashboard') }}" 
                           class="px-6 py-2.5 rounded-lg border border-white/20 hover:border-white/40 bg-white/10 hover:bg-white/20 transition-all duration-300 font-medium text-sm backdrop-blur-sm">
                            Dashboard
                        </a>
                    @else
                        @if (Route::has('magic-link.form'))
                            <a href="{{ route('magic-link.form') }}" 
                               class="px-6 py-2.5 rounded-lg bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-500 hover:to-blue-500 text-white font-semibold text-sm shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                                Log in or Sign up
                            </a>
                        @endif
                    @endauth
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main class="relative z-10 flex-1 flex items-center">
            <div class="max-w-7xl mx-auto px-6 py-20 lg:py-32">
                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    <!-- Left Column: Content -->
                    <div class="space-y-8">
                        <!-- Badge -->
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm font-medium">
                            <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
                            Automated macOS update notifications
                        </div>

                        <!-- Headline -->
                        <div class="space-y-4">
                            <h1 class="text-4xl lg:text-6xl font-bold leading-tight">
                                <span class="bg-gradient-to-r from-white via-slate-200 to-slate-400 bg-clip-text text-transparent">
                                    Stay ahead of
                                </span>
                                <br>
                                <span class="bg-gradient-to-r from-purple-400 via-blue-400 to-indigo-400 bg-clip-text text-transparent">
                                    macOS releases
                                </span>
                            </h1>
                            <p class="text-xl text-slate-300 leading-relaxed max-w-2xl">
                                Monitor the official MacAdmins SOFA feed and automatically notify your users when it's time to install updates. Configure grace periods, track deadlines, and maintain fleet compliance effortlessly.
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4">
                            @if (Route::has('magic-link.form'))
                                <a href="{{ route('magic-link.form') }}" 
                                   class="inline-flex items-center justify-center px-8 py-4 rounded-xl bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-500 hover:to-blue-500 text-white font-semibold text-lg shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                                    Magic Link Sign Up / Login
                                    <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            @endif
                            <div class="flex gap-3">
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" 
                                       class="inline-flex items-center justify-center px-6 py-4 rounded-xl border border-white/20 hover:border-white/40 bg-white/10 hover:bg-white/20 text-white font-medium transition-all duration-300 backdrop-blur-sm">
                                        or Sign up with Email
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Features Grid -->
                        <div class="grid sm:grid-cols-2 gap-6 pt-8">
                            <div class="flex items-start gap-3">
                                <div class="p-2 rounded-lg bg-emerald-500/10 text-emerald-400 flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-white mb-1">Real-time Updates</h3>
                                    <p class="text-slate-400 text-sm">Daily checks of the SOFA feed for the latest macOS releases</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="p-2 rounded-lg bg-blue-500/10 text-blue-400 flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-white mb-1">Smart Scheduling</h3>
                                    <p class="text-slate-400 text-sm">Flexible grace periods and deadline management</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="p-2 rounded-lg bg-purple-500/10 text-purple-400 flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-white mb-1">Email Notifications</h3>
                                    <p class="text-slate-400 text-sm">Automated reminders with clear installation deadlines</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="p-2 rounded-lg bg-indigo-500/10 text-indigo-400 flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-white mb-1">Complete Dashboard</h3>
                                    <p class="text-slate-400 text-sm">Track subscriber status and compliance metrics</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Visual/Demo -->
                    <div class="relative">
                        <div class="relative rounded-2xl border border-white/10 bg-gradient-to-br from-white/10 to-white/5 backdrop-blur-xl p-8 shadow-2xl">
                            <!-- Mock Dashboard Preview -->
                            <div class="space-y-6">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded-xl bg-gradient-to-br from-emerald-500 to-green-600 shadow-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 text-white">
                                            <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm3.53 6.22a.75.75 0 0 1 0 1.06l-4.5 4.5a.75.75 0 0 1-1.06 0l-2-2a.75.75 0 1 1 1.06-1.06l1.47 1.47 3.97-3.97a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-white">How it works</h3>
                                        <p class="text-slate-400 text-sm">Automated compliance in three simple steps</p>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div class="flex gap-4 p-4 rounded-xl bg-white/5 border border-white/10">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gradient-to-r from-purple-500 to-blue-500 flex items-center justify-center text-white font-bold text-sm">1</div>
                                        <div>
                                            <p class="text-white font-medium">Monitor SOFA Feed</p>
                                            <p class="text-slate-400 text-sm">Daily checks for new macOS releases and security updates</p>
                                        </div>
                                    </div>

                                    <div class="flex gap-4 p-4 rounded-xl bg-white/5 border border-white/10">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gradient-to-r from-purple-500 to-blue-500 flex items-center justify-center text-white font-bold text-sm">2</div>
                                        <div>
                                            <p class="text-white font-medium">Smart Notifications</p>
                                            <p class="text-slate-400 text-sm">Personalized deadlines and gentle reminders for each user</p>
                                        </div>
                                    </div>

                                    <div class="flex gap-4 p-4 rounded-xl bg-white/5 border border-white/10">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gradient-to-r from-purple-500 to-blue-500 flex items-center justify-center text-white font-bold text-sm">3</div>
                                        <div>
                                            <p class="text-white font-medium">Track Compliance</p>
                                            <p class="text-slate-400 text-sm">Real-time dashboard showing fleet update status</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-6 p-4 rounded-xl bg-gradient-to-r from-emerald-500/10 to-blue-500/10 border border-emerald-500/20">
                                    <p class="text-emerald-400 text-sm font-medium flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                        Pro tip: Use magic links to sign in without passwords!
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Floating decoration -->
                        <div class="absolute -top-6 -right-6 w-12 h-12 bg-gradient-to-br from-purple-500 to-blue-600 rounded-full opacity-20 animate-pulse"></div>
                        <div class="absolute -bottom-4 -left-4 w-8 h-8 bg-gradient-to-br from-emerald-500 to-green-600 rounded-full opacity-30 animate-pulse delay-500"></div>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>
