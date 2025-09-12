<?php

namespace App\Jobs;

use App\Models\Release;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class FetchMacOSReleases implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $url = config('macos_notifier.sofa_feed_url');
            
            Log::info('Fetching macOS releases from SOFA feed', ['url' => $url]);
            
            $response = Http::timeout(30)->get($url);
            
            if (!$response->successful()) {
                Log::error('Failed to fetch SOFA feed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return;
            }
            
            $data = $response->json();
            
            if (!isset($data['OSVersions'])) {
                Log::error('Invalid SOFA feed format - missing OSVersions');
                return;
            }
            
            $this->processReleases($data['OSVersions']);
            
            Log::info('Successfully processed macOS releases from SOFA feed');
            
        } catch (\Exception $e) {
            Log::error('Error fetching macOS releases', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Process the releases from the SOFA feed
     */
    private function processReleases(array $osVersions): void
    {
        foreach ($osVersions as $version) {
            if (!isset($version['Latest'])) {
                continue;
            }
            
            $latest = $version['Latest'];
            
            // Extract major version (e.g., "macOS 14" from "macOS Sonoma 14.6.1")
            $majorVersion = $this->extractMajorVersion($latest);
            
            if (!$majorVersion) {
                continue;
            }
            
            $releaseDate = $this->parseReleaseDate($latest);
            
            if (!$releaseDate) {
                continue;
            }
            
            // Create or update the release record
            Release::updateOrCreate(
                [
                    'major_version' => $majorVersion,
                    'version' => $latest['ProductVersion'] ?? 'Unknown'
                ],
                [
                    'release_date' => $releaseDate,
                    'raw_json' => $latest
                ]
            );
        }
    }

    /**
     * Extract major version from the release data
     */
    private function extractMajorVersion(array $latest): ?string
    {
        $productVersion = $latest['ProductVersion'] ?? '';
        
        if (preg_match('/^(\d+)\./', $productVersion, $matches)) {
            $majorNum = (int) $matches[1];
            return "macOS {$majorNum}";
        }
        
        return null;
    }

    /**
     * Parse the release date from various possible fields
     */
    private function parseReleaseDate(array $latest): ?Carbon
    {
        // Try different possible date fields from SOFA feed
        $dateFields = ['ReleaseDate', 'PostDate', 'BuildDate'];
        
        foreach ($dateFields as $field) {
            if (isset($latest[$field])) {
                try {
                    return Carbon::parse($latest[$field]);
                } catch (\Exception $e) {
                    Log::warning("Failed to parse date from field {$field}", [
                        'value' => $latest[$field],
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
        
        // Fallback to current date if no valid date found
        Log::warning('No valid release date found, using current date', ['data' => $latest]);
        return Carbon::now();
    }
}
