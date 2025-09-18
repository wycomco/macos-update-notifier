<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('public.already_unsubscribed.title') }} - {{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="text-center">
                <div class="mx-auto h-12 w-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                    <svg class="h-6 w-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900 dark:text-white">
                    {{ __('public.already_unsubscribed.title') }}
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('public.already_unsubscribed.subtitle') }}
                </p>
            </div>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <div class="text-center">
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        {!! __('public.already_unsubscribed.unsubscribed_on', [
                            'email' => $subscriber->email,
                            'date' => $subscriber->unsubscribed_at->format('M j, Y \a\t g:i A')
                        ]) !!}
                    </p>
                    
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        <p>
                            @if($subscriber->admin)
                                {!! __('public.already_unsubscribed.want_resubscribe', [
                                    'contact' => __('public.already_unsubscribed.your_admin', ['email' => $subscriber->admin->email])
                                ]) !!}
                            @else
                                {!! __('public.already_unsubscribed.want_resubscribe', [
                                    'contact' => __('public.already_unsubscribed.support')
                                ]) !!}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
