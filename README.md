# macOS Update Notifier

> **A proactive email notification system for MacAdmins to ensure timely macOS updates across their fleet**

## 🎯 What is this?

As a MacAdmin, you know the challenge: users tend to ignore macOS update notifications that appear on their screens, but they actually read and respond to emails. The **macOS Update Notifier** bridges this gap by sending timely email notifications when macOS updates are released and installation deadlines are approaching.

### The Problem

- System notifications get dismissed or ignored
- Users postpone updates indefinitely
- Critical security updates are delayed
- Compliance requirements are missed

### The Solution

- Email notifications that users actually read
- Customizable installation deadlines per user/group
- Automated monitoring of macOS releases
- Professional email templates with clear instructions
- Multi-admin management with role-based access

---

## 🚀 Key Features

### For MacAdmins

- **📊 Administrative Dashboard**: Overview of subscribers, recent releases, and system statistics
- **👥 User Management**: Super admin and regular admin roles with appropriate access controls
- **📧 Bulk Import**: Import subscribers via CSV upload or copy/paste from spreadsheets
- **📈 Activity Tracking**: Monitor subscriber actions and notification history
- **🔄 Automated Monitoring**: Daily checks for new macOS releases from the SOFA feed

### For End Users

- **📬 Timely Notifications**: Email alerts when updates are available and deadlines approach
- **⚙️ Self-Service Options**: Links to update preferences or unsubscribe
- **📝 Clear Instructions**: Step-by-step update installation guidance
- **⏰ Deadline Awareness**: Clear communication about installation deadlines

### Technical Features

- **🔐 Secure Authentication**: Magic link login system (no passwords to manage)
- **📱 Responsive Design**: Modern glassmorphism UI that works on all devices
- **🗃️ SQLite Database**: No complex database setup required – but available when needed
- **⚡ Background Jobs**: Efficient processing of notifications and data fetching
- **🧪 Comprehensive Testing**: 190+ tests ensuring reliability

---

## 🏗️ Architecture Overview

```text
┌─────────────────┐    ┌──────────────────┐    ┌──────────────────┐
│   SOFA Feed     │───▶│  Update Notifier │───▶│   Email Server   │
│ (macOS Releases)│    │     (Laravel)    │    │    (SMTP/SES)    │
└─────────────────┘    └──────────────────┘    └──────────────────┘
                                 ▲                      │
                                 │                      ▼
                       ┌──────────────────┐    ┌──────────────────┐
                       │    Mac Admins    │    │   Subscribers    │
                       │   (Web Access)   │    │  (Email client)  │
                       └──────────────────┘    └──────────────────┘
```

### How It Works

