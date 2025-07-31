<?php

return [
    'cloud' => env('CLOUDINARY_CLOUD_NAME'),
    'key' => env('CLOUDINARY_API_KEY'),  
    'secret' => env('CLOUDINARY_API_SECRET'),
    'secure' => true,
    'folder' => env('CLOUDINARY_FOLDER', ''),
];