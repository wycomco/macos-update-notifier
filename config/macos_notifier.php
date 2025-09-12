<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SOFA Feed Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the macOS SOFA feed that provides macOS release data.
    |
    */

    'sofa_feed_url' => env('SOFA_FEED_URL', 'https://sofafeed.macadmins.io/v1/macos_data_feed.json'),

    /*
    |--------------------------------------------------------------------------
    | Default Days to Install
    |--------------------------------------------------------------------------
    |
    | Default number of days after a macOS release that users have to install
    | the update. This is used when a subscriber doesn't specify their own
    | preference.
    |
    */

    'default_days_to_install' => env('DEFAULT_DAYS_TO_INSTALL', 30),

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for when notifications should be sent.
    |
    */

    'notification_warning_days' => env('NOTIFICATION_WARNING_DAYS', 2), // Send notification when deadline is within X days
];
