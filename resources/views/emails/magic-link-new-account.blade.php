<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Your Login Link</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center; margin-bottom: 30px; }
        .content { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .button { display: inline-block; background: #3b82f6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; margin: 20px 0; }
        .footer { margin-top: 30px; font-size: 14px; color: #666; text-align: center; }
        .warning { background: #fef3c7; border: 1px solid #f59e0b; padding: 15px; border-radius: 6px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>macOS Update Notifier</h1>
        <p>Your secure login link</p>
    </div>

    <div class="content">
        <h2>Hello!</h2>
        
        <p>Someone requested to create a macOS Update Notifier account with your email address. Click the button below to create this account and to sign in securely:</p>
        
        <div style="text-align: center;">
            <a href="{{ $loginUrl }}" class="button">Create Your Account and Sign In</a>
        </div>
        
        <div class="warning">
            <strong>Security Notice:</strong> This link will expire in 15 minutes and can only be used once. If you didn't request this link, you can safely ignore this email.
        </div>
        
        <p>If the button doesn't work, you can copy and paste this link into your browser:</p>
        <p style="word-break: break-all; background: #f3f4f6; padding: 10px; border-radius: 4px;">
            {{ $loginUrl }}
        </p>
    </div>

    <div class="footer">
        <p>This email was sent to {{ $email }}</p>
        <p>© {{ date('Y') }} macOS Update Notifier. All rights reserved.</p>
    </div>
</body>
</html>
