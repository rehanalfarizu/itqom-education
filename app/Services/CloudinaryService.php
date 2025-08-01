<?php

namespace App\Services;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\UploadedFile;

class CloudinaryService
{
    public function uploadImage(UploadedFile $file, string $folder = null): string
    {
        $folder = $folder ?? config('cloudinary.folder', 'itqom-platform');
        
        $result = Cloudinary::upload($file->getRealPath(), [
            'folder' => $folder,
            'resource_type' => 'image',
            'transformation' => [
                'quality' => 'auto',
                'fetch_format' => 'auto'
            ]
        ]);

        return $result->getSecurePath();
    }

    public function uploadImageWithPublicId(UploadedFile $file, string $publicId, string $folder = null): string
    {
        $folder = $folder ?? config('cloudinary.folder', 'itqom-platform');
        
        $result = Cloudinary::upload($file->getRealPath(), [
            'folder' => $folder,
            'public_id' => $publicId,
            'resource_type' => 'image',
            'overwrite' => true,
            'transformation' => [
                'quality' => 'auto',
                'fetch_format' => 'auto'
            ]
        ]);

        return $result->getSecurePath();
    }

    public function getOptimizedUrl(string $publicId, array $transformations = []): string
    {
        $defaultTransformations = [
            'quality' => 'auto',
            'fetch_format' => 'auto'
        ];

        $transformations = array_merge($defaultTransformations, $transformations);

        return Cloudinary::getUrl($publicId, $transformations);
    }

    public function deleteImage(string $publicId): bool
    {
        try {
            $result = Cloudinary::destroy($publicId);
            return isset($result['result']) && $result['result'] === 'ok';
        } catch (\Exception $e) {
            return false;
        }
    }
}
