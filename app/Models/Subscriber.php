<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Subscriber extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'email',
        'language',
        'subscribed_versions',
        'days_to_install',
        'admin_id',
        'unsubscribe_token',
        'unsubscribed_at',
        'is_subscribed',
    ];

    protected $casts = [
        'subscribed_versions' => 'array',
        'days_to_install' => 'integer',
        'unsubscribed_at' => 'datetime',
        'is_subscribed' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($subscriber) {
            if (!$subscriber->unsubscribe_token) {
                $subscriber->unsubscribe_token = Str::random(32);
            }
            if (is_null($subscriber->is_subscribed)) {
                $subscriber->is_subscribed = true;
            }
            if (!$subscriber->language) {
                $subscriber->language = config('subscriber_languages.default', 'en');
            }
        });
        
        static::created(function ($subscriber) {
            // Log the subscription action when a subscriber is created
            $subscriber->actions()->create([
                'action' => 'subscribed',
                'data' => [
                    'subscribed_at' => $subscriber->created_at,
                    'subscribed_versions' => $subscriber->subscribed_versions,
                ],
            ]);
        });
    }

    /**
     * Get the admin that manages this subscriber
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get all actions for this subscriber
     */
    public function actions(): HasMany
    {
        return $this->hasMany(SubscriberAction::class);
    }

    /**
     * Check if the subscriber is subscribed to a specific major version
     */
    public function isSubscribedTo(string $majorVersion): bool
    {
        return in_array($majorVersion, $this->subscribed_versions ?? []);
    }

    /**
     * Get the deadline date for a given release date
     */
    public function getDeadlineDate(\Carbon\Carbon $releaseDate): \Carbon\Carbon
    {
        return $releaseDate->copy()->addDays($this->days_to_install);
    }

    /**
     * Check if subscriber is active (not unsubscribed)
     */
    public function isActive(): bool
    {
        return is_null($this->unsubscribed_at);
    }

    /**
     * Unsubscribe the subscriber
     */
    public function unsubscribe(): void
    {
        $this->update([
            'unsubscribed_at' => now(),
            'is_subscribed' => false
        ]);
        
        $this->actions()->create([
            'action' => 'unsubscribed',
            'data' => ['unsubscribed_at' => now()],
        ]);
    }

    /**
     * Re-enable/resubscribe the subscriber
     * Note: This should only be done with explicit user consent to comply with email regulations
     */
    public function resubscribe(User $admin, string $consentMethod, ?string $consentNotes = null): void
    {
        $this->update([
            'unsubscribed_at' => null,
            'is_subscribed' => true
        ]);
        
        // Create comprehensive audit log
        $details = [
            'admin_name' => $admin->name,
            'admin_email' => $admin->email,
            'admin_id' => $admin->id,
            'consent_method' => $consentMethod,
            'consent_notes' => $consentNotes,
            'resubscribed_at' => now()->toDateTimeString(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];
        
        $this->actions()->create([
            'action' => 'resubscribed',
            'data' => $details,
        ]);
    }

    /**
     * Update subscribed versions and log the action
     */
    public function updateVersions(array $versions): void
    {
        $oldVersions = $this->subscribed_versions;
        $this->update(['subscribed_versions' => $versions]);
        
        $this->actions()->create([
            'action' => 'version_changed',
            'data' => [
                'old_versions' => $oldVersions,
                'new_versions' => $versions,
            ],
        ]);
    }

    /**
     * Log notification sent
     */
    public function logNotificationSent(Release $release): void
    {
        $this->actions()->create([
            'action' => 'notification_sent',
            'data' => [
                'release_id' => $release->id,
                'version' => $release->version,
                'major_version' => $release->major_version,
            ],
        ]);
    }

    /**
     * Generate unsubscribe URL
     */
    public function getUnsubscribeUrl(): string
    {
        return route('public.unsubscribe', $this->unsubscribe_token);
    }

    /**
     * Generate version change URL
     */
    public function getVersionChangeUrl(): string
    {
        return route('public.version-change', $this->unsubscribe_token);
    }

    /**
     * Generate language change URL
     */
    public function getLanguageChangeUrl(): string
    {
        return route('public.language-change', $this->unsubscribe_token);
    }

    /**
     * Get the supported languages
     */
    public static function getSupportedLanguages(): array
    {
        return config('subscriber_languages.supported', []);
    }

    /**
     * Get the language display name
     */
    public function getLanguageDisplayName(): string
    {
        $languages = self::getSupportedLanguages();
        return $languages[$this->language]['name'] ?? 'Unknown';
    }

    /**
     * Get the language flag emoji
     */
    public function getLanguageFlag(): string
    {
        $languages = self::getSupportedLanguages();
        return $languages[$this->language]['flag'] ?? '';
    }

    /**
     * Get the language display name with flag
     */
    public function getLanguageDisplayNameWithFlag(): string
    {
        $languages = self::getSupportedLanguages();
        if (isset($languages[$this->language])) {
            return $languages[$this->language]['flag'] . ' ' . $languages[$this->language]['name'];
        }
        return $this->language ?? 'Unknown';
    }

    /**
     * Update the subscriber's language and log the action
     */
    public function updateLanguage(string $newLanguage): void
    {
        $oldLanguage = $this->language;
        $this->update(['language' => $newLanguage]);
        
        $this->actions()->create([
            'action' => 'language_changed',
            'data' => [
                'old_language' => $oldLanguage,
                'new_language' => $newLanguage,
            ],
        ]);
    }

    /**
     * Scope to get only active subscribers
     */
    public function scopeActive($query)
    {
        return $query->where('is_subscribed', true);
    }
}
