<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent">
                    Re-enable Subscription
                </h2>
                <p class="mt-2 text-slate-400">
                    Restore notification access for this subscriber
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('subscribers.show', $subscriber) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-medium rounded-xl transition-all duration-300 backdrop-blur-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Back to Subscriber
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-6">
            
            <!-- Subscriber Information -->
            <div class="group relative mb-8">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-purple-600/10 rounded-2xl blur-xl"></div>
                <div class="relative bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6">
                    
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-16 h-16 bg-blue-500/20 rounded-2xl flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-white">{{ $subscriber->email }}</h3>
                            <div class="flex items-center gap-4 mt-2">
                                <span class="inline-flex items-center gap-2 text-red-400 font-medium">
                                    <div class="w-2 h-2 bg-red-400 rounded-full"></div>
                                    Unsubscribed
                                </span>
                                <span class="text-slate-400">{{ $subscriber->unsubscribed_at?->format('F j, Y \a\t g:i A') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 bg-white/5 rounded-xl border border-white/10">
                            <label class="text-slate-400 text-sm block mb-1">Language</label>
                            <span class="text-white font-medium">{{ $subscriber->getLanguageFlag() }} {{ $subscriber->getLanguageDisplayName() }}</span>
                        </div>
                        <div class="p-4 bg-white/5 rounded-xl border border-white/10">
                            <label class="text-slate-400 text-sm block mb-1">Days to Install</label>
                            <span class="text-white font-medium">{{ $subscriber->days_to_install }} days</span>
                        </div>
                        <div class="p-4 bg-white/5 rounded-xl border border-white/10">
                            <label class="text-slate-400 text-sm block mb-1">Subscribed Versions</label>
                            <div class="flex flex-wrap gap-1">
                                @forelse($subscriber->subscribed_versions as $version)
                                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-blue-500/20 text-blue-400 border border-blue-500/30">
                                        {{ $version }}
                                    </span>
                                @empty
                                    <span class="text-slate-400 text-sm">None</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Re-enable Form -->
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-br from-red-500/10 to-orange-600/10 rounded-2xl blur-xl"></div>
                <div class="relative bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-8">
                    
                    <form method="POST" action="{{ route('subscribers.resubscribe', $subscriber) }}" id="resubscribe-form">
                        @csrf
                        
                        <!-- Legal Warning -->
                        <div class="mb-8 p-6 bg-red-500/10 rounded-2xl border-2 border-red-500/20">
                            <div class="flex items-start gap-4">
                                <div class="p-3 bg-red-500/20 rounded-xl flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-red-400 font-bold text-xl mb-3">⚠️ Legal Compliance Warning</h4>
                                    <div class="text-red-100 space-y-3">
                                        <p class="font-medium">
                                            <strong>IMPORTANT:</strong> Re-enabling a subscription for someone who has unsubscribed may violate email marketing laws including:
                                        </p>
                                        <div class="bg-red-500/10 rounded-xl p-4 border border-red-500/20">
                                            <ul class="space-y-2 text-sm">
                                                <li class="flex items-start gap-2">
                                                    <span class="w-2 h-2 bg-red-400 rounded-full mt-2 flex-shrink-0"></span>
                                                    <span><strong>CAN-SPAM Act (US):</strong> Requires explicit consent to send commercial emails</span>
                                                </li>
                                                <li class="flex items-start gap-2">
                                                    <span class="w-2 h-2 bg-red-400 rounded-full mt-2 flex-shrink-0"></span>
                                                    <span><strong>GDPR (EU):</strong> Requires lawful basis and explicit consent for processing personal data</span>
                                                </li>
                                                <li class="flex items-start gap-2">
                                                    <span class="w-2 h-2 bg-red-400 rounded-full mt-2 flex-shrink-0"></span>
                                                    <span><strong>CASL (Canada):</strong> Requires express consent before sending commercial electronic messages</span>
                                                </li>
                                            </ul>
                                        </div>
                                        <p class="font-bold text-red-200 bg-red-500/10 p-3 rounded-xl border border-red-500/20">
                                            Only proceed if you have obtained explicit, documented consent from this subscriber to re-enable their subscription.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Fields -->
                        <div class="space-y-8">
                            <!-- Consent Method -->
                            <div class="space-y-3">
                                <label class="block text-white font-semibold text-lg" for="consent_method">
                                    How was consent obtained? <span class="text-red-400">*</span>
                                </label>
                                <select name="consent_method" 
                                        id="consent_method"
                                        required
                                        class="w-full px-4 py-4 bg-slate-800/50 border-2 border-white/20 rounded-xl text-white placeholder-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 outline-none transition-all duration-300 text-base @error('consent_method') border-red-500 @enderror">
                                    <option value="">Select consent method...</option>
                                    <option value="email" {{ old('consent_method') == 'email' ? 'selected' : '' }}>Email request from subscriber</option>
                                    <option value="phone" {{ old('consent_method') == 'phone' ? 'selected' : '' }}>Phone call from subscriber</option>
                                    <option value="in_person" {{ old('consent_method') == 'in_person' ? 'selected' : '' }}>In-person request</option>
                                    <option value="support_ticket" {{ old('consent_method') == 'support_ticket' ? 'selected' : '' }}>Support ticket/help desk</option>
                                    <option value="written_form" {{ old('consent_method') == 'written_form' ? 'selected' : '' }}>Written form/document</option>
                                    <option value="website_form" {{ old('consent_method') == 'website_form' ? 'selected' : '' }}>Website contact form</option>
                                    <option value="other" {{ old('consent_method') == 'other' ? 'selected' : '' }}>Other (specify in notes below)</option>
                                </select>
                                @error('consent_method')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Additional Notes -->
                            <div class="space-y-3">
                                <label class="block text-white font-semibold text-lg" for="consent_notes">
                                    Additional Notes/Documentation
                                </label>
                                <textarea name="consent_notes" 
                                          id="consent_notes"
                                          rows="4"
                                          placeholder="Enter any additional information about how consent was obtained, reference numbers, dates, etc."
                                          class="w-full px-4 py-4 bg-slate-800/50 border-2 border-white/20 rounded-xl text-white placeholder-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 outline-none transition-all duration-300 resize-none text-base @error('consent_notes') border-red-500 @enderror">{{ old('consent_notes') }}</textarea>
                                <p class="text-slate-400 text-sm">Maximum 1,000 characters</p>
                                @error('consent_notes')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Legal Confirmation Checkbox -->
                            <div class="p-6 bg-yellow-500/10 rounded-2xl border-2 border-yellow-500/20">
                                <label class="flex items-start gap-4 cursor-pointer">
                                    <input type="checkbox" 
                                           name="legal_confirmation" 
                                           value="1"
                                           required
                                           {{ old('legal_confirmation') ? 'checked' : '' }}
                                           class="mt-1 w-6 h-6 text-blue-600 bg-slate-800 border-2 border-white/20 rounded-lg focus:ring-4 focus:ring-blue-500/20 transition-all duration-200 @error('legal_confirmation') border-red-500 @enderror">
                                    <div>
                                        <div class="text-white font-semibold text-lg mb-2 flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                            I confirm that I have obtained explicit consent
                                        </div>
                                        <p class="text-yellow-100 leading-relaxed">
                                            I confirm that this subscriber has provided explicit consent to re-enable their subscription 
                                            and that I have documented evidence of this consent. I understand that re-enabling subscriptions 
                                            without proper consent may violate applicable email marketing laws.
                                        </p>
                                    </div>
                                </label>
                                @error('legal_confirmation')
                                    <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-white/10">
                            <a href="{{ route('subscribers.show', $subscriber) }}" 
                               class="px-8 py-3 text-slate-300 hover:text-white font-semibold rounded-xl border-2 border-white/20 hover:border-white/40 hover:bg-white/5 transition-all duration-300 text-base">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-8 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 text-base">
                                Re-enable Subscription
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Form validation
        document.getElementById('resubscribe-form').addEventListener('submit', function(e) {
            const consentMethod = document.getElementById('consent_method').value;
            const legalConfirmation = document.querySelector('input[name="legal_confirmation"]').checked;
            
            if (!consentMethod) {
                e.preventDefault();
                alert('Please select how consent was obtained.');
                document.getElementById('consent_method').focus();
                return;
            }
            
            if (!legalConfirmation) {
                e.preventDefault();
                alert('You must confirm that explicit consent has been obtained.');
                document.querySelector('input[name="legal_confirmation"]').focus();
                return;
            }
            
            // Final confirmation
            const confirmed = confirm(
                'Are you absolutely certain you have documented consent from this subscriber? ' +
                'This action will re-enable their subscription and they will start receiving notifications again.'
            );
            
            if (!confirmed) {
                e.preventDefault();
            }
        });
    </script>
</x-app-layout>