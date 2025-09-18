<?php

return [
    'macos_update' => [
        'subject' => 'Actualización de macOS Requerida: :version :build',
        'title' => '🍎 Actualización de macOS Requerida',
        'alert' => [
            'multiple_days' => 'Recordatorio de actualización: :days días restantes',
            'tomorrow' => '⚠️ Aviso final: Actualización requerida para mañana',
            'today' => '🚨 Crítico: Actualización requerida hoy',
            'overdue' => '🚨 Atrasado: La fecha límite de actualización ha pasado',
        ],
        'new_release_title' => 'Nueva versión de macOS disponible',
        'release_info' => [
            'release' => 'Versión:',
            'release_date' => 'Fecha de lanzamiento:',
            'deadline' => 'Su fecha límite:',
            'days_remaining' => 'Días restantes:',
            'days_overdue' => 'Días de retraso:',
        ],
        'action_required_title' => '📋 Acción Requerida',
        'action_text' => 'Por favor instale la actualización de macOS <strong>:version</strong> tan pronto como sea posible.',
        'install_steps_title' => 'Para instalar la actualización:',
        'install_steps' => [
            'Haga clic en el menú Apple 🍎 en la esquina superior izquierda',
            'Seleccione "Acerca de este Mac"',
            'Haga clic en "Más información..." luego "Actualización de software"',
            'Siga las instrucciones para instalar las actualizaciones disponibles',
        ],
        'warning_title' => '⚠️ Advertencia Importante:',
        'warning_text' => 'No instalar esta actualización antes de su fecha límite puede resultar en:',
        'warning_points' => [
            'Instalación automática forzada',
            'Reinicios del sistema inesperados',
            'Pérdida potencial de datos si los archivos no están guardados',
            'Indisponibilidad temporal del sistema',
        ],
        'why_important_title' => '🛡️ Por qué esta actualización es importante',
        'why_important_text' => 'Las actualizaciones de macOS típicamente incluyen:',
        'why_important_points' => [
            'Parches de seguridad críticos',
            'Corrección de errores y mejoras de estabilidad',
            'Mejoras de rendimiento',
            'Nuevas características y funcionalidades',
        ],
        'need_help' => '¿Necesita ayuda?',
        'need_help_with_admin' => 'Contacte a su administrador <strong>:admin_name</strong> (:admin_email) si tiene preguntas sobre esta actualización o necesita ayuda con el proceso de instalación.',
        'need_help_without_admin' => 'Contacte a su administrador de TI si tiene preguntas sobre esta actualización o necesita ayuda con el proceso de instalación.',
        'footer' => [
            'automated_notification' => 'Esta es una notificación automatizada del sistema de notificación de actualizaciones de macOS.',
            'subscription_reason' => 'Está recibiendo esto porque está suscrito a actualizaciones para :version.',
            'change_version' => 'Cambiar su preferencia de versión de macOS',
            'change_language' => 'Cambiar su preferencia de idioma',
            'unsubscribe' => 'Cancelar suscripción a estas notificaciones',
        ],
    ],
];