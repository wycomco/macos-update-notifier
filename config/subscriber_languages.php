<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Subscriber Language
    |--------------------------------------------------------------------------
    |
    | The default language for new subscribers when none is specified.
    | This can be overridden by the DEFAULT_SUBSCRIBER_LANGUAGE environment variable.
    |
    */
    'default' => env('DEFAULT_SUBSCRIBER_LANGUAGE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Supported Languages
    |--------------------------------------------------------------------------
    |
    | List of supported languages for subscriber notifications and public views.
    | Each entry contains the language code and display name.
    |
    */
    'supported' => [
        'en' => [
            'name' => 'English',
            'flag' => '🇺🇸',
        ],
        'de' => [
            'name' => 'Deutsch',
            'flag' => '🇩🇪',
        ],
        'fr' => [
            'name' => 'Français',
            'flag' => '🇫🇷',
        ],
        'es' => [
            'name' => 'Español',
            'flag' => '🇪🇸',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Language Validation Rules
    |--------------------------------------------------------------------------
    |
    | Validation rules for language selection
    |
    */
    'validation_rule' => 'nullable|string|in:en,de,fr,es',
];