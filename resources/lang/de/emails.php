<?php

return [
    'macos_update' => [
        'subject' => 'macOS Update Erforderlich: :version :build',
        'title' => '🍎 macOS Update Erforderlich',
        'alert' => [
            'multiple_days' => 'Update-Erinnerung: :days Tage verbleibend',
            'tomorrow' => '⚠️ Letzte Warnung: Update bis morgen erforderlich',
            'today' => '🚨 Kritisch: Update heute erforderlich',
            'overdue' => '🚨 Überfällig: Update-Frist ist abgelaufen',
        ],
        'new_release_title' => 'Neue macOS Version verfügbar',
        'release_info' => [
            'release' => 'Version:',
            'release_date' => 'Veröffentlichungsdatum:',
            'deadline' => 'Ihre Frist:',
            'days_remaining' => 'Verbleibende Tage:',
            'days_overdue' => 'Tage überfällig:',
        ],
        'action_required_title' => '📋 Aktion Erforderlich',
        'action_text' => 'Bitte installieren Sie das macOS Update <strong>:version</strong> so schnell wie möglich.',
        'install_steps_title' => 'So installieren Sie das Update:',
        'install_steps' => [
            'Klicken Sie auf das Apple-Menü 🍎 in der oberen linken Ecke',
            'Wählen Sie "Über diesen Mac"',
            'Klicken Sie auf "Weitere Informationen..." dann "Software-Update"',
            'Folgen Sie den Anweisungen zur Installation verfügbarer Updates',
        ],
        'warning_title' => '⚠️ Wichtige Warnung:',
        'warning_text' => 'Das Versäumnis, dieses Update bis zu Ihrer Frist zu installieren, kann zu folgendem führen:',
        'warning_points' => [
            'Automatische erzwungene Installation',
            'Unerwartete Systemneustarts',
            'Möglicher Datenverlust, wenn Dateien nicht gespeichert sind',
            'Vorübergehende Systemunaverfügbarkeit',
        ],
        'why_important_title' => '🛡️ Warum dieses Update wichtig ist',
        'why_important_text' => 'macOS Updates enthalten typischerweise:',
        'why_important_points' => [
            'Kritische Sicherheitspatches',
            'Fehlerbehebungen und Stabilitätsverbesserungen',
            'Leistungsverbesserungen',
            'Neue Funktionen und Features',
        ],
        'need_help' => 'Brauchen Sie Hilfe?',
        'need_help_with_admin' => 'Kontaktieren Sie Ihren Administrator <strong>:admin_name</strong> (:admin_email), wenn Sie Fragen zu diesem Update haben oder Hilfe bei der Installation benötigen.',
        'need_help_without_admin' => 'Kontaktieren Sie Ihren IT-Administrator, wenn Sie Fragen zu diesem Update haben oder Hilfe bei der Installation benötigen.',
        'footer' => [
            'automated_notification' => 'Dies ist eine automatische Benachrichtigung vom macOS Update Benachrichtigungssystem.',
            'subscription_reason' => 'Sie erhalten diese Nachricht, weil Sie Updates für :version abonniert haben.',
            'change_version' => 'Ihre macOS-Versionspräferenz ändern',
            'change_language' => 'Ihre Sprachpräferenz ändern',
            'unsubscribe' => 'Diese Benachrichtigungen abbestellen',
        ],
    ],
];