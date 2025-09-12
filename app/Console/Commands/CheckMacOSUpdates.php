<?php

namespace App\Console\Commands;

use App\Jobs\FetchMacOSReleases;
use App\Jobs\ProcessNotifications;
use Illuminate\Console\Command;

class CheckMacOSUpdates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'macos:check-updates {--fetch-only : Only fetch releases without processing notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for macOS updates and process notifications for subscribers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting macOS update check...');
        
        // First, fetch the latest releases
        $this->info('Fetching latest macOS releases from SOFA feed...');
        
        try {
            FetchMacOSReleases::dispatchSync();
            $this->info('✓ Successfully fetched macOS releases');
        } catch (\Exception $e) {
            $this->error('✗ Failed to fetch macOS releases: ' . $e->getMessage());
            return 1;
        }
        
        // If fetch-only option is set, skip notifications
        if ($this->option('fetch-only')) {
            $this->info('Fetch-only mode: Skipping notification processing');
            return 0;
        }
        
        // Process notifications for subscribers
        $this->info('Processing notifications for subscribers...');
        
        try {
            ProcessNotifications::dispatchSync();
            $this->info('✓ Successfully processed notifications');
        } catch (\Exception $e) {
            $this->error('✗ Failed to process notifications: ' . $e->getMessage());
            return 1;
        }
        
        $this->info('macOS update check completed successfully!');
        return 0;
    }
}