1. **Daily Monitoring**: System fetches latest macOS releases from the [SOFA feed](https://sofa.macadmins.io/)
2. **Deadline Calculation**: Calculates installation deadlines based on subscriber preferences
3. **Smart Notifications**: Sends emails only when deadlines are approaching (configurable threshold)
4. **User Actions**: Subscribers receive emails with clear instructions and self-service options

---

## 📋 Prerequisites

Before installing, ensure you have:

- **Web Server**: Apache, Nginx, or development server
- **PHP 8.2+** with extensions:
  - SQLite PDO (for database)
  - cURL (for fetching releases)
  - OpenSSL (for security)
  - Mbstring (for email handling)
- **Composer** (PHP dependency manager)
- **Email Service**: SMTP server, AWS SES, or similar
- **NPM**: for frontend compilation

> **💡 For Non-Developers**: If these terms are unfamiliar, consider using a hosting service like [Laravel Forge](https://forge.laravel.com/) or [DigitalOcean App Platform](https://www.digitalocean.com/products/app-platform) that handles the technical setup.

---

## 🛠️ Installation

### Method 1: Quick Start (Recommended for Testing)

```bash
# 1. Download the project
git clone https://github.com/wycomco/macos-update-notifier.git
cd macos-update-notifier

# 2. Install dependencies
composer install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Create database
touch database/database.sqlite
php artisan migrate

# 5. Start the development server
php artisan serve
```

Visit `http://localhost:8000` to access the application.

### Method 2: Production Deployment

For production use, consider these hosting options:

#### Option A: Laravel Forge (Easiest)

1. Sign up for [Laravel Forge](https://forge.laravel.com/)
2. Connect your server (DigitalOcean, AWS, etc.)
3. Deploy directly from your GitHub repository
4. Forge handles SSL, database, and server configuration

#### Option B: Manual Server Setup

1. Configure your web server (Apache/Nginx)
2. Set up SSL certificates
3. Configure scheduled tasks (cron)
4. Set up process monitoring (Supervisor)

> **📖 Detailed deployment guides** are available in the [Laravel documentation](https://laravel.com/docs/deployment).

---

## ⚙️ Configuration

### 1. Environment Setup

Edit your `.env` file with these essential settings:

```env
# Application
APP_NAME="macOS Update Notifier"
APP_URL=https://your-domain.com

# Database (SQLite - no setup required)
DB_CONNECTION=sqlite

# Email Configuration (REQUIRED)
MAIL_MAILER=smtp
MAIL_HOST=smtp.your-provider.com
MAIL_PORT=587
MAIL_USERNAME=your-email@company.com
MAIL_PASSWORD=your-app-password
MAIL_FROM_ADDRESS="macos-updates@company.com"
MAIL_FROM_NAME="macOS Update Team"

# macOS Notifier Settings
SOFA_FEED_URL=https://sofafeed.macadmins.io/v1/macos_data_feed.json
DEFAULT_DAYS_TO_INSTALL=30
NOTIFICATION_WARNING_DAYS=7
```

### 2. Email Service Setup

Choose your email provider:

#### Gmail/Google Workspace

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password  # Use App Password, not regular password
```

#### Microsoft 365

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.office365.com
MAIL_PORT=587
MAIL_USERNAME=your-email@company.com
MAIL_PASSWORD=your-password
```

#### AWS SES (Scalable)

```env
MAIL_MAILER=ses
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=us-east-1
```

### 3. Super Admin Setup

Create your first admin account:

```bash
php artisan tinker
```

```php
use App\Models\User;

User::create([
    'name' => 'Your Name',
    'email' => 'admin@company.com',
    'password' => bcrypt('temporary-password'),
    'is_super_admin' => true
]);
```

### 4. Automated Tasks

For production, set up the Laravel scheduler:

```bash
# Add to your server's crontab
crontab -e

# Add this line:
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

For development/testing:

```bash
php artisan schedule:work
```

### 5. Queue Worker Setup (Required for Production)

The application uses background jobs for email processing and other tasks. In production, you **must** run a queue worker:

#### Option A: Using Supervisor (Recommended for Production)

Create a supervisor configuration file `/etc/supervisor/conf.d/macos-notifier.conf`:

```ini
[program:macos-notifier-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your/project/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/path/to/your/project/storage/logs/worker.log
stopwaitsecs=3600
```

Then start the supervisor service:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start macos-notifier-worker:*
```

#### Option B: Using systemd

Create a systemd service file `/etc/systemd/system/macos-notifier-queue.service`:

```ini
[Unit]
Description=macOS Update Notifier Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/path/to/your/project
ExecStart=/usr/bin/php artisan queue:work --sleep=3 --tries=3 --max-time=3600
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
```

Enable and start the service:

```bash
sudo systemctl enable macos-notifier-queue
sudo systemctl start macos-notifier-queue
```

#### Option C: Development/Testing

For development or testing environments, you can run the queue worker manually:

```bash
php artisan queue:work
```

> **⚠️ Important**: Without a queue worker running, emails will not be sent and other background tasks will not execute. The application will appear to work but notifications will remain in the queue.

---

## 👥 User Management

### Admin Roles

#### Super Admin

- **Full System Access**: Manage all users and subscribers
- **User Management**: Create, promote, demote, and delete admin accounts
- **System Overview**: View system-wide statistics and all subscriber data
- **Settings Control**: Configure system-wide settings

#### Regular Admin

- **Subscriber Management**: Manage only their assigned subscribers
- **Limited Dashboard**: View only their subscriber statistics
- **No User Access**: Cannot manage other admin accounts

### Adding Subscribers

#### Method 1: Individual Entry

1. Navigate to **Subscribers → Add New**
2. Fill in subscriber details
3. Select macOS versions to monitor
4. Set installation deadline (days after release)

#### Method 2: Bulk Import

1. Navigate to **Subscribers → Bulk Import**
2. Choose your method:
   - **CSV Upload**: Upload a properly formatted CSV file
   - **Copy/Paste**: Paste data directly from Excel/Sheets
3. Configure import settings:
   - **macOS Versions**: Select which versions to monitor
   - **Days to Install**: Set notification deadline (1-365 days)
   - **Language**: Choose notification language (optional, defaults to English)

##### CSV Format Example

```csv
email,subscribed_versions,days_to_install
user1@company.com,"macOS 14,macOS 15",30
user2@company.com,"macOS 15",14
admin@company.com,"macOS 14,macOS 15",60
```

##### Language Support

- **English** (en) - Default
- **German** (de) - Deutsch
- **French** (fr) - Français  
- **Spanish** (es) - Español

All imported subscribers will use the selected language for notifications. If no language is specified, English will be used as the default.

---

## 📧 Email Notifications

### When Notifications Are Sent

Emails are automatically sent when:

1. **New Release Available**: A macOS update is published for subscribed versions
2. **Deadline Approaching**: Installation deadline is within the warning period (default: 7 days)
3. **Critical Updates**: Security updates with shorter installation windows

### Email Content Includes

- **Release Information**: Version number, release date, and key features
- **Personal Deadline**: User's specific installation deadline
- **Installation Instructions**: Step-by-step update guide
- **Self-Service Links**: Update preferences or unsubscribe options
- **Company Branding**: Customizable sender name and styling

### Customization Options

Administrators can modify:

- **Notification Timing**: Adjust warning periods in `.env`
- **Sender Information**: Company name and email address
- **Installation Deadlines**: Per-subscriber customization

---

## 📊 Dashboard & Analytics

### Super Admin Dashboard

- **System Overview**: Total subscribers, active admins, recent releases
- **User Performance**: Admin activity and subscriber management stats
- **Version Distribution**: Breakdown of subscribers by macOS version
- **Recent Activity**: Latest system actions and notifications

### Regular Admin Dashboard

- **Subscriber Summary**: Count and status of managed subscribers
- **Version Breakdown**: Distribution of subscribed macOS versions
- **Recent Actions**: Latest subscriber activity and notifications
- **Quick Actions**: Add subscribers, bulk import, view reports

### Reports Include

- **Notification History**: Track what emails were sent and when
- **Subscriber Activity**: Monitor user interactions and preferences
- **System Health**: Database status, email queue, and error tracking

---

## 🔧 Maintenance & Monitoring

### Daily Operations

The system automatically handles:

- **Release Monitoring**: Checks SOFA feed for new macOS versions
- **Notification Processing**: Sends emails for approaching deadlines
- **Database Cleanup**: Removes old logs and temporary data

### Manual Operations

#### Check for Updates Immediately

```bash
php artisan macos:check-updates
```

#### Test Email Configuration

```bash
php artisan macos:check-updates --test-email=admin@company.com
```

#### View System Status

```bash
php artisan macos:status
```

### Monitoring & Logs

#### Application Logs

```bash
tail -f storage/logs/laravel.log
```

#### Email Queue Status

```bash
php artisan queue:status
```

#### Queue Worker Management

Check if your queue worker is running:

```bash
# For supervisor
sudo supervisorctl status macos-notifier-worker:*

# For systemd
sudo systemctl status macos-notifier-queue

# View recent queue jobs
php artisan queue:failed
```

If emails aren't being sent, restart the queue worker:

```bash
# For supervisor
sudo supervisorctl restart macos-notifier-worker:*

# For systemd
sudo systemctl restart macos-notifier-queue

# Or manually restart
php artisan queue:restart
```

#### Database Health

The system uses SQLite by default, requiring minimal maintenance. For high-volume deployments, consider migrating to PostgreSQL or MySQL.

---

## 🔧 Troubleshooting

### Common Issues

1. **No releases found**: Check the SOFA feed URL and internet connectivity
2. **Emails not sending**: 
   - Verify mail configuration in `.env`
   - **Check if queue worker is running** (most common issue)
   - Review queue logs for failed jobs
3. **Scheduled tasks not running**: Ensure `schedule:work` is running or cron is configured
4. **Background jobs failing**: Check queue worker logs and restart if necessary

### Queue Troubleshooting

If notifications aren't being sent:

1. **Check queue worker status**:

   ```bash
   # View queue status
   php artisan queue:status
   
   # Check for failed jobs
   php artisan queue:failed
   ```

2. **Test email sending manually**:

   ```bash
   # Send a test notification
   php artisan macos:check-updates --test-email=your-email@company.com
   ```

3. **Restart queue worker**:

   ```bash
   php artisan queue:restart
   ```

4. **Check worker logs**:

   ```bash
   # Application logs
   tail -f storage/logs/laravel.log
   
   # Worker logs (if using supervisor)
   tail -f storage/logs/worker.log
   ```

### Logs

Check application logs for detailed error information:

```bash
tail -f storage/logs/laravel.log
```

---

## 🤝 Contributing

We welcome contributions from the MacAdmin community! Here's how you can help:

### 🐛 Bug Reports

Found a bug? Please [open an issue](https://github.com/wycomco/macos-update-notifier/issues) with:

- **Clear description** of the problem
- **Steps to reproduce** the issue
- **Expected vs actual behavior**
- **Environment details** (PHP version, server type, etc.)

### 💡 Feature Requests

Have ideas for improvements? We'd love to hear them:

- **Smart Customization Options** (Email templates?)
- **Email notification enhancements** (Slack, Teams, SMS integration)
- **Advanced scheduling options** (per-department deadlines)
- **Reporting features** (compliance reports, analytics)
- **Integration capabilities** (JAMF, Kandji, Munki, SimpleMDM)

### 🔧 Code Contributions

#### What We Need Most

1. **Email Template Improvements**: Better mobile responsiveness
2. **MDM Integration**: Sync the subscribed macOS versions with your MDM or inventory solution
3. **IDP Integration**: Connect with identity providers for smart updating of subscribers
4. **Internationalization**: Support for multiple languages
5. **Advanced Analytics**: Better reporting and insights
6. **Testing**: More test coverage for edge cases

#### Development Setup

```bash
# 1. Fork the repository
git clone https://github.com/wycomco/macos-update-notifier.git

# 2. Install development dependencies
composer install
npm install

# 3. Set up testing environment
cp .env.testing.example .env.testing
php artisan migrate --env=testing

# 4. Run tests
php artisan test

# 5. Start development server
npm run dev
php artisan serve
```

#### Coding Standards

- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding style
- Write tests for new features
- Update documentation for any changes
- Use meaningful commit messages

### 📝 Documentation

- **User Guides**: Help write installation instructions for different platforms
- **Video Tutorials**: Create setup walkthroughs for non-developers
- **Translation**: Help translate the interface to other languages

---

## 🆘 Support & Community

### Getting Help

- **💬 MacAdmins Slack**: Join `#macos-update-notifier` channel
- **📖 Documentation**: Check our [Wiki](https://github.com/wycomco/macos-update-notifier/wiki)
- **🐛 GitHub Issues**: [Technical problems and bugs](https://github.com/wycomco/macos-update-notifier/issues)
- **📚 Laravel Docs**: [Framework documentation](https://laravel.com/docs)

---

## 🔒 Security & Privacy

### Security Measures

- **🔐 Secure Authentication**: Magic link system eliminates password vulnerabilities
- **🛡️ Data Protection**: Minimal data collection, secure storage practices
- **🔍 Input Validation**: All user inputs are sanitized and validated
- **📝 Audit Logs**: Track administrative actions for compliance

### Privacy Considerations

- **Data Collection**: Only email addresses and update preferences
- **No Tracking**: No user behavior analytics or tracking cookies
- **Self-Hosted**: Full control over your subscriber data
- **GDPR Compliant**: Built-in unsubscribe and data deletion features

### Reporting Security Issues

Found a security vulnerability? Please email [support@wycomco.de](mailto:support@wycomco.de) directly rather than opening a public issue.

---

## 📄 License & Legal

This project is open source software licensed under the [MIT License](LICENSE.md).

### What This Means

- ✅ **Commercial Use**: Use in your organization freely
- ✅ **Modification**: Customize for your needs
- ✅ **Distribution**: Share with other MacAdmins
- ✅ **Private Use**: Use internally without restrictions

### Attribution

While not required, we appreciate attribution when you share or modify this project.

---

## 🙏 Acknowledgments

Special thanks to:

- **[MacAdmins Community](https://macadmins.org/)**: For inspiration and feedback
- **[SOFA Project](https://sofa.macadmins.io/)**: For the macOS release data feed
- **[Laravel Community](https://laravel.com/)**: For the amazing framework
- **Beta Testers**: MacAdmins who helped test and improve the system

---

*Made with ❤️ for the MacAdmin community*
