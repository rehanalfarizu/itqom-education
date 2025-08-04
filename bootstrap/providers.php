<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\CloudinaryConfigurationServiceProvider::class, // Add our custom provider first
    App\Providers\Filament\AdminPanelProvider::class,
    App\Providers\RouteServiceProvider::class,
    CloudinaryLabs\CloudinaryLaravel\CloudinaryServiceProvider::class, // Pastikan ada ini
];
