<?php

return [
    'macos_update' => [
        'subject' => 'Mise à jour macOS Requise : :version :build',
        'title' => '🍎 Mise à jour macOS Requise',
        'alert' => [
            'multiple_days' => 'Rappel de mise à jour : :days jours restants',
            'tomorrow' => '⚠️ Dernier avis : Mise à jour requise d\'ici demain',
            'today' => '🚨 Critique : Mise à jour requise aujourd\'hui',
            'overdue' => '🚨 En retard : La date limite de mise à jour est dépassée',
        ],
        'new_release_title' => 'Nouvelle version macOS disponible',
        'release_info' => [
            'release' => 'Version :',
            'release_date' => 'Date de sortie :',
            'deadline' => 'Votre échéance :',
            'days_remaining' => 'Jours restants :',
            'days_overdue' => 'Jours de retard :',
        ],
        'action_required_title' => '📋 Action Requise',
        'action_text' => 'Veuillez installer la mise à jour macOS <strong>:version</strong> dès que possible.',
        'install_steps_title' => 'Pour installer la mise à jour :',
        'install_steps' => [
            'Cliquez sur le menu Apple 🍎 dans le coin supérieur gauche',
            'Sélectionnez "À propos de ce Mac"',
            'Cliquez sur "Plus d\'informations..." puis "Mise à jour logicielle"',
            'Suivez les instructions pour installer les mises à jour disponibles',
        ],
        'warning_title' => '⚠️ Avertissement Important :',
        'warning_text' => 'Le défaut d\'installer cette mise à jour avant votre échéance peut entraîner :',
        'warning_points' => [
            'Installation automatique forcée',
            'Redémarrages système inattendus',
            'Perte potentielle de données si les fichiers ne sont pas sauvegardés',
            'Indisponibilité temporaire du système',
        ],
        'why_important_title' => '🛡️ Pourquoi cette mise à jour est importante',
        'why_important_text' => 'Les mises à jour macOS incluent généralement :',
        'why_important_points' => [
            'Correctifs de sécurité critiques',
            'Corrections de bugs et améliorations de stabilité',
            'Améliorations de performance',
            'Nouvelles fonctionnalités',
        ],
        'need_help' => 'Besoin d\'aide ?',
        'need_help_with_admin' => 'Contactez votre administrateur <strong>:admin_name</strong> (:admin_email) si vous avez des questions sur cette mise à jour ou besoin d\'aide pour l\'installation.',
        'need_help_without_admin' => 'Contactez votre administrateur informatique si vous avez des questions sur cette mise à jour ou besoin d\'aide pour l\'installation.',
        'footer' => [
            'automated_notification' => 'Ceci est une notification automatique du système de notification de mise à jour macOS.',
            'subscription_reason' => 'Vous recevez ceci car vous êtes abonné aux mises à jour pour :version.',
            'change_version' => 'Modifier votre préférence de version macOS',
            'change_language' => 'Modifier votre préférence de langue',
            'unsubscribe' => 'Se désabonner de ces notifications',
        ],
    ],
];