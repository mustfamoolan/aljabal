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

        if (!file_exists($link) && function_exists('symlink') && !is_link($link)) {
            // Try to create symlink if possible
            if (!is_dir($link)) {
                try {
                    if (symlink($target, $link)) {
                        // Symlink created successfully
                    }
                } catch (\Exception $e) {
                    // Silently fail - user will need to create manually via SSH
                }
            }
        }
    }
}
