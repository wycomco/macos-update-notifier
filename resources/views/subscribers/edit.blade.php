<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent">
                    Edit Subscriber
                </h2>
                <p class="mt-2 text-slate-400">
                    Update subscriber information and preferences
                </p>
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
                                    <p class="text-slate-400 text-sm">Responsible Administrator</p>
                                </div>
                            </div>
                            
                            <div class="p-4 bg-white/5 rounded-xl border border-white/10">
                                <label class="text-slate-400 text-sm block mb-1">Admin Email</label>
                                <span class="text-white font-medium">{{ $subscriber->admin->email }}</span>
                            </div>
                            
                            <div class="p-4 bg-white/5 rounded-xl border border-white/10">
                                <label class="text-slate-400 text-sm block mb-1">Total Subscribers</label>
                                <span class="text-white font-medium">{{ $subscriber->admin->subscribers()->count() }} subscribers</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-500/10 to-pink-600/10 rounded-2xl blur-xl"></div>
                <div class="relative bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-8">
                    
                    <div class="flex items-center gap-3 mb-8">
                        <div class="p-3 bg-purple-500/20 rounded-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-purple-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white">Subscriber Information</h3>
                    </div>

                    <form action="{{ route('subscribers.update', $subscriber) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <!-- Email Address -->
                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-medium text-slate-300">
                                Email Address
                            </label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $subscriber->email) }}" 
                                   required
                                   class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Subscribed macOS Versions -->
                        <div class="space-y-4">
                            <label class="block text-sm font-medium text-slate-300">
                                Subscribed macOS Versions
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                @foreach($availableVersions as $version)
                                    <label class="relative flex items-center p-3 bg-white/5 rounded-xl border border-white/10 hover:bg-white/10 transition-all duration-300 cursor-pointer group">
                                        <input type="checkbox" 
                                               name="subscribed_versions[]" 
                                               value="{{ $version }}" 
                                               {{ in_array($version, old('subscribed_versions', $subscriber->subscribed_versions ?? [])) ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-5 h-5 bg-white/10 border-2 border-white/30 rounded-lg peer-checked:bg-purple-500 peer-checked:border-purple-500 transition-all duration-300 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" 
                                                 class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity duration-300" 
                                                 viewBox="0 0 20 20" 
                                                 fill="currentColor">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <span class="ml-3 text-white font-medium group-hover:text-purple-300 transition-colors">
                                            {{ $version }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            @error('subscribed_versions')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Days to Install -->
                        <div class="space-y-2">
                            <label for="days_to_install" class="block text-sm font-medium text-slate-300">
                                Days to Install After Release
                            </label>
                            <div class="relative">
                                <input type="number" 
                                       id="days_to_install" 
                                       name="days_to_install" 
                                       value="{{ old('days_to_install', $subscriber->days_to_install) }}" 
                                       min="1" 
                                       max="365" 
                                       required
                                       class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 @error('days_to_install') border-red-500 @enderror">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                                    <span class="text-slate-400 text-sm">days</span>
                                </div>
                            </div>
                            <p class="text-slate-400 text-sm">
                                Number of days after a macOS release that the update must be installed.
                            </p>
                            @error('days_to_install')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-between pt-6">
                            <a href="{{ route('subscribers.index') }}" 
                               class="inline-flex items-center gap-2 px-6 py-3 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-medium rounded-xl transition-all duration-300 backdrop-blur-xl">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                                </svg>
                                Back
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-medium rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M7.707 10.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V6h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2h5v5.586l-1.293-1.293zM9 4a1 1 0 012 0v2H9V4z" />
                                </svg>
                                Update Subscriber
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
