<?php

return [
    'macos_update' => [
        'subject' => 'macOS Update Required: :version :build',
        'title' => '🍎 macOS Update Required',
        'alert' => [
            'multiple_days' => 'Update Reminder: :days days remaining',
            'tomorrow' => '⚠️ Final Notice: Update required by tomorrow',
            'today' => '🚨 Critical: Update required today',
            'overdue' => '🚨 Overdue: Update deadline has passed',
        ],
        'new_release_title' => 'New macOS Release Available',
        'release_info' => [
            'release' => 'Release:',
            'release_date' => 'Release Date:',
            'deadline' => 'Your Deadline:',
            'days_remaining' => 'Days Remaining:',
            'days_overdue' => 'Days Overdue:',
        ],
        'action_required_title' => '📋 Action Required',
        'action_text' => 'Please install the macOS update <strong>:version</strong> as soon as possible.',
        'install_steps_title' => 'To install the update:',
        'install_steps' => [
            'Click the Apple menu 🍎 in the top-left corner',
            'Select "About This Mac"',
            'Click "More Info..." then "Software Update"',
            'Follow the prompts to install available updates',
        ],
        'warning_title' => '⚠️ Important Warning:',
        'warning_text' => 'Failure to install this update by your deadline may result in:',
        'warning_points' => [
            'Automatic forced installation',
            'Unexpected system restarts',
            'Potential data loss if files are not saved',
            'Temporary system unavailability',
        ],
        'why_important_title' => '🛡️ Why This Update is Important',
        'why_important_text' => 'macOS updates typically include:',
        'why_important_points' => [
            'Critical security patches',
            'Bug fixes and stability improvements',
            'Performance enhancements',
            'New features and functionality',
        ],
        'need_help' => 'Need Help?',
        'need_help_with_admin' => 'Contact your administrator <strong>:admin_name</strong> (:admin_email) if you have questions about this update or need assistance with the installation process.',
        'need_help_without_admin' => 'Contact your IT administrator if you have questions about this update or need assistance with the installation process.',
        'footer' => [
            'automated_notification' => 'This is an automated notification from the macOS Update Notifier system.',
            'subscription_reason' => 'You are receiving this because you are subscribed to updates for :version.',
            'change_version' => 'Change your macOS version preference',
            'change_language' => 'Change your language preference',
            'unsubscribe' => 'Unsubscribe from these notifications',
        ],
    ],
];