<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent">
                    Subscribers
                </h2>
                <p class="mt-2 text-slate-400">
                    Manage your macOS update notification subscribers
                </p>
            </div>
            <div class="flex items-center gap-4">
                @if(auth()->user()->isSuperAdmin())
                    <!-- View Mode Toggle for Super Admins -->
                    <div class="flex items-center bg-white/10 backdrop-blur-xl border border-white/20 rounded-xl p-1">
                        <a href="{{ route('subscribers.index', ['show_all' => false]) }}" 
                           class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ !$showAll ? 'bg-purple-500 text-white shadow-lg' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                            My Subscribers
                        </a>
                        <a href="{{ route('subscribers.index', ['show_all' => true]) }}" 
                           class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ $showAll ? 'bg-purple-500 text-white shadow-lg' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                            All Subscribers
                        </a>
                    </div>
                @endif
                <div class="flex items-center gap-3">
                    <a href="{{ route('subscribers.create') }}" 
                       class="inline-flex items-center px-6 py-3 rounded-xl bg-gradient-to-r from-purple-500 to-blue-600 hover:from-purple-600 hover:to-blue-700 text-white font-semibold shadow-lg hover:shadow-xl transition-all duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Add Subscriber
                    </a>
                    
                    <!-- Bulk Import Dropdown -->
                    <div class="relative z-50" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="inline-flex items-center px-6 py-3 rounded-xl bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold shadow-lg hover:shadow-xl transition-all duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                            Bulk Import
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-2" :class="{ 'rotate-180': open }" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             style="z-index: 9999;"
                             class="absolute right-0 mt-2 w-64 bg-white/10 backdrop-blur-xl border border-white/20 rounded-xl shadow-xl">
                            <div class="py-2">
                                <a href="{{ route('subscribers.import', ['method' => 'textarea']) }}" 
                                   class="flex items-center px-4 py-3 text-sm text-slate-200 hover:bg-white/10 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0v12h8V4H6z" clip-rule="evenodd" />
                                    </svg>
                                    <div>
                                        <div class="font-medium">Paste Email List</div>
                                        <div class="text-xs text-slate-400">Copy and paste emails</div>
                                    </div>
                                </a>
                                <a href="{{ route('subscribers.import', ['method' => 'csv']) }}" 
                                   class="flex items-center px-4 py-3 text-sm text-slate-200 hover:bg-white/10 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <div>
                                        <div class="font-medium">Upload CSV File</div>
                                        <div class="text-xs text-slate-400">Import from CSV file</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                
                <!-- Main Content -->
                <div class="lg:col-span-3">
                    @if(auth()->user()->isSuperAdmin())
                        <!-- View Mode Indicator -->
                        <div class="mb-6 flex items-center justify-between bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl p-4">
                            <div class="flex items-center gap-3">
                                @if($showAll)
                                    <div class="w-3 h-3 bg-blue-400 rounded-full"></div>
                                    <span class="text-slate-300 font-medium">Showing all subscribers from all admins</span>
                                @else
                                    <div class="w-3 h-3 bg-purple-400 rounded-full"></div>
                                    <span class="text-slate-300 font-medium">Showing only your subscribers</span>
                                @endif
                            </div>
                            <span class="text-slate-400 text-sm">{{ $totalSubscribers }} {{ $totalSubscribers === 1 ? 'subscriber' : 'subscribers' }}</span>
                        </div>
                    @endif
                    
                    @if($subscribers->count() > 0)
                        <!-- Subscribers Grid -->
                        <div class="space-y-4">
                            @foreach($subscribers as $subscriber)
                                <div class="group relative">
                                    <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-white/10 rounded-2xl blur-xl group-hover:blur-2xl transition-all duration-300"></div>
                                    <div class="relative bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6 hover:bg-white/20 transition-all duration-300">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-4">
                                                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-blue-600 rounded-xl flex items-center justify-center">
                                                    <span class="text-white font-bold text-lg">{{ substr($subscriber->email, 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <h3 class="text-lg font-semibold text-white">{{ $subscriber->email }}</h3>
                                                    <div class="flex items-center gap-4 mt-1">
                                                        @if($subscriber->isActive())
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-500/20 text-green-400 border border-green-500/30">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                                </svg>
                                                                Active
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-slate-500/20 text-slate-400 border border-slate-500/30">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.707-10.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L9.414 11H13a1 1 0 100-2H9.414l1.293-1.293z" clip-rule="evenodd" />
                                                                </svg>
                                                                Inactive
                                                            </span>
                                                        @endif
                                                        <span class="text-slate-400 text-sm">{{ $subscriber->days_to_install }} days to install</span>
                                                        @if(auth()->user()->isSuperAdmin() && $subscriber->admin)
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                                </svg>
                                                                {{ $subscriber->admin->name ?? $subscriber->admin->email }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('subscribers.show', $subscriber) }}" 
                                                   class="p-2 rounded-lg bg-blue-500/20 text-blue-400 hover:bg-blue-500/30 transition-colors"
                                                   title="View Details">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </a>
                                                <a href="{{ route('subscribers.edit', $subscriber) }}" 
                                                   class="p-2 rounded-lg bg-orange-500/20 text-orange-400 hover:bg-orange-500/30 transition-colors"
                                                   title="Edit">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                    </svg>
                                                </a>
                                                <form action="{{ route('subscribers.destroy', $subscriber) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            onclick="return confirm('Are you sure you want to delete this subscriber?')"
                                                            class="p-2 rounded-lg bg-red-500/20 text-red-400 hover:bg-red-500/30 transition-colors"
                                                            title="Delete">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd" />
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414L10 11.414l2.293-2.293a1 1 0 000-1.414z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        
                                        @if($subscriber->subscribed_versions && count($subscriber->subscribed_versions) > 0)
                                            <div class="mt-4 pt-4 border-t border-white/10">
                                                <p class="text-sm text-slate-400 mb-2">Subscribed Versions:</p>
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach($subscriber->subscribed_versions as $version)
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-purple-500/20 text-purple-400 border border-purple-500/30">
                                                            {{ $version }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if($subscribers->hasPages())
                            <div class="mt-8">
                                {{ $subscribers->links() }}
                            </div>
                        @endif
                    @else
                        <!-- Empty State -->
                        <div class="group relative">
                            <div class="absolute inset-0 bg-gradient-to-br from-slate-500/10 to-slate-600/10 rounded-2xl blur-xl"></div>
                            <div class="relative bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-12 text-center">
                                <div class="p-6 bg-slate-500/20 rounded-2xl inline-block mb-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-white mb-2">No subscribers found</h3>
                                <p class="text-slate-400 mb-6">Get started by adding your first subscriber to receive macOS update notifications.</p>
                                <a href="{{ route('subscribers.create') }}" 
                                   class="inline-flex items-center px-6 py-3 rounded-xl bg-gradient-to-r from-purple-500 to-blue-600 hover:from-purple-600 hover:to-blue-700 text-white font-semibold shadow-lg hover:shadow-xl transition-all duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                    Add your first subscriber
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    
                    <!-- Statistics Card -->
                    <div class="group relative z-10">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-purple-600/10 rounded-2xl blur-xl"></div>
                        <div class="relative bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="p-2 bg-blue-500/20 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-white">Statistics</h3>
                            </div>
                            
                            <div class="space-y-4">
                                <div class="text-center p-4 bg-white/10 rounded-xl">
                                    <div class="text-3xl font-bold text-white mb-1">{{ $totalSubscribers }}</div>
                                    <div class="text-sm text-slate-400">Total Subscribers</div>
                                </div>
                                
                                <div class="text-center p-4 bg-white/10 rounded-xl">
                                    <div class="text-3xl font-bold text-green-400 mb-1">
                                        {{ $subscribers->where('is_subscribed', true)->count() }}
                                    </div>
                                    <div class="text-sm text-slate-400">Active Subscribers</div>
                                </div>
                                
                                <div class="text-center p-4 bg-white/10 rounded-xl">
                                    <div class="text-3xl font-bold text-purple-400 mb-1">
                                        {{ $subscribers->where('created_at', '>=', now()->subDays(30))->count() ?? 0 }}
                                    </div>
                                    <div class="text-sm text-slate-400">This Month</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Latest Releases Card -->
                    <div class="group relative z-10">
                        <div class="absolute inset-0 bg-gradient-to-br from-green-500/10 to-emerald-600/10 rounded-2xl blur-xl"></div>
                        <div class="relative bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="p-2 bg-green-500/20 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-white">Latest Releases</h3>
                            </div>
                            
                            @if($latestReleases->count() > 0)
                                <div class="space-y-3">
                                    @foreach($latestReleases as $release)
                                        <div class="p-3 bg-white/10 rounded-xl border border-white/10">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <div class="font-semibold text-white">{{ $release->major_version }}</div>
                                                    <div class="text-sm text-slate-400 font-mono">{{ $release->version }}</div>
                                                </div>
                                                <div class="text-xs text-slate-500">
                                                    {{ $release->release_date->format('M j') }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <div class="p-3 bg-slate-500/20 rounded-xl inline-block mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <p class="text-slate-400 text-sm">No releases found</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
