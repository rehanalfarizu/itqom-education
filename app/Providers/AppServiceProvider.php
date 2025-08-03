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
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
            
            // Completely disable facade caching for Heroku
            $this->app['config']->set('app.cache_facades', false);
            
            // Override AliasLoader to prevent file writing
            $aliasLoader = \Illuminate\Foundation\AliasLoader::getInstance();
            $reflection = new \ReflectionClass($aliasLoader);
            $cachePath = $reflection->getProperty('cachePath');
            $cachePath->setAccessible(true);
            $cachePath->setValue($aliasLoader, null); // Disable cache path completely

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
