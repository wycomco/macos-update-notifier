<x-guest-layout>
    <div class="mb-8 text-center">
        <div class="mx-auto w-16 h-16 bg-gradient-to-br from-purple-500 to-blue-600 rounded-full flex items-center justify-center mb-4 shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M14.243 5.757a6 6 0 10-.986 9.284 1 1 0 111.087 1.678A8 8 0 1118 10a3 3 0 01-4.8 2.401A4 4 0 1114 10a1 1 0 102 0c0-1.537-.586-3.07-1.757-4.243zM12 10a2 2 0 10-4 0 2 2 0 004 0z" clip-rule="evenodd" />
            </svg>
        </div>
        <h1 class="text-3xl font-bold bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent">
            Magic Link Sign In
        </h1>
        <p class="mt-2 text-slate-400">
            No password needed — we'll send you a secure one-time link
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('magic-link.request') }}" class="space-y-6">
        @csrf

        <!-- Honeypot fields (hidden from users but visible to bots) -->
        <div style="position: absolute; left: -9999px; top: -9999px;">
            <input type="text" name="website" tabindex="-1" autocomplete="off">
            <input type="text" name="url" tabindex="-1" autocomplete="off">
            <input type="text" name="homepage" tabindex="-1" autocomplete="off">
            <input type="text" name="phone" tabindex="-1" autocomplete="off">
        </div>

        <!-- Email Address -->
        <div class="space-y-2">
            <x-input-label for="email" :value="__('Email address')" class="text-slate-200 font-medium" />
            <x-text-input id="email" 
                         class="block w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-slate-400 focus:border-purple-400 focus:ring-purple-400 focus:ring-2 focus:ring-offset-0 transition-all backdrop-blur-sm" 
                         type="email" 
                         name="email" 
                         :value="old('email')" 
                         required 
                         autofocus 
                         autocomplete="username"
                         placeholder="Enter your email address" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Math CAPTCHA -->
        <div class="space-y-2">
            <x-input-label for="captcha_answer" :value="__('Security Check')" class="text-slate-200 font-medium" />
            <div class="p-4 rounded-xl bg-gradient-to-r from-amber-500/10 to-orange-500/10 border border-amber-500/20">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-full bg-amber-500/20 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-amber-400 mb-2">
                            What is {{ $captcha['question'] }}?
                        </p>
                        <x-text-input id="captcha_answer"
                                     class="w-24 px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-white placeholder-slate-400 focus:border-amber-400 focus:ring-amber-400 focus:ring-2 focus:ring-offset-0 transition-all backdrop-blur-sm"
                                     type="number"
                                     name="captcha_answer"
                                     :value="old('captcha_answer')"
                                     required
                                     placeholder="Answer" />
                        <input type="hidden" name="captcha_key" value="{{ $captcha['key'] }}">
                    </div>
                </div>
            </div>
            <x-input-error :messages="$errors->get('captcha_answer')" class="mt-2" />
            <x-input-error :messages="$errors->get('security')" class="mt-2" />
        </div>

        <!-- Info Box -->
        <div class="p-4 rounded-xl bg-gradient-to-r from-emerald-500/10 to-blue-500/10 border border-emerald-500/20">
            <div class="flex items-start gap-3">
                <div class="p-1 rounded-full bg-emerald-500/20 flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-emerald-400">How it works</p>
                    <p class="text-xs text-slate-300 mt-1">
                        We'll send a secure link to your email. Click it to sign in instantly — no password required!
                        Don't have an account? We'll create one when you click the link.
                    </p>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="space-y-4">
            <button type="submit" 
                    class="w-full inline-flex items-center justify-center px-6 py-3 rounded-lg bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-500 hover:to-blue-500 text-white font-semibold shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                </svg>
                {{ __('Send Magic Link') }}
            </button>

            <a href="{{ route('login') }}" 
               class="w-full inline-flex items-center justify-center px-6 py-3 rounded-lg border border-white/20 hover:border-white/40 bg-white/10 hover:bg-white/20 text-white font-medium transition-all duration-300 backdrop-blur-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                </svg>
                Use Password Instead
            </a>
        </div>

        <!-- Footer -->
        <div class="pt-4 text-center border-t border-white/10">
            <p class="text-sm text-slate-400">
                By continuing, you agree to receive email notifications about macOS updates.
            </p>
        </div>
    </form>
</x-guest-layout>
