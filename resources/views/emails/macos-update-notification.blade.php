<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('emails.macos_update.title') }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #007AFF;
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .content {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 8px 8px;
            border: 1px solid #dee2e6;
            border-top: none;
        }
        .alert {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .warning {
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        .info-box {
            background-color: white;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 14px;
            color: #6c757d;
        }
        h1 {
            margin: 0;
            font-size: 24px;
        }
        h2 {
            color: #495057;
            font-size: 18px;
            margin-top: 0;
        }
        .deadline {
            font-size: 20px;
            font-weight: bold;
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ __('emails.macos_update.title') }}</h1>
    </div>
    
    <div class="content">
        <div class="alert @if($daysRemaining <= 1) warning @endif">
            <strong>
                @if($daysRemaining > 1)
                    {{ __('emails.macos_update.alert.multiple_days', ['days' => $daysRemaining]) }}
                @elseif($daysRemaining == 1)
                    {{ __('emails.macos_update.alert.tomorrow') }}
                @elseif($daysRemaining == 0)
                    {{ __('emails.macos_update.alert.today') }}
                @else
                    {{ __('emails.macos_update.alert.overdue') }}
                @endif
            </strong>
        </div>

        <h2>{{ __('emails.macos_update.new_release_title') }}</h2>
        
        <div class="info-box">
            <p><strong>{{ __('emails.macos_update.release_info.release') }}</strong> {{ $release->major_version }} {{ $release->version }}</p>
            <p><strong>{{ __('emails.macos_update.release_info.release_date') }}</strong> {{ $release->release_date->format('F j, Y') }}</p>
            <p><strong>{{ __('emails.macos_update.release_info.deadline') }}</strong> <span class="deadline">{{ $deadline->format('F j, Y') }}</span></p>
            @if($daysRemaining >= 0)
                <p><strong>{{ __('emails.macos_update.release_info.days_remaining') }}</strong> {{ $daysRemaining }}</p>
            @else
                <p><strong>{{ __('emails.macos_update.release_info.days_overdue') }}</strong> {{ abs($daysRemaining) }}</p>
            @endif
        </div>

        <h2>{{ __('emails.macos_update.action_required_title') }}</h2>
        <p>{!! __('emails.macos_update.action_text', ['version' => $release->version]) !!}</p>
        
        <p><strong>{{ __('emails.macos_update.install_steps_title') }}</strong></p>
        <ol>
            @foreach(__('emails.macos_update.install_steps') as $step)
                <li>{{ $step }}</li>
            @endforeach
        </ol>

        <div class="alert warning">
            <p><strong>{{ __('emails.macos_update.warning_title') }}</strong></p>
            <p>{{ __('emails.macos_update.warning_text') }}</p>
            <ul>
                @foreach(__('emails.macos_update.warning_points') as $point)
                    <li>{{ $point }}</li>
                @endforeach
            </ul>
        </div>

        <h2>{{ __('emails.macos_update.why_important_title') }}</h2>
        <p>{{ __('emails.macos_update.why_important_text') }}</p>
        <ul>
            @foreach(__('emails.macos_update.why_important_points') as $point)
                <li>{{ $point }}</li>
            @endforeach
        </ul>

        <p><strong>{{ __('emails.macos_update.need_help') }}</strong> 
            @if($subscriber->admin)
                {!! __('emails.macos_update.need_help_with_admin', [
                    'admin_name' => $subscriber->admin->name ?? $subscriber->admin->email,
                    'admin_email' => $subscriber->admin->email
                ]) !!}
            @else
                {{ __('emails.macos_update.need_help_without_admin') }}
            @endif
        </p>
    </div>

    <div class="footer">
        <p>{{ __('emails.macos_update.footer.automated_notification') }}</p>
        <p>{{ __('emails.macos_update.footer.subscription_reason', ['version' => $release->major_version]) }}</p>
        
        <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #dee2e6;">
            <p style="margin: 5px 0;">
                <a href="{{ route('public.version-change', ['token' => $subscriber->unsubscribe_token]) }}" 
                   style="color: #007AFF; text-decoration: none;">
                    {{ __('emails.macos_update.footer.change_version') }}
                </a>
            </p>
            <p style="margin: 5px 0;">
                <a href="{{ route('public.language-change', ['token' => $subscriber->unsubscribe_token]) }}" 
                   style="color: #007AFF; text-decoration: none;">
                    {{ __('emails.macos_update.footer.change_language') }}
                </a>
            </p>
            <p style="margin: 5px 0;">
                <a href="{{ route('public.unsubscribe', ['token' => $subscriber->unsubscribe_token]) }}" 
                   style="color: #dc3545; text-decoration: none;">
                    {{ __('emails.macos_update.footer.unsubscribe') }}
                </a>
            </p>
        </div>
    </div>
</body>
</html>
