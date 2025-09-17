<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent">
                    Add New Subscriber
                </h2>
                <p class="mt-2 text-slate-400">
                    Create a new subscriber to receive macOS update notifications
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-6">
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-white/10 rounded-2xl blur-xl"></div>
                <div class="relative bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-8">
                    
                    <form action="{{ route('subscribers.store') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-white mb-2">
                                Email Address
                            </label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required
                                   class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 @error('email') border-red-500 ring-2 ring-red-500/50 @enderror"
                                   placeholder="subscriber@example.com">
                            @error('email')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Language Selection -->
                        <div>
                            <label for="language" class="block text-sm font-medium text-white mb-2">
                                Preferred Language
                            </label>
                            <select id="language" 
                                    name="language" 
                                    required
                                    class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 @error('language') border-red-500 ring-2 ring-red-500/50 @enderror">
                                @foreach($supportedLanguages as $code => $language)
                                    <option value="{{ $code }}" 
                                            {{ old('language', config('subscriber_languages.default_language', 'en')) === $code ? 'selected' : '' }}
                                            class="bg-slate-800 text-white">
                                        {{ $language['flag'] }} {{ $language['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-2 text-sm text-slate-400">
                                Language for email notifications and public pages
                            </p>
                            @error('language')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Current macOS Version -->
                        <div>
                            <label for="macos_version" class="block text-sm font-medium text-white mb-2">
                                Current macOS Version
                            </label>
                            <select id="macos_version" 
                                    name="macos_version" 
                                    required
                                    class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 @error('macos_version') border-red-500 ring-2 ring-red-500/50 @enderror">
                                <option value="" class="bg-slate-800 text-slate-400">Select macOS Version</option>
                                @php
                                    $macosVersions = ['Sonoma', 'Ventura', 'Monterey', 'Big Sur', 'Catalina', 'Mojave'];
                                @endphp
                                @foreach($macosVersions as $version)
                                    <option value="{{ $version }}" 
                                            {{ old('macos_version') === $version ? 'selected' : '' }}
                                            class="bg-slate-800 text-white">
                                        macOS {{ $version }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-2 text-sm text-slate-400">
                                The macOS version currently installed on the user's device
                            </p>
                            @error('macos_version')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Subscribed Versions -->
                        <div>
                            <label class="block text-sm font-medium text-white mb-4">
                                Subscribed macOS Versions
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($availableVersions as $version)
                                    <label class="group relative cursor-pointer has-[:checked]:bg-purple-500/20 has-[:checked]:border-purple-500/50 has-[:checked]:shadow-lg has-[:checked]:shadow-purple-500/25">
                                        <input type="checkbox" 
                                               name="subscribed_versions[]" 
                                               value="{{ $version }}" 
                                               {{ in_array($version, old('subscribed_versions', [])) ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="flex items-center gap-3 p-4 bg-white/10 border border-white/20 rounded-xl transition-all duration-300 peer-checked:bg-purple-500/20 peer-checked:border-purple-500/50 peer-checked:shadow-lg peer-checked:shadow-purple-500/25 hover:bg-white/20">
                                            <div class="w-5 h-5 rounded-full border-2 border-white/30 flex items-center justify-center peer-checked:border-purple-400 peer-checked:bg-purple-500 peer-checked:shadow-lg peer-checked:shadow-purple-500/50 transition-all duration-300">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <span class="text-white font-medium peer-checked:text-purple-200 transition-colors">{{ $version }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('subscribed_versions')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Days to Install -->
                        <div>
                            <label for="days_to_install" class="block text-sm font-medium text-white mb-2">
                                Days to Install After Release
                            </label>
                            <input type="number" 
                                   id="days_to_install" 
                                   name="days_to_install" 
                                   value="{{ old('days_to_install', config('macos_notifier.default_days_to_install', 30)) }}" 
                                   min="1" 
                                   max="365" 
                                   required
                                   class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 @error('days_to_install') border-red-500 ring-2 ring-red-500/50 @enderror">
                            <p class="mt-2 text-sm text-slate-400">
                                Number of days after a macOS release that the update must be installed
                            </p>
                            @error('days_to_install')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-between pt-6 border-t border-white/10">
                            <a href="{{ route('subscribers.index') }}" 
                               class="inline-flex items-center px-6 py-3 rounded-xl border border-white/20 hover:border-white/40 bg-white/10 hover:bg-white/20 text-white font-medium transition-all duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                                </svg>
                                Back to Subscribers
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center px-6 py-3 rounded-xl bg-gradient-to-r from-purple-500 to-blue-600 hover:from-purple-600 hover:to-blue-700 text-white font-semibold shadow-lg hover:shadow-xl transition-all duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Create Subscriber
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
