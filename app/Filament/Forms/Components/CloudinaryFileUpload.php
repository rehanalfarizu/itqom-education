<?php

namespace App\Filament\Forms\Components;

use App\Services\CloudinaryService;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Component;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CloudinaryFileUpload extends FileUpload
{
    protected string $view = 'filament.forms.components.cloudinary-file-upload';

    protected function setUp(): void
    {
        parent::setUp();

        $this->disk('cloudinary')
            ->directory('courses')
            ->acceptedFileTypes(['image/*'])
            ->image()
            ->imageEditor()
            ->imageEditorAspectRatios([
                '16:9',
                '4:3',
                '1:1',
            ])
            ->getUploadedFileNameForStorageUsing(
                fn (UploadedFile $file): string => (string) str($file->getClientOriginalName())
                    ->prepend(now()->timestamp . '_'),
            );
    }

    public function saveUploadedFileUsing(\Closure $callback): static
    {
        $this->saveUploadedFileUsing = $callback;

        return $this;
    }

    public function getUploadedFileUsing(\Closure $callback): static
    {
        $this->getUploadedFileUsing = $callback;

        return $this;
    }

    protected function saveUploadedFiles(): void
    {
        $cloudinaryService = new CloudinaryService();

        $files = array_filter($this->getState() ?? []);

        foreach ($files as $file) {
            if (!$file instanceof UploadedFile) {
                continue;
            }

            try {
                // Upload to Cloudinary
                $cloudinaryUrl = $cloudinaryService->uploadImage(
                    $file,
                    'courses'
                );

                // Store the Cloudinary URL instead of local path
                $this->state([$cloudinaryUrl]);

            } catch (\Exception $e) {
                // Fallback to local storage if Cloudinary fails
                $path = $file->store($this->getDirectory(), $this->getDiskName());
                $this->state([$path]);
            }
        }
    }
}
