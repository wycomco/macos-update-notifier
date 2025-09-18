<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent">
                    Bulk Import Subscribers
                </h2>
                <p class="mt-2 text-slate-400">
                    @if($method === 'csv')
                        Upload a CSV file to import multiple subscribers at once
                    @else
                        Paste a list of email addresses to import multiple subscribers
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-4">
                <!-- Method Toggle -->
                <div class="flex items-center bg-white/10 backdrop-blur-xl border border-white/20 rounded-xl p-1">
                    <a href="{{ route('subscribers.import', ['method' => 'textarea']) }}" 
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ $method === 'textarea' ? 'bg-blue-500 text-white shadow-lg' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0v12h8V4H6z" clip-rule="evenodd" />
                        </svg>
                        Paste Emails
                    </a>
                    <a href="{{ route('subscribers.import', ['method' => 'csv']) }}" 
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ $method === 'csv' ? 'bg-green-500 text-white shadow-lg' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        Upload CSV
                    </a>
                </div>
                
                <a href="{{ route('subscribers.index') }}" 
                   class="inline-flex items-center px-6 py-3 rounded-xl bg-white/10 backdrop-blur-xl border border-white/20 text-slate-300 hover:text-white hover:bg-white/20 transition-all duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Back to Subscribers
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-6">
            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-6 group relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-red-500/10 to-red-600/10 rounded-2xl blur-xl"></div>
                    <div class="relative bg-red-500/10 backdrop-blur-xl border border-red-500/20 rounded-2xl p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-2 bg-red-500/20 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-red-300">Import Errors</h3>
                        </div>
                        <ul class="text-red-200 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li class="flex items-start gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mt-0.5 text-red-400 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $error }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Import Form -->
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-white/10 rounded-2xl blur-xl"></div>
                <div class="relative bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-8">
                    
                    <form method="POST" action="{{ route('subscribers.import.process') }}" enctype="multipart/form-data" class="space-y-8">
                        @csrf

                        <!-- Import Method Section -->
                        @if($method === 'csv')
                            <!-- CSV File Upload -->
                            <div>
                                <label for="csv_file" class="block text-lg font-semibold text-slate-200 mb-3">
                                    Upload CSV File
                                    <span class="text-red-400">*</span>
                                </label>
                                <div class="mt-1">
                                    <input type="file" 
                                           id="csv_file" 
                                           name="csv_file" 
                                           accept=".csv,.txt"
                                           class="block w-full px-4 py-4 rounded-xl bg-white/10 border border-white/20 text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-gradient-to-r file:from-green-500 file:to-emerald-600 file:text-white file:font-semibold hover:file:from-green-600 hover:file:to-emerald-700 transition-all"
                                           required>
                                </div>
                                <div class="mt-3 p-4 bg-green-500/10 rounded-xl border border-green-500/20">
                                    <h4 class="text-sm font-semibold text-green-300 mb-2">CSV Format Requirements:</h4>
                                    <ul class="text-green-200 text-sm space-y-1">
                                        <li>• One email address per line (first column)</li>
                                        <li>• Optional header row (will be automatically detected)</li>
                                        <li>• Supported formats: .csv, .txt</li>
                                    </ul>
                                    <div class="mt-3 p-3 bg-green-600/20 rounded-lg">
                                        <p class="text-green-200 text-sm font-medium">Example:</p>
                                        <code class="text-green-100 text-xs">
                                            email<br>
                                            user1@example.com<br>
                                            user2@example.com<br>
                                            user3@example.com
                                        </code>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Email Textarea -->
                            <div>
                                <label for="emails" class="block text-lg font-semibold text-slate-200 mb-3">
                                    Email Addresses
                                    <span class="text-red-400">*</span>
                                </label>
                                <div class="mt-1">
                                    <textarea id="emails" 
                                              name="emails" 
                                              rows="12" 
                                              placeholder="user1@example.com&#10;user2@example.com&#10;user3@example.com&#10;..."
                                              class="block w-full px-4 py-4 rounded-xl bg-white/10 border border-white/20 text-white placeholder-slate-400 focus:border-blue-400 focus:ring-blue-400 focus:ring-2 focus:ring-offset-0 transition-all backdrop-blur-sm resize-none"
                                              required>{{ old('emails') }}</textarea>
                                </div>
                                <div class="mt-3 p-4 bg-blue-500/10 rounded-xl border border-blue-500/20">
                                    <h4 class="text-sm font-semibold text-blue-300 mb-2">Paste Instructions:</h4>
                                    <ul class="text-blue-200 text-sm space-y-1">
                                        <li>• Enter one email address per line</li>
                                        <li>• Invalid emails will be automatically skipped</li>
                                        <li>• Duplicate emails will be ignored</li>
                                        <li>• You can paste from Excel, Google Sheets, or any text source</li>
                                    </ul>
                                </div>
                            </div>
                        @endif

                        <!-- macOS Versions Section -->
                        <div>
                            <label class="block text-lg font-semibold text-slate-200 mb-4">
                                macOS Versions to Subscribe To
                                <span class="text-red-400">*</span>
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                @foreach($availableVersions as $version)
                                    <label class="flex items-center p-4 bg-white/10 rounded-xl border border-white/20 hover:bg-white/20 transition-all cursor-pointer group">
                                        <input type="checkbox" 
                                               name="subscribed_versions[]" 
                                               value="{{ $version }}"
                                               {{ in_array($version, old('subscribed_versions', [])) ? 'checked' : '' }}
                                               class="w-5 h-5 rounded border-white/20 bg-white/10 text-purple-500 focus:ring-purple-400 focus:ring-2 focus:ring-offset-0">
                                        <span class="ml-3 text-slate-200 font-medium group-hover:text-white transition-colors">{{ $version }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <p class="mt-3 text-sm text-slate-400">
                                Select which macOS versions these subscribers should be notified about.
                            </p>
                        </div>

                        <!-- Days to Install -->
                        <div>
                            <label for="days_to_install" class="block text-lg font-semibold text-slate-200 mb-3">
                                Days to Install
                                <span class="text-red-400">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" 
                                       id="days_to_install" 
                                       name="days_to_install" 
                                       min="1" 
                                       max="365" 
                                       value="{{ old('days_to_install', 30) }}"
                                       class="block w-full px-4 py-4 rounded-xl bg-white/10 border border-white/20 text-white placeholder-slate-400 focus:border-purple-400 focus:ring-purple-400 focus:ring-2 focus:ring-offset-0 transition-all backdrop-blur-sm"
                                       required>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <span class="text-slate-400">days</span>
                                </div>
                            </div>
                            <p class="mt-3 text-sm text-slate-400">
                                Number of days after release that notifications should be sent (1-365 days).
                            </p>
                        </div>

                        <!-- Language Selection -->
                        <div>
                            <label for="language" class="block text-lg font-semibold text-slate-200 mb-3">
                                Notification Language
                            </label>
                            <div class="relative">
                                <select id="language" 
                                        name="language" 
                                        class="block w-full px-4 py-4 rounded-xl bg-white/10 border border-white/20 text-white focus:border-orange-400 focus:ring-orange-400 focus:ring-2 focus:ring-offset-0 transition-all backdrop-blur-sm appearance-none">
                                    <option value="" class="bg-slate-800 text-slate-200">Use Default Language ({{ config('subscriber_languages.supported.' . config('subscriber_languages.default') . '.name', 'English') }})</option>
                                    @foreach($supportedLanguages as $code => $languageData)
                                        <option value="{{ $code }}" 
                                                {{ old('language') === $code ? 'selected' : '' }}
                                                class="bg-slate-800 text-slate-200">
                                            {{ $languageData['flag'] }} {{ $languageData['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            <p class="mt-3 text-sm text-slate-400">
                                Choose the language for notifications that will be sent to these subscribers. If not specified, the default language will be used.
                            </p>
                        </div>

                        <!-- Import Preview -->
                        <div class="p-6 bg-purple-500/10 rounded-xl border border-purple-500/20">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="p-2 bg-purple-500/20 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-purple-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-purple-300">Import Summary</h4>
                            </div>
                            <div class="text-purple-200 space-y-2">
                                <p>• All imported subscribers will be assigned to your account (<strong>{{ Auth::user()->email }}</strong>)</p>
                                <p>• Subscribers will receive notifications for the selected macOS versions</p>
                                <p>• Existing subscribers will be automatically skipped</p>
                                <p>• Invalid email addresses will be reported after import</p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-between pt-6 border-t border-white/10">
                            <a href="{{ route('subscribers.index') }}" 
                               class="inline-flex items-center px-6 py-3 rounded-xl bg-white/10 backdrop-blur-xl border border-white/20 text-slate-300 hover:text-white hover:bg-white/20 transition-all duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                                </svg>
                                Cancel
                            </a>
                            
                            <button type="submit" 
                                    class="inline-flex items-center px-8 py-3 rounded-xl bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-500 hover:to-blue-500 text-white font-semibold shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                                @if($method === 'csv')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    Import from CSV
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                    </svg>
                                    Import Subscribers
                                @endif
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Auto-format pasted email lists
        document.getElementById('emails')?.addEventListener('paste', function(e) {
            setTimeout(() => {
                let content = this.value;
                // Clean up common formatting issues
                content = content.replace(/[,;]/g, '\n'); // Replace commas/semicolons with newlines
                content = content.replace(/\s+\n/g, '\n'); // Remove spaces before newlines
                content = content.replace(/\n\s+/g, '\n'); // Remove spaces after newlines
                content = content.replace(/\n+/g, '\n'); // Remove multiple newlines
                content = content.trim();
                this.value = content;
            }, 100);
        });
    </script>
    @endpush
</x-app-layout>
