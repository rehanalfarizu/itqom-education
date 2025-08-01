<?php

return [
    'cloud' => [
        'cloud_name' => env('CLOUDINARY_CLOUD_NAME', 'hltd67bzw'),
        'api_key'    => env('CLOUDINARY_API_KEY', '889987677545791'),
        'api_secret' => env('CLOUDINARY_API_SECRET', 's9rw45O-qjDY3lpNgmY8_RP0uN8'),
        'url' => [
            'secure' => true
        ]
    ],
    
    'folder' => env('CLOUDINARY_FOLDER', 'itqom-platform'),
];