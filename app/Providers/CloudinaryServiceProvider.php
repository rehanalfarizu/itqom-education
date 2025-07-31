<?php
// app/Providers/CloudinaryServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use CloudinaryLabs\CloudinaryLaravel\CloudinaryAdapter;
use Cloudinary\Cloudinary;

class CloudinaryServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        Storage::extend('cloudinary', function ($app, $config) {
            $adapter = new CloudinaryAdapter(
                new Cloudinary([
                    'cloud' => [
                        'cloud_name' => $config['cloud_name'],
                        'api_key' => $config['api_key'],
                        'api_secret' => $config['api_secret'],
                    ],
                    'url' => [
                        'secure' => $config['secure'] ?? true,
                    ]
                ])
            );

            return new Filesystem($adapter);
        });
    }
}
