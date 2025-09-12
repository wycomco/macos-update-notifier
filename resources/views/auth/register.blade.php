<x-guest-layout>
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent">
            Create your account
        </h1>
        <p class="mt-2 text-slate-400">
            Start managing macOS updates for your organization
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Name -->
        <div class="space-y-2">
            <x-input-label for="name" :value="__('Full name')" class="text-slate-200 font-medium" />
            <x-text-input id="name" 
                         class="block w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-slate-400 focus:border-purple-400 focus:ring-purple-400 focus:ring-2 focus:ring-offset-0 transition-all backdrop-blur-sm" 
                         type="text" 
                         name="name" 
                         :value="old('name')" 
                         required 
                         autofocus 
                         autocomplete="name"
                         placeholder="Enter your full name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
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
                         autocomplete="new-password"
                         placeholder="Create a secure password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="space-y-2">
            <x-input-label for="password_confirmation" :value="__('Confirm password')" class="text-slate-200 font-medium" />
            <x-text-input id="password_confirmation" 
                         class="block w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-slate-400 focus:border-purple-400 focus:ring-purple-400 focus:ring-2 focus:ring-offset-0 transition-all backdrop-blur-sm" 
                         type="password" 
                         name="password_confirmation" 
                         required 
                         autocomplete="new-password"
                         placeholder="Confirm your password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Submit Button -->
        <div class="space-y-4">
            <button type="submit" 
                    class="w-full inline-flex items-center justify-center px-6 py-3 rounded-lg bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-500 hover:to-blue-500 text-white font-semibold shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" />
                </svg>
                {{ __('Create Account — Free Forever') }}
            </button>
        </div>

        <!-- Login Link -->
        <div class="pt-4 text-center border-t border-white/10">
            <p class="text-sm text-slate-400">
                {{ __('Already have an account?') }} 
                <a href="{{ route('login') }}" 
                   class="text-purple-400 hover:text-purple-300 underline font-medium transition-colors">
                    {{ __('Sign in instead') }}
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
