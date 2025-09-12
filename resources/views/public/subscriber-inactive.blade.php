<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Subscriber Inactive - {{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="text-center">
                <div class="mx-auto h-12 w-12 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center">
                    <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900 dark:text-white">
                    Subscriber Inactive
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    This subscription is no longer active
                </p>
            </div>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <div class="text-center">
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        {{ $subscriber->email }} is no longer subscribed to macOS update notifications.
                    </p>
                    
                    @if($subscriber->unsubscribed_at)
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                            Unsubscribed on {{ $subscriber->unsubscribed_at->format('M j, Y \a\t g:i A') }}
                        </p>
                    @endif

                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        <p>Want to resubscribe? Contact 
                        @if($subscriber->admin)
                            your admin at {{ $subscriber->admin->email }}
                        @else
                            support
                        @endif
                        for assistance.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
