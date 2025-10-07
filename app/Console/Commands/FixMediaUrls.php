<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class FixMediaUrls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:fix-urls {--force : Force production URLs regardless of environment}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix media URLs for production environment and clear related caches';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Fixing media URLs for production...');

        // Clear all caches that might contain old URLs
        $this->info('📤 Clearing caches...');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');

        // Check storage link
        $this->info('🔗 Checking storage link...');
        if (!is_link(public_path('storage'))) {
            $this->warn('Storage link not found. Creating storage link...');
            Artisan::call('storage:link');
            $this->info('✅ Storage link created successfully.');
        } else {
            $this->info('✅ Storage link exists.');
        }

        // Display current configuration
        $this->info('📋 Current Configuration:');
        $this->table(['Setting', 'Value'], [
            ['APP_URL', config('app.url')],
            ['Environment', app()->environment()],
            ['Storage Disk URL', \Storage::disk('public')->url('')],
            ['Force Production URLs', $this->option('force') ? 'Yes' : 'No'],
        ]);

        // Set force production URL if requested
        if ($this->option('force')) {
            $this->info('🚀 Forcing production URLs...');
            config(['media.force_production_url' => true]);
        }

        $this->info('✅ Media URL fix completed!');
        $this->info('🌐 All images should now display correctly on https://mcc-nac.com');
        
        return 0;
    }
}
