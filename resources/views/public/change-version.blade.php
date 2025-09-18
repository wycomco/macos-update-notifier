<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('public.change_version.title') }} - {{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                {{ __('public.change_version.title') }}
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                {{ __('public.change_version.subtitle') }}
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('public.change_version.current_subscription') }}
                    </h3>
                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        <p><strong>{{ __('public.change_version.email') }}</strong> {{ $subscriber->email }}</p>
                        <p><strong>{{ __('public.change_version.currently_subscribed') }}</strong> {{ implode(', ', $subscriber->subscribed_versions) }}</p>
                        <p><strong>{{ __('public.change_version.install_deadline') }}</strong> {{ $subscriber->days_to_install }} {{ __('public.change_version.days') }}</p>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md">
                        <ul class="text-red-800 dark:text-red-200 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('public.version-change.update', $subscriber->unsubscribe_token) }}">>
                    @csrf
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            {{ __('public.change_version.versions_label') }}
                        </label>
                        <div class="space-y-2">
                            @foreach($availableVersions as $version)
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="subscribed_versions[]" 
                                           value="{{ $version }}"
                                           {{ in_array($version, old('subscribed_versions', $subscriber->subscribed_versions)) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ $version }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex flex-col space-y-3">
                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            {{ __('public.change_version.update_button') }}
                        </button>
                        
                        <a href="/" class="w-full flex justify-center py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            {{ __('public.change_version.cancel_button') }}
                        </a>
                    </div>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ __('public.change_version.need_help') }}
                        @if($subscriber->admin)
                            {!! __('public.change_version.contact_admin', ['email' => $subscriber->admin->email]) !!}
                        @else
                            {{ __('public.change_version.contact_support') }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
