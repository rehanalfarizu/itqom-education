<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Midtrans\Config as MidtransConfig;
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
        // Force HTTPS in production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Initialize Cloudinary early
        \App\Services\CloudinaryService::ensureInitialized();

        if ($this->app->environment('production')) {
            // Configure Midtrans
            try {
                MidtransConfig::$serverKey = config('midtrans.server_key');
                MidtransConfig::$isProduction = config('midtrans.is_production');
                MidtransConfig::$isSanitized = true;
                MidtransConfig::$is3ds = true;
            } catch (\Exception $e) {
                Log::warning('Midtrans configuration failed: ' . $e->getMessage());
            }

            // Database query logging (only in production for debugging)
            if (config('app.debug', false)) {
                DB::listen(function($query) {
                    Log::info($query->sql, $query->bindings);
                });
            }
        }

        // Register CloudinaryService as singleton
        $this->app->singleton(\App\Services\CloudinaryService::class, function ($app) {
            return new \App\Services\CloudinaryService();
        });
    }


}
