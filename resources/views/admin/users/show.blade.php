<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent">
                    User Details: {{ $user->name ?? $user->email }}
                </h2>
                <p class="mt-2 text-slate-400">
                    User management and subscriber overview
                </p>
            </div>
            <a href="{{ route('admin.users.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-medium rounded-xl transition-all duration-300 backdrop-blur-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Back to Users
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-6">
            
            @if (session('success'))
                <div class="mb-6 group relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-500/20 to-emerald-600/20 rounded-2xl blur-xl"></div>
                    <div class="relative bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-green-500/20 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <span class="text-green-400 font-medium">{{ session('success') }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- User Info -->
                <div class="group relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-500/10 to-pink-600/10 rounded-2xl blur-xl"></div>
                    <div class="relative bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6">
                        
                        <!-- User Avatar and Basic Info -->
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                <span class="text-white text-xl font-bold">{{ strtoupper(substr($user->name ?? $user->email, 0, 1)) }}</span>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white">{{ $user->name ?? 'N/A' }}</h3>
                                <p class="text-slate-400">{{ $user->email }}</p>
                                @if($user->is_super_admin)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-yellow-500/20 to-orange-500/20 text-yellow-400 border border-yellow-500/30 mt-2">
                                        Super Admin
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-slate-500/20 text-slate-400 border border-slate-500/30 mt-2">
                                        Admin
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- User Details -->
                        <div class="space-y-4">
                            <div class="p-3 bg-white/5 rounded-xl border border-white/10">
                                <label class="text-slate-400 text-sm block mb-1">Email Verified</label>
                                @if($user->email_verified_at)
                                    <div class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-green-400 font-medium">{{ $user->email_verified_at->format('M j, Y g:i A') }}</span>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-red-400 font-medium">Not verified</span>
                                    </div>
                                @endif
                            </div>

                            <div class="p-3 bg-white/5 rounded-xl border border-white/10">
                                <label class="text-slate-400 text-sm block mb-1">Last Login</label>
                                <span class="text-white font-medium">
                                    @if($user->last_login_at)
                                        {{ $user->last_login_at->diffForHumans() }}
                                    @else
                                        Never
                                    @endif
                                </span>
                            </div>

                            <div class="p-3 bg-white/5 rounded-xl border border-white/10">
                                <label class="text-slate-400 text-sm block mb-1">Member Since</label>
                                <span class="text-white font-medium">{{ $user->created_at->format('M j, Y g:i A') }}</span>
                            </div>

                            <div class="p-3 bg-white/5 rounded-xl border border-white/10">
                                <label class="text-slate-400 text-sm block mb-1">Last Updated</label>
                                <span class="text-white font-medium">{{ $user->updated_at->format('M j, Y g:i A') }}</span>
                            </div>
                        </div>

                        @if($user->id !== Auth::id())
                            <div class="mt-6">
                                @if(!$user->is_super_admin)
                                    <form method="POST" action="{{ route('admin.users.promote', $user) }}">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-medium rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl"
                                                onclick="return confirm('Promote {{ $user->email }} to super admin?')">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 17a.75.75 0 01-.75-.75V5.612L5.29 9.77a.75.75 0 01-1.08-1.04l5.25-5.5a.75.75 0 011.08 0l5.25 5.5a.75.75 0 11-1.08 1.04l-3.96-4.158V16.25A.75.75 0 0110 17z" clip-rule="evenodd" />
                                            </svg>
                                            Promote to Super Admin
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.users.demote', $user) }}">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 text-white font-medium rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl"
                                                onclick="return confirm('Demote {{ $user->email }} from super admin?')">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v10.638l3.96-4.158a.75.75 0 111.08 1.04l-5.25 5.5a.75.75 0 01-1.08 0l-5.25-5.5a.75.75 0 111.08-1.04l3.96 4.158V3.75A.75.75 0 0110 3z" clip-rule="evenodd" />
                                            </svg>
                                            Demote from Super Admin
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Subscribers -->
                <div class="lg:col-span-2 group relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-purple-600/10 rounded-2xl blur-xl"></div>
                    <div class="relative bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6">
                        
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-blue-500/20 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-white">Managed Subscribers ({{ $user->subscribers->count() }})</h3>
                            </div>
                            @if($user->subscribers->count() > 0)
                                <a href="{{ route('subscribers.index', ['admin' => $user->id]) }}" 
                                   class="inline-flex items-center gap-2 px-3 py-2 bg-blue-500/20 hover:bg-blue-500/30 border border-blue-500/30 text-blue-400 font-medium rounded-lg transition-all duration-300">
                                    View all
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            @endif
                        </div>

                        @if($user->subscribers->count() > 0)
                            <div class="overflow-hidden rounded-xl border border-white/10">
                                <div class="overflow-x-auto">
                                    <table class="w-full">
                                        <thead>
                                            <tr class="border-b border-white/10 bg-white/5">
                                                <th class="text-left py-3 px-4 text-slate-400 font-medium text-sm">Email</th>
                                                <th class="text-left py-3 px-4 text-slate-400 font-medium text-sm">macOS Version</th>
                                                <th class="text-left py-3 px-4 text-slate-400 font-medium text-sm">Status</th>
                                                <th class="text-left py-3 px-4 text-slate-400 font-medium text-sm">Added</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-white/10">
                                            @foreach($user->subscribers->take(10) as $subscriber)
                                                <tr class="hover:bg-white/5 transition-colors">
                                                    <td class="py-3 px-4 text-white">{{ $subscriber->email }}</td>
                                                    <td class="py-3 px-4 text-slate-300">
                                                        @if($subscriber->subscribed_versions && count($subscriber->subscribed_versions) > 0)
                                                            {{ implode(', ', $subscriber->subscribed_versions) }}
                                                        @else
                                                            <span class="text-slate-500">Not specified</span>
                                                        @endif
                                                    </td>
                                                    <td class="py-3 px-4">
                                                        @if($subscriber->is_subscribed)
                                                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-green-500/20 text-green-400 border border-green-500/30">
                                                                Active
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-red-500/20 text-red-400 border border-red-500/30">
                                                                Unsubscribed
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="py-3 px-4 text-slate-400">{{ $subscriber->created_at->diffForHumans() }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            @if($user->subscribers->count() > 10)
                                <div class="text-center mt-4 p-3 bg-white/5 rounded-xl">
                                    <p class="text-slate-400">
                                        Showing 10 of {{ $user->subscribers->count() }} subscribers
                                    </p>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-12">
                                <div class="p-4 bg-slate-500/20 rounded-xl inline-block mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                    </svg>
                                </div>
                                <p class="text-slate-400 text-lg">This user is not managing any subscribers yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            @if($recentActions->count() > 0)
                <div class="mt-8 group relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-500/10 to-emerald-600/10 rounded-2xl blur-xl"></div>
                    <div class="relative bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 bg-green-500/20 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white">Recent Activity</h3>
                        </div>
                        
                        <div class="space-y-4">
                            @foreach($recentActions as $action)
                                <div class="flex items-start gap-4 p-4 bg-white/5 rounded-xl border border-white/10 hover:bg-white/10 transition-colors">
                                    <div class="flex-shrink-0 p-2 rounded-lg
                                        @if($action->action === 'subscribed') bg-green-500/20
                                        @elseif($action->action === 'unsubscribed') bg-red-500/20
                                        @elseif($action->action === 'version_changed') bg-blue-500/20
                                        @else bg-slate-500/20
                                        @endif">
                                        @if($action->action === 'subscribed')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                            </svg>
                                        @elseif($action->action === 'unsubscribed')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        @elseif($action->action === 'version_changed')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 100-2 1 1 0 000 2zM14 9a1 1 0 11-2 0 1 1 0 012 0zm-7 3a1 1 0 011-1h6a1 1 0 110 2H8a1 1 0 01-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-white font-medium">
                                            <span class="text-purple-400">{{ $action->subscriber->email }}</span>
                                            @if($action->action === 'subscribed')
                                                <span class="text-slate-300">subscribed to updates</span>
                                            @elseif($action->action === 'unsubscribed')
                                                <span class="text-slate-300">unsubscribed from updates</span>
                                            @elseif($action->action === 'version_changed')
                                                <span class="text-slate-300">changed macOS version to</span>
                                                <span class="text-blue-400">{{ $action->data['new_version'] ?? 'unknown' }}</span>
                                            @else
                                                <span class="text-slate-300">{{ $action->action }}</span>
                                            @endif
                                        </p>
                                        <p class="text-slate-400 text-sm mt-1">{{ $action->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
