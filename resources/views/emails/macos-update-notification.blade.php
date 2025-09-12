<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>macOS Update Required</title>
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
        <h1>🍎 macOS Update Required</h1>
    </div>
    
    <div class="content">
        <div class="alert @if($daysRemaining <= 1) warning @endif">
            <strong>
                @if($daysRemaining > 1)
                    Update Reminder: {{ $daysRemaining }} days remaining
                @elseif($daysRemaining == 1)
                    ⚠️ Final Notice: Update required by tomorrow
                @elseif($daysRemaining == 0)
                    🚨 Critical: Update required today
                @else
                    🚨 Overdue: Update deadline has passed
                @endif
            </strong>
        </div>

        <h2>New macOS Release Available</h2>
        
        <div class="info-box">
            <p><strong>Release:</strong> {{ $release->major_version }} {{ $release->version }}</p>
            <p><strong>Release Date:</strong> {{ $release->release_date->format('F j, Y') }}</p>
            <p><strong>Your Deadline:</strong> <span class="deadline">{{ $deadline->format('F j, Y') }}</span></p>
            @if($daysRemaining >= 0)
                <p><strong>Days Remaining:</strong> {{ $daysRemaining }}</p>
            @else
                <p><strong>Days Overdue:</strong> {{ abs($daysRemaining) }}</p>
            @endif
        </div>

        <h2>📋 Action Required</h2>
        <p>Please install the macOS update <strong>{{ $release->version }}</strong> as soon as possible.</p>
        
        <p><strong>To install the update:</strong></p>
        <ol>
            <li>Click the Apple menu 🍎 in the top-left corner</li>
            <li>Select "About This Mac"</li>
            <li>Click "More Info..." then "Software Update"</li>
            <li>Follow the prompts to install available updates</li>
        </ol>

        <div class="alert warning">
            <p><strong>⚠️ Important Warning:</strong></p>
            <p>Failure to install this update by your deadline may result in:</p>
            <ul>
                <li>Automatic forced installation</li>
                <li>Unexpected system restarts</li>
                <li>Potential data loss if files are not saved</li>
                <li>Temporary system unavailability</li>
            </ul>
        </div>

        <h2>🛡️ Why This Update is Important</h2>
        <p>macOS updates typically include:</p>
        <ul>
            <li>Critical security patches</li>
            <li>Bug fixes and stability improvements</li>
            <li>Performance enhancements</li>
            <li>New features and functionality</li>
        </ul>

        <p><strong>Need Help?</strong> 
            @if($subscriber->admin)
                Contact your administrator <strong>{{ $subscriber->admin->name ?? $subscriber->admin->email }}</strong> 
                ({{ $subscriber->admin->email }}) if you have questions about this update or need assistance with the installation process.
            @else
                Contact your IT administrator if you have questions about this update or need assistance with the installation process.
            @endif
        </p>
    </div>

    <div class="footer">
        <p>This is an automated notification from the macOS Update Notifier system.</p>
        <p>You are receiving this because you are subscribed to updates for {{ $release->major_version }}.</p>
        
        <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #dee2e6;">
            <p style="margin: 5px 0;">
                <a href="{{ route('public.version-change', ['token' => $subscriber->unsubscribe_token]) }}" 
                   style="color: #007AFF; text-decoration: none;">
                    Change your macOS version preference
                </a>
            </p>
            <p style="margin: 5px 0;">
                <a href="{{ route('public.unsubscribe', ['token' => $subscriber->unsubscribe_token]) }}" 
                   style="color: #dc3545; text-decoration: none;">
                    Unsubscribe from these notifications
                </a>
            </p>
        </div>
    </div>
</body>
</html>
