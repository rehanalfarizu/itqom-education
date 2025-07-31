<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    App\Providers\RouteServiceProvider::class,
    CloudinaryLabs\CloudinaryLaravel\CloudinaryServiceProvider::class, // Pastikan ada ini
];
