<x-guest-layout>
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent">
            Welcome back
        </h1>
        <p class="mt-2 text-slate-400">
            Sign in to manage your macOS update notifications
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

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
                         placeholder="Enter your email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="space-y-2">
            <x-input-label for="password" :value="__('Password')" class="text-slate-200 font-medium" />
            <x-text-input id="password" 
                         class="block w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-slate-400 focus:border-purple-400 focus:ring-purple-400 focus:ring-2 focus:ring-offset-0 transition-all backdrop-blur-sm" 
                         type="password" 
                         name="password" 
                         required 
                         autocomplete="current-password"
                         placeholder="Enter your password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-slate-300">
                <input id="remember_me" 
                       type="checkbox" 
                       class="rounded bg-white/10 border-white/20 text-purple-500 focus:ring-purple-500 focus:ring-2 focus:ring-offset-0" 
                       name="remember">
                {{ __('Remember me') }}
            </label>
            @if (Route::has('password.request'))
                <a class="text-sm text-purple-400 hover:text-purple-300 underline transition-colors" 
                   href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <div class="space-y-4">
            <button type="submit" 
                    class="w-full inline-flex items-center justify-center px-6 py-3 rounded-lg bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-500 hover:to-blue-500 text-white font-semibold shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 3a1 1 0 011 1v12a1 1 0 11-2 0V4a1 1 0 011-1zm7.707 3.293a1 1 0 010 1.414L9.414 9H17a1 1 0 110 2H9.414l1.293 1.293a1 1 0 01-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                {{ __('Sign In') }}
            </button>

            @if (Route::has('magic-link.form'))
                <a href="{{ route('magic-link.form') }}" 
                   class="w-full inline-flex items-center justify-center px-6 py-3 rounded-lg border border-white/20 hover:border-white/40 bg-white/10 hover:bg-white/20 text-white font-medium transition-all duration-300 backdrop-blur-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M14.243 5.757a6 6 0 10-.986 9.284 1 1 0 111.087 1.678A8 8 0 1118 10a3 3 0 01-4.8 2.401A4 4 0 1114 10a1 1 0 102 0c0-1.537-.586-3.07-1.757-4.243zM12 10a2 2 0 10-4 0 2 2 0 004 0z" clip-rule="evenodd" />
                    </svg>
                    Use Magic Link Instead
                </a>
            @endif
        </div>

        <!-- Register Link -->
        <div class="pt-4 text-center border-t border-white/10">
            <p class="text-sm text-slate-400">
                {{ __("Don't have an account?") }} 
                <a href="{{ route('register') }}" 
                   class="text-purple-400 hover:text-purple-300 underline font-medium transition-colors">
                    {{ __('Create one now') }}
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
