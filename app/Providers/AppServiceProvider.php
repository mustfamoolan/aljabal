<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Create storage link if it doesn't exist and symlink function is available
        $link = public_path('storage');
        $target = storage_path('app/public');

        // If public/storage exists as a directory (not a symlink), we can't fix it automatically
        // because we need to delete it first, which requires file system permissions
        // This should be handled by the deployment script or manually via SSH
        if (!file_exists($link) && function_exists('symlink') && !is_link($link) && !is_dir($link)) {
            try {
                if (symlink($target, $link)) {
                    // Symlink created successfully
                }
            } catch (\Exception $e) {
                // Silently fail - user will need to create manually via SSH or use deploy.sh script
            }
        }
    }
}
