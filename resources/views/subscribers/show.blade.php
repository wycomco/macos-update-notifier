<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent">
                    Subscriber Details
                </h2>
                <p class="mt-2 text-slate-400">
                    View and manage subscriber information
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('subscribers.edit', $subscriber) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-orange-600 to-yellow-600 hover:from-orange-700 hover:to-yellow-700 text-white font-medium rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                    Edit
                </a>
                <form action="{{ route('subscribers.destroy', $subscriber) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white font-medium rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl"
                            onclick="return confirm('Are you sure you want to delete this subscriber?')">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-6 space-y-8">
            
            <!-- Admin Ownership Information (Super Admin Only) -->
            @if(auth()->user()->isSuperAdmin() && $subscriber->admin)
                <div class="group relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-yellow-500/10 to-orange-600/10 rounded-2xl blur-xl"></div>
                    <div class="relative bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6">
                        
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 bg-yellow-500/20 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-white">Administrator Ownership</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="flex items-center gap-4 p-4 bg-white/5 rounded-xl border border-white/10">
                                <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-white text-lg font-bold">{{ substr($subscriber->admin->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="text-white font-medium">{{ $subscriber->admin->name }}</p>
                                    <p class="text-slate-400 text-sm">Administrator</p>
                                </div>
                            </div>
                            
                            <div class="p-4 bg-white/5 rounded-xl border border-white/10">
                                <label class="text-slate-400 text-sm block mb-1">Admin Email</label>
                                <span class="text-white font-medium">{{ $subscriber->admin->email }}</span>
                            </div>
                            
                            <div class="p-4 bg-white/5 rounded-xl border border-white/10">
                                <label class="text-slate-400 text-sm block mb-1">Subscriber Count</label>
                                <span class="text-white font-medium">{{ $subscriber->admin->subscribers()->count() }} subscribers</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Main Subscriber Information -->
                <div class="lg:col-span-2 group relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-purple-600/10 rounded-2xl blur-xl"></div>
                    <div class="relative bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6">
                        
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 bg-blue-500/20 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white">Subscriber Information</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <!-- Contact Information -->
                            <div class="space-y-4">
                                <h4 class="text-lg font-semibold text-purple-400 mb-4">Contact Information</h4>
                                
                                <div class="p-4 bg-white/5 rounded-xl border border-white/10">
                                    <label class="text-slate-400 text-sm block mb-1">Email Address</label>
                                    <span class="text-white font-medium">{{ $subscriber->email }}</span>
                                </div>
                                
                                <div class="p-4 bg-white/5 rounded-xl border border-white/10">
                                    <label class="text-slate-400 text-sm block mb-1">Preferred Language</label>
                                    <span class="text-white font-medium">
                                        {{ $subscriber->getLanguageFlag() }} {{ $subscriber->getLanguageDisplayName() }}
                                    </span>
                                </div>
                                
                                <div class="p-4 bg-white/5 rounded-xl border border-white/10">
                                    <label class="text-slate-400 text-sm block mb-1">Member Since</label>
                                    <span class="text-white font-medium">{{ $subscriber->created_at->format('F j, Y') }}</span>
                                </div>
                                
                                <div class="p-4 bg-white/5 rounded-xl border border-white/10">
                                    <label class="text-slate-400 text-sm block mb-1">Last Updated</label>
                                    <span class="text-white font-medium">{{ $subscriber->updated_at->format('F j, Y') }}</span>
                                </div>
                            </div>

                            <!-- Subscription Settings -->
                            <div class="space-y-4">
                                <h4 class="text-lg font-semibold text-green-400 mb-4">Subscription Settings</h4>
                                
                                <div class="p-4 bg-white/5 rounded-xl border border-white/10">
                                    <label class="text-slate-400 text-sm block mb-1">Days to Install</label>
                                    <span class="text-white font-medium">{{ $subscriber->days_to_install }} days</span>
                                </div>
                                
                                <div class="p-4 bg-white/5 rounded-xl border border-white/10">
                                    <label class="text-slate-400 text-sm block mb-3">Subscribed Versions</label>
                                    <div class="flex flex-wrap gap-2">
                                        @forelse($subscriber->subscribed_versions as $version)
                                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium bg-blue-500/20 text-blue-400 border border-blue-500/30">
                                                {{ $version }}
                                            </span>
                                        @empty
                                            <span class="text-slate-400 text-sm">No versions selected</span>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Subscription Status -->
                <div class="lg:col-span-2 group relative mb-8">
                    <div class="absolute inset-0 bg-gradient-to-br from-{{ $subscriber->isActive() ? 'green' : 'red' }}-500/10 to-{{ $subscriber->isActive() ? 'emerald' : 'pink' }}-600/10 rounded-2xl blur-xl"></div>
                    <div class="relative bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6">
                        
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-{{ $subscriber->isActive() ? 'green' : 'red' }}-500/20 rounded-lg">
                                    @if($subscriber->isActive())
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                </div>
                                <h3 class="text-xl font-bold text-white">Subscription Status</h3>
                            </div>
                            
                            @if(!$subscriber->isActive())
                                <!-- Re-enable Button -->
                                <a href="{{ route('subscribers.resubscribe.form', $subscriber) }}" 
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-medium rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                                    </svg>
                                    Re-enable Subscription
                                </a>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="p-4 bg-white/5 rounded-xl border border-white/10">
                                <label class="text-slate-400 text-sm block mb-1">Current Status</label>
                                @if($subscriber->isActive())
                                    <span class="inline-flex items-center gap-2 text-green-400 font-medium">
                                        <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                                        Active - Receiving Notifications
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-2 text-red-400 font-medium">
                                        <div class="w-2 h-2 bg-red-400 rounded-full"></div>
                                        Unsubscribed
                                    </span>
                                @endif
                            </div>
                            
                            @if($subscriber->unsubscribed_at)
                                <div class="p-4 bg-white/5 rounded-xl border border-white/10">
                                    <label class="text-slate-400 text-sm block mb-1">Unsubscribed On</label>
                                    <span class="text-white font-medium">{{ $subscriber->unsubscribed_at->format('F j, Y \a\t g:i A') }}</span>
                                </div>
                            @endif
                        </div>

                        @if(!$subscriber->isActive())
                            <div class="mt-6 p-4 bg-yellow-500/10 rounded-xl border border-yellow-500/20">
                                <div class="flex items-start gap-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-yellow-400 mt-0.5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    <div>
                                        <h4 class="text-yellow-400 font-semibold mb-1">Unsubscribed User</h4>
                                        <p class="text-yellow-200 text-sm">
                                            This subscriber has unsubscribed and is not receiving notifications. 
                                            Only re-enable their subscription if you have obtained explicit consent from them to do so.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Related Releases -->
                <div class="group relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-500/10 to-emerald-600/10 rounded-2xl blur-xl"></div>
                    <div class="relative bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6">
                        
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 bg-green-500/20 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-white">Related Releases</h3>
                        </div>
                        
                        @if($relatedReleases->count() > 0)
                            <div class="space-y-3">
                                @foreach($relatedReleases as $release)
                                    @php
                                        $deadline = $release->getDeadlineDate($subscriber->days_to_install);
                                        $isOverdue = $deadline->isPast();
                                        $isUpcoming = $deadline->diffInDays(null, false) <= 2 && !$isOverdue;
                                    @endphp
                                    <div class="p-4 bg-white/5 rounded-xl border border-white/10 hover:bg-white/10 transition-colors">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h4 class="text-white font-semibold">{{ $release->major_version }}</h4>
                                                <p class="text-slate-400 text-sm">{{ $release->version }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-slate-400 text-sm mb-1">{{ $release->release_date->format('M j') }}</p>
                                                @if($isOverdue)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-red-500/20 text-red-400 border border-red-500/30">
                                                        Overdue
                                                    </span>
                                                @elseif($isUpcoming)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-orange-500/20 text-orange-400 border border-orange-500/30">
                                                        Due Soon
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-green-500/20 text-green-400 border border-green-500/30">
                                                        On Track
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="p-4 bg-slate-500/20 rounded-xl inline-block mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <p class="text-slate-400">No releases found for subscribed versions</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <div class="mt-8">
                <a href="{{ route('subscribers.index') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-medium rounded-xl transition-all duration-300 backdrop-blur-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Back to Subscribers
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
