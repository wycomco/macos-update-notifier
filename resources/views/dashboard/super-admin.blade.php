<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-yellow-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">
                    Super Admin Dashboard
                </h2>
                <p class="mt-2 text-slate-400">
                    System-wide overview and management. Welcome back, {{ auth()->user()->name }}!
                </p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.users.index') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-medium rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                    </svg>
                    Manage Users
                </a>
                <span class="text-sm text-slate-400">Last updated: {{ now()->format('M j, Y g:i A') }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-6 space-y-8">
            
            <!-- Primary Stats Grid -->
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
                            <span class="text-purple-400">System-wide total</span>
                        </div>
                    </div>
                </div>

                <!-- Total Admins -->
                <div class="group relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-500/20 to-emerald-600/20 rounded-2xl blur-xl group-hover:blur-2xl transition-all duration-300"></div>
                    <div class="relative bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6 hover:bg-white/20 transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-400">Total Admins</p>
                                <p class="text-3xl font-bold text-white mt-2">{{ $totalAdmins }}</p>
                            </div>
                            <div class="p-3 bg-green-500/20 rounded-xl">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-sm">
                            <span class="text-green-400">Active administrators</span>
                        </div>
                    </div>
                </div>

                <!-- Active Subscribers -->
                <div class="group relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-yellow-500/20 to-orange-600/20 rounded-2xl blur-xl group-hover:blur-2xl transition-all duration-300"></div>
                    <div class="relative bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6 hover:bg-white/20 transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-400">Active Subscribers</p>
                                <p class="text-3xl font-bold text-white mt-2">{{ $activeSubscribers }}</p>
                            </div>
                            <div class="p-3 bg-yellow-500/20 rounded-xl">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-sm">
                            <span class="text-yellow-400">{{ $subscriptionRate }}% subscription rate</span>
                        </div>
                    </div>
                </div>

                <!-- Unsubscribed -->
                <div class="group relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-red-500/20 to-pink-600/20 rounded-2xl blur-xl group-hover:blur-2xl transition-all duration-300"></div>
                    <div class="relative bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6 hover:bg-white/20 transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-400">Unsubscribed</p>
                                <p class="text-3xl font-bold text-white mt-2">{{ $unsubscribedCount }}</p>
                            </div>
                            <div class="p-3 bg-red-500/20 rounded-xl">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.707-10.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L9.414 11H13a1 1 0 100-2H9.414l1.293-1.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-sm">
                            <span class="text-red-400">Opt-out requests</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Secondary Stats and Quick Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- System Metrics -->
                <div class="group relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/10 to-purple-600/10 rounded-2xl blur-xl"></div>
                    <div class="relative bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6">
                        <h3 class="text-lg font-bold text-white mb-4">System Metrics</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-3 bg-white/10 rounded-xl">
                                <span class="text-slate-300">Subscription Rate</span>
                                <span class="text-xl font-bold text-purple-400">{{ $subscriptionRate }}%</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-white/10 rounded-xl">
                                <span class="text-slate-300">Avg Subscribers/Admin</span>
                                <span class="text-xl font-bold text-green-400">{{ $totalAdmins > 0 ? round($totalSubscribers / $totalAdmins, 1) : 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="lg:col-span-2 group relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-500/10 to-pink-600/10 rounded-2xl blur-xl"></div>
                    <div class="relative bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6">
                        <h3 class="text-lg font-bold text-white mb-6">Super Admin Actions</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <a href="{{ route('admin.users.index') }}" 
                               class="flex items-center gap-3 p-4 bg-white/10 rounded-xl border border-white/20 hover:bg-white/20 transition-all duration-300 group">
                                <div class="p-2 bg-purple-500/20 rounded-lg group-hover:bg-purple-500/30 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-purple-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-white font-medium">User Management</p>
                                    <p class="text-slate-400 text-sm">Manage all users</p>
                                </div>
                            </a>
                            
                            <a href="{{ route('subscribers.index') }}" 
                               class="flex items-center gap-3 p-4 bg-white/10 rounded-xl border border-white/20 hover:bg-white/20 transition-all duration-300 group">
                                <div class="p-2 bg-blue-500/20 rounded-lg group-hover:bg-blue-500/30 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-white font-medium">All Subscribers</p>
                                    <p class="text-slate-400 text-sm">View subscriber list</p>
                                </div>
                            </a>

                            <a href="{{ route('dashboard') }}" 
                               class="flex items-center gap-3 p-4 bg-white/10 rounded-xl border border-white/20 hover:bg-white/20 transition-all duration-300 group">
                                <div class="p-2 bg-green-500/20 rounded-lg group-hover:bg-green-500/30 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z" />
                                        <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-white font-medium">Analytics</p>
                                    <p class="text-slate-400 text-sm">View detailed stats</p>
                                </div>
                            </a>
                        </div>
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
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-500/20 text-blue-400 border border-blue-500/30">
                                                {{ $count }}
                                            </span>
                                            <span class="text-slate-400 text-sm">({{ $totalSubscribers > 0 ? round(($count / $totalSubscribers) * 100, 1) : 0 }}%)</span>
                                        </div>
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
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Admin Performance -->
                <div class="group relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-500/10 to-emerald-600/10 rounded-2xl blur-xl"></div>
                    <div class="relative bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 bg-green-500/20 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-white">Admin Performance</h3>
                        </div>
                        
                        @if($adminStats->count() > 0)
                            <div class="space-y-3">
                                @foreach($adminStats->take(5) as $admin)
                                    <div class="flex items-center gap-3 p-3 bg-white/5 rounded-xl border border-white/10">
                                        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center flex-shrink-0">
                                            <span class="text-white text-sm font-semibold">{{ substr($admin->name, 0, 1) }}</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-white font-medium">{{ $admin->name }}</p>
                                            <p class="text-slate-400 text-xs truncate">{{ $admin->email }}</p>
                                        </div>
                                        <div class="text-center">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-500/20 text-green-400 border border-green-500/30">
                                                {{ $admin->subscribers_count }}
                                            </span>
                                            <p class="text-slate-400 text-xs mt-1">subscribers</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="p-4 bg-slate-500/20 rounded-xl inline-block mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </div>
                                <p class="text-slate-400">No admin data available</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent System Activity -->
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-500/10 to-pink-600/10 rounded-2xl blur-xl"></div>
                <div class="relative bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 bg-purple-500/20 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-purple-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white">Recent System Activity</h3>
                    </div>
                    
                    @if($recentActions->count() > 0)
                        <div class="overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b border-white/10">
                                            <th class="text-left py-3 px-4 text-slate-400 font-medium text-sm">Action</th>
                                            <th class="text-left py-3 px-4 text-slate-400 font-medium text-sm">Subscriber</th>
                                            <th class="text-left py-3 px-4 text-slate-400 font-medium text-sm">Admin</th>
                                            <th class="text-left py-3 px-4 text-slate-400 font-medium text-sm">Time</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-white/10">
                                        @foreach($recentActions->take(10) as $action)
                                            <tr class="hover:bg-white/5 transition-colors">
                                                <td class="py-3 px-4">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-purple-500/20 text-purple-400 border border-purple-500/30">
                                                        {{ ucfirst(str_replace('_', ' ', $action->action)) }}
                                                    </span>
                                                </td>
                                                <td class="py-3 px-4 text-white text-sm">{{ $action->subscriber->email ?? 'Unknown' }}</td>
                                                <td class="py-3 px-4 text-slate-300 text-sm">{{ optional($action->subscriber->admin)->name ?? 'System' }}</td>
                                                <td class="py-3 px-4 text-slate-400 text-sm">{{ $action->created_at->diffForHumans() }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="p-4 bg-slate-500/20 rounded-xl inline-block mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <p class="text-slate-400">No recent activity</p>
                            <p class="text-sm text-slate-500 mt-1">System activity will appear here as users interact with the platform</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
