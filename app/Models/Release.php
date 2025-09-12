<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Release extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'major_version',
        'version',
        'release_date',
        'raw_json',
    ];

    protected $casts = [
        'release_date' => 'datetime',
        'raw_json' => 'array',
    ];

    /**
     * Get the latest release for a specific major version
     */
    public static function getLatestForMajorVersion(string $majorVersion): ?self
    {
        return static::where('major_version', $majorVersion)
            ->orderByDesc('release_date')
            ->orderByDesc('version')
            ->first();
    }

    /**
     * Check if this release is within deadline for a subscriber
     */
    public function isWithinDeadline(int $daysToInstall, int $warningDays = 2): bool
    {
        $deadline = $this->release_date->copy()->addDays($daysToInstall);
        $warningDate = $deadline->copy()->subDays($warningDays);
        
        return Carbon::now()->gte($warningDate) && Carbon::now()->lte($deadline);
    }

    /**
     * Get the deadline date for this release
     */
    public function getDeadlineDate(int $daysToInstall): Carbon
    {
        return $this->release_date->copy()->addDays($daysToInstall);
    }
}
