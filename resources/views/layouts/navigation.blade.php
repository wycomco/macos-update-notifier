<nav class="relative z-20 border-b border-white/10 backdrop-blur-sm bg-white/5">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                    <div class="p-2 rounded-xl bg-gradient-to-br from-purple-500 to-blue-600 shadow-lg group-hover:shadow-xl transition-all duration-300">
                        <x-application-logo class="w-6 h-6 text-white" />
                    </div>
                    <span class="text-lg font-bold tracking-tight bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent">
                        {{ config('app.name', 'macOS Update Notifier') }}
                    </span>
                </a>
            </div>

            <!-- Navigation Links -->
            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                @auth
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center px-4 py-2 text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'text-purple-400 bg-purple-500/10 rounded-lg border border-purple-500/20' : 'text-slate-300 hover:text-white hover:bg-white/10 rounded-lg' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" />
                        </svg>
                        Dashboard
                    </a>
                    
                    <a href="{{ route('subscribers.index') }}" 
                       class="inline-flex items-center px-4 py-2 text-sm font-medium transition-colors {{ request()->routeIs('subscribers.*') ? 'text-purple-400 bg-purple-500/10 rounded-lg border border-purple-500/20' : 'text-slate-300 hover:text-white hover:bg-white/10 rounded-lg' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                        </svg>
                        Subscribers
                    </a>

                    @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('admin.users.index') }}" 
                           class="inline-flex items-center px-4 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.users.*') ? 'text-purple-400 bg-purple-500/10 rounded-lg border border-purple-500/20' : 'text-slate-300 hover:text-white hover:bg-white/10 rounded-lg' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                            </svg>
                            User Management
                        </a>
                    @endif
                @endauth
            </div>

            <!-- User Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                @auth
                    <div class="ml-3 relative" x-data="{ open: false }">
                        <div>
                            <button @click="open = ! open" 
                                    class="flex items-center gap-3 px-4 py-2 rounded-lg bg-white/10 border border-white/20 hover:bg-white/20 transition-all duration-300 backdrop-blur-sm">
                                <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-blue-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-semibold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                </div>
                                <div class="text-left">
                                    <div class="text-sm font-medium text-white">{{ auth()->user()->name }}</div>
                                    @if(auth()->user()->isSuperAdmin())
                                        <div class="text-xs text-yellow-400">Super Admin</div>
                                    @else
                                        <div class="text-xs text-blue-400">Admin</div>
                                    @endif
                                </div>
                                <svg class="w-4 h-4 text-slate-400 transition-transform" :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>

                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 z-50 mt-2 w-48 rounded-xl bg-white/20 backdrop-blur-xl border border-white/30 shadow-2xl"
                             style="display: none;">
                            <div class="py-2">
                                <a href="{{ route('profile.edit') }}" 
                                   class="flex items-center px-4 py-2 text-sm text-white hover:text-white hover:bg-white/20 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-3" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                    </svg>
                                    Profile
                                </a>
                                
                                <div class="border-t border-white/20 my-1"></div>
                                
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" 
                                            class="flex items-center w-full px-4 py-2 text-sm text-red-300 hover:text-red-200 hover:bg-red-500/20 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-3" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd" />
                                        </svg>
                                        Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex gap-4">
                        <a href="{{ route('magic-link.form') }}" 
                           class="inline-flex items-center px-6 py-2 rounded-lg border border-white/20 hover:border-white/40 bg-white/10 hover:bg-white/20 text-white font-medium transition-all duration-300 backdrop-blur-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 3a1 1 0 011 1v12a1 1 0 11-2 0V4a1 1 0 011-1zm7.707 3.293a1 1 0 010 1.414L9.414 9H17a1 1 0 110 2H9.414l1.293 1.293a1 1 0 01-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Sign In
                        </a>
                    </div>
                @endauth
            </div>

            <!-- Mobile menu button -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" 
                        class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-white hover:bg-white/10 transition-colors">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-white/10">
        <div class="pt-2 pb-3 space-y-1 bg-white/5 backdrop-blur-sm">
            @auth
                <a href="{{ route('dashboard') }}" 
                   class="block pl-3 pr-4 py-2 text-base font-medium {{ request()->routeIs('dashboard') ? 'text-purple-400 bg-purple-500/10 border-r-4 border-purple-500' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                    Dashboard
                </a>
                
                <a href="{{ route('subscribers.index') }}" 
                   class="block pl-3 pr-4 py-2 text-base font-medium {{ request()->routeIs('subscribers.*') ? 'text-purple-400 bg-purple-500/10 border-r-4 border-purple-500' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                    Subscribers
                </a>

                @if(auth()->user()->isSuperAdmin())
                    <a href="{{ route('admin.users.index') }}" 
                       class="block pl-3 pr-4 py-2 text-base font-medium {{ request()->routeIs('admin.users.*') ? 'text-purple-400 bg-purple-500/10 border-r-4 border-purple-500' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                        User Management
                    </a>
                @endif
            @endauth
        </div>

        @auth
            <div class="pt-4 pb-1 border-t border-white/10 bg-white/5">
                <div class="px-4">
                    <div class="font-medium text-base text-white">{{ auth()->user()->name }}</div>
                    <div class="font-medium text-sm text-slate-400">{{ auth()->user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <a href="{{ route('profile.edit') }}" 
                       class="block px-4 py-2 text-base font-medium text-slate-300 hover:text-white hover:bg-white/10">
                        Profile
                    </a>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" 
                                class="block w-full text-left px-4 py-2 text-base font-medium text-red-400 hover:text-red-300 hover:bg-red-500/10">
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        @endauth
    </div>
</nav>
