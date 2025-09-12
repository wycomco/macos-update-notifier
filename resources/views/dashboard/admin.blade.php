<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent">
                    Admin Dashboard
                </h2>
                <p class="mt-2 text-slate-400">
                    Welcome back, {{ auth()->user()->name }}! Here's your subscriber management overview.
                </p>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm text-slate-400">Last updated:</span>
                <span class="text-sm font-medium text-purple-400">{{ now()->format('M j, Y g:i A') }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-6 space-y-8">
            
            <!-- Quick Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Subscribers -->
                <div class="group relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/20 to-purple-600/20 rounded-2xl blur-xl group-hover:blur-2xl transition-all duration-300"></div>
                    <div class="relative bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6 hover:bg-white/20 transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-400">Total Subscribers</p>
                                <p class="text-3xl font-bold text-white mt-2">{{ $totalSubscribers }}</p>
                            </div>
                            <div class="p-3 bg-blue-500/20 rounded-xl">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-sm">
                            <span class="text-green-400">+{{ $totalSubscribers > 0 ? 1 : 0 }}</span>
                            <span class="text-slate-400 ml-1">this week</span>
                        </div>
                    </div>
                </div>

                <!-- Active Subscribers -->
                <div class="group relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-500/20 to-emerald-600/20 rounded-2xl blur-xl group-hover:blur-2xl transition-all duration-300"></div>
                    <div class="relative bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6 hover:bg-white/20 transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-400">Active Subscribers</p>
                                <p class="text-3xl font-bold text-white mt-2">{{ $activeSubscribers }}</p>
                            </div>
                            <div class="p-3 bg-green-500/20 rounded-xl">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-sm">
                            <span class="text-slate-400">{{ $subscriptionRate }}% subscription rate</span>
                        </div>
                    </div>
                </div>

                <!-- Unsubscribed -->
                <div class="group relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-orange-500/20 to-red-600/20 rounded-2xl blur-xl group-hover:blur-2xl transition-all duration-300"></div>
                    <div class="relative bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6 hover:bg-white/20 transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-400">Unsubscribed</p>
                                <p class="text-3xl font-bold text-white mt-2">{{ $unsubscribedCount }}</p>
                            </div>
                            <div class="p-3 bg-orange-500/20 rounded-xl">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-orange-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.707-10.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L9.414 11H13a1 1 0 100-2H9.414l1.293-1.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-sm">
                            <span class="text-slate-400">{{ $unsubscribedCount > 0 ? 'Recent unsubscribes' : 'No unsubscribes' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Subscription Rate -->
                <div class="group relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-500/20 to-pink-600/20 rounded-2xl blur-xl group-hover:blur-2xl transition-all duration-300"></div>
                    <div class="relative bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6 hover:bg-white/20 transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-400">Subscription Rate</p>
                                <p class="text-3xl font-bold text-white mt-2">{{ $subscriptionRate }}%</p>
                            </div>
                            <div class="p-3 bg-purple-500/20 rounded-xl">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-purple-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-sm">
                            <span class="text-slate-400">{{ $activeSubscribers }}/{{ $totalSubscribers }} active</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/10 to-purple-600/10 rounded-2xl blur-xl"></div>
                <div class="relative bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6">
                    <h3 class="text-xl font-bold text-white mb-6">Quick Actions</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <a href="{{ route('subscribers.create') }}" 
                           class="flex items-center gap-3 p-4 bg-white/10 rounded-xl border border-white/20 hover:bg-white/20 transition-all duration-300 group">
                            <div class="p-2 bg-blue-500/20 rounded-lg group-hover:bg-blue-500/30 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-white font-medium">Add Subscriber</p>
                                <p class="text-slate-400 text-sm">Create new subscriber</p>
                            </div>
                        </a>
                        
                        <a href="{{ route('subscribers.index') }}" 
                           class="flex items-center gap-3 p-4 bg-white/10 rounded-xl border border-white/20 hover:bg-white/20 transition-all duration-300 group">
                            <div class="p-2 bg-green-500/20 rounded-lg group-hover:bg-green-500/30 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-white font-medium">Manage Subscribers</p>
                                <p class="text-slate-400 text-sm">View and edit all</p>
                            </div>
                        </a>

                        @if($totalSubscribers > 0)
                            <a href="{{ route('subscribers.index') }}?filter=active" 
                               class="flex items-center gap-3 p-4 bg-white/10 rounded-xl border border-white/20 hover:bg-white/20 transition-all duration-300 group">
                                <div class="p-2 bg-purple-500/20 rounded-lg group-hover:bg-purple-500/30 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-purple-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-white font-medium">View Active</p>
                                    <p class="text-slate-400 text-sm">Filter active only</p>
                                </div>
                            </a>
                        @endif

                        <a href="{{ route('profile.edit') }}" 
                           class="flex items-center gap-3 p-4 bg-white/10 rounded-xl border border-white/20 hover:bg-white/20 transition-all duration-300 group">
                            <div class="p-2 bg-orange-500/20 rounded-lg group-hover:bg-orange-500/30 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-orange-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-white font-medium">Profile Settings</p>
                                <p class="text-slate-400 text-sm">Update your profile</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- macOS Version Distribution -->
                <div class="group relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-purple-600/10 rounded-2xl blur-xl"></div>
                    <div class="relative bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 bg-blue-500/20 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z" />
                                    <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-white">macOS Version Distribution</h3>
                        </div>
                        
                        @if(!empty($versionStats))
                            <div class="space-y-3">
                                @foreach($versionStats as $version => $count)
                                    <div class="flex items-center justify-between p-3 bg-white/10 rounded-xl border border-white/10">
                                        <span class="text-white font-medium">{{ $version ?: 'Unknown' }}</span>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-500/20 text-blue-400 border border-blue-500/30">
                                            {{ $count }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="p-4 bg-slate-500/20 rounded-xl inline-block mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z" />
                                        <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z" />
                                    </svg>
                                </div>
                                <p class="text-slate-400">No version data available</p>
                                <p class="text-sm text-slate-500 mt-1">Version stats will appear when subscribers are added</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="group relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-500/10 to-emerald-600/10 rounded-2xl blur-xl"></div>
                    <div class="relative bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 bg-green-500/20 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-white">Recent Activity</h3>
                        </div>
                        
                        @if($recentActions->count() > 0)
                            <div class="space-y-3">
                                @foreach($recentActions as $action)
                                    <div class="flex items-center gap-3 p-3 bg-white/5 rounded-xl border border-white/10">
                                        <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center flex-shrink-0">
                                            <span class="text-white text-xs font-semibold">{{ substr($action->subscriber->email ?? 'U', 0, 1) }}</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-white text-sm font-medium">{{ ucfirst(str_replace('_', ' ', $action->action)) }}</p>
                                            <p class="text-slate-400 text-xs truncate">{{ $action->subscriber->email ?? 'Unknown subscriber' }}</p>
                                        </div>
                                        <div class="text-xs text-slate-500">
                                            {{ $action->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="p-4 bg-slate-500/20 rounded-xl inline-block mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <p class="text-slate-400">No recent activity</p>
                                <p class="text-sm text-slate-500 mt-1">Activity will appear as subscribers interact with the system</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
