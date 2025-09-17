<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Language Updated - {{ config('app.name', 'Laravel') }}</title>
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
                    Language Updated
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Your language preference has been saved
                </p>
            </div>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <div class="text-center">
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        {{ $subscriber->email }} will now receive notifications in:
                    </p>
                    
                    <div class="mb-6">
                        <span class="inline-flex items-center px-4 py-2 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-lg text-lg font-medium">
                            {{ $subscriber->getLanguageDisplayNameWithFlag() }}
                        </span>
                    </div>
                    
                    <div class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md mb-6">
                        <p class="text-green-800 dark:text-green-200 text-sm">
                            <strong>All set!</strong> Future notifications about macOS updates will be sent in your selected language.
                        </p>
                    </div>

                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        <p>Questions about your subscription? Contact 
                        @if($subscriber->admin)
                            your admin at {{ $subscriber->admin->email }}
                        @else
                            support
                        @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>