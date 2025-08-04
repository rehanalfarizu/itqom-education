<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\CloudinaryConfigurationServiceProvider::class, // Configuration provider
    App\Providers\CustomCloudinaryServiceProvider::class, // Our custom provider INSTEAD of original
    App\Providers\Filament\AdminPanelProvider::class,
    App\Providers\RouteServiceProvider::class,
    // CloudinaryLabs\CloudinaryLaravel\CloudinaryServiceProvider::class, // Disabled - using our custom one
];
