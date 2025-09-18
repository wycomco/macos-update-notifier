<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('public.unsubscribe.title') }} - {{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                {{ __('public.unsubscribe.title') }}
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                {{ __('public.unsubscribe.subtitle') }}
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('public.unsubscribe.subscriber_details') }}
                    </h3>
                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        <p><strong>{{ __('public.unsubscribe.email') }}</strong> {{ $subscriber->email }}</p>
                        <p><strong>{{ __('public.unsubscribe.subscribed_to') }}</strong> {{ implode(', ', $subscriber->subscribed_versions) }}</p>
                        <p><strong>{{ __('public.unsubscribe.install_deadline') }}</strong> {{ $subscriber->days_to_install }} {{ __('public.unsubscribe.days') }}</p>
                    </div>
                </div>

                <div class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-md">
                    <p class="text-yellow-800 dark:text-yellow-200 text-sm">
                        <strong>{{ __('public.unsubscribe.warning_title') }}</strong> {{ __('public.unsubscribe.warning_text') }}
                    </p>
                </div>

                <form method="POST" action="{{ route('public.unsubscribe.confirm', $subscriber->unsubscribe_token) }}">>
                    @csrf
                    <div class="flex flex-col space-y-3">
                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            {{ __('public.unsubscribe.confirm_button') }}
                        </button>
                        
                        <a href="/" class="w-full flex justify-center py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            {{ __('public.unsubscribe.keep_button') }}
                        </a>
                    </div>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ __('public.unsubscribe.having_issues') }}
                        @if($subscriber->admin)
                            {!! __('public.unsubscribe.contact_admin', ['email' => $subscriber->admin->email]) !!}
                        @else
                            {{ __('public.unsubscribe.contact_support') }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
