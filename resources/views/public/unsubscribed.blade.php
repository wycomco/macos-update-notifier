<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Unsubscribed - {{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="text-center">
                <div class="mx-auto h-12 w-12 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                    <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900 dark:text-white">
                    Successfully Unsubscribed
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    You will no longer receive macOS update notifications
                </p>
            </div>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <div class="text-center">
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        {{ $subscriber->email }} has been successfully unsubscribed from macOS update notifications.
                    </p>
                    
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md mb-6">
                        <p class="text-blue-800 dark:text-blue-200 text-sm">
                            <strong>Security Reminder:</strong> Don't forget to manually check for macOS updates regularly to keep your system secure. You can do this in System Preferences > Software Update.
                        </p>
                    </div>

                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        <p>If you unsubscribed by mistake, please contact 
                        @if($subscriber->admin)
                            your admin at {{ $subscriber->admin->email }}
                        @else
                            support
                        @endif
                        to resubscribe.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
