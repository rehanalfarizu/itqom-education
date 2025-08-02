# Local Storage Integration Guide

## Overview
Sistem penyimpanan gambar telah dikonfigurasi untuk menggunakan folder lokal alih-alih URL Cloudinary, dengan opsi untuk tetap menggunakan Cloudinary sebagai CDN jika diperlukan.

## 🔧 Konfigurasi

### 1. Storage Configuration
Sistem menggunakan `storage/app/public/courses` untuk menyimpan gambar course.

### 2. Laravel Storage Setup

#### config/filesystems.php
```php
'public' => [
    'driver' => 'local',
    'root' => storage_path('app/public'),
    'url' => env('APP_URL').'/storage',
    'visibility' => 'public',
    'throw' => false,
],
```

### 3. Symbolic Link
Pastikan symbolic link sudah dibuat:
```bash
php artisan storage:link
```

## 📁 File Structure

### Storage Structure
```
storage/app/public/
├── courses/
│   ├── course_1659123456_image1.jpg
│   ├── course_1659123457_image2.png
│   └── course_1659123458_image3.webp
└── ...
```

### Database Storage
Gambar disimpan sebagai path relatif di database:
```sql
-- courses table
image: "courses/course_1659123456_image1.jpg"
```

## 🚀 Features

### 1. Filament Admin Panel
- **Upload Interface**: Drag & drop file upload ke folder lokal
- **Image Editor**: Built-in image cropping dan resizing
- **Auto Storage**: Otomatis simpan ke `storage/app/public/courses`
- **File Management**: Otomatis hapus file lama saat update

### 2. API Endpoints
```php
// Get all courses dengan local image URLs
GET /api/courses

// Response format:
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Course Title",
      "image": "/storage/courses/course_1659123456_image1.jpg",
      "thumbnail": "/storage/courses/course_1659123456_image1.jpg"
    }
  ]
}
```

### 3. Frontend Components

#### CloudinaryImage Component (Updated for Local Storage)
```vue
<CloudinaryImage
  :src="course.image"
  :alt="course.title"
  :width="400"
  :height="300"
  fallback="/images/default-course.jpg"
  class="w-full h-full object-cover"
/>
```

**Props:**
- `src`: Path gambar lokal (e.g., "courses/image.jpg")
- `fallback`: Fallback image jika error
- `width/height`: Dimensi (tidak digunakan untuk local storage)

## 💡 Usage Examples

### 1. Upload Course Image via Filament
```php
// File akan disimpan ke: storage/app/public/courses/course_{timestamp}_{filename}
// Database akan menyimpan: "courses/course_{timestamp}_{filename}"
```

### 2. Display Course Image in Vue.js
```vue
<template>
  <CloudinaryImage
    :src="course.image"
    fallback="/images/default-course.jpg"
    class="rounded-lg shadow-lg"
  />
</template>
```

### 3. Get Image URLs in Laravel
```php
$course = Course::find(1);
$imageUrl = $course->image_url; // Returns: /storage/courses/filename.jpg
$thumbnailUrl = $course->thumbnail_url; // Same as image_url for local storage
```

## 🔍 Path Resolution

### Image Path Processing
```php
// Database: "courses/course_1659123456_image1.jpg"
// Accessor returns: "/storage/courses/course_1659123456_image1.jpg"
// Browser access: "https://yourapp.com/storage/courses/course_1659123456_image1.jpg"
```

### Frontend Path Handling
```javascript
// Input: "courses/course_1659123456_image1.jpg"
// CloudinaryImage component returns: "/storage/courses/course_1659123456_image1.jpg"
```

## 🛡️ Error Handling

### Backend
```php
// Automatic file cleanup on update
if ($this->record->image && $this->record->image !== $data['image']) {
    if (Storage::disk('public')->exists($this->record->image)) {
        Storage::disk('public')->delete($this->record->image);
    }
}
```

### Frontend
```vue
<CloudinaryImage
  :src="course.image"
  fallback="/images/default-course.jpg"
  @error="handleImageError"
  @load="handleImageLoad"
/>
```

## ⚡ Performance Considerations

### 1. File Optimization
- Filament otomatis compress gambar saat upload
- Support WebP format untuk ukuran file lebih kecil
- Image editor built-in untuk cropping

### 2. Lazy Loading
```vue
<CloudinaryImage
  :src="course.image"
  :lazy="true"
  class="w-full h-40 object-cover"
/>
```

### 3. Caching
- Browser caching otomatis untuk file statis
- Nginx/Apache dapat dikonfigurasi untuk expires headers

## 🔧 File Management

### Automatic Cleanup
```php
// Di EditCourse.php
protected function mutateFormDataBeforeSave(array $data): array
{
    if (isset($data['image']) && $this->record->image !== $data['image']) {
        // Delete old file
        Storage::disk('public')->delete($this->record->image);
    }
    return $data;
}
```

### Manual Cleanup
```bash
# Remove unused course images
find storage/app/public/courses -name "*.jpg" -mtime +30 -delete
```

## 📱 Mobile & Responsive

### Image Sizing
```php
// Filament FileUpload configuration
FileUpload::make('image')
    ->imageEditor()
    ->imageEditorAspectRatios(['16:9', '4:3', '1:1'])
    ->maxSize(5120) // 5MB
```

### Responsive Display
```vue
<CloudinaryImage
  :src="course.image"
  class="w-full h-auto md:h-40 object-cover"
/>
```

## 🚀 Deployment Notes

### Production Environment
1. Pastikan folder `storage/app/public/courses` writable
2. Symbolic link `php artisan storage:link` sudah dibuat
3. Web server serve file dari `/storage` path

### Nginx Configuration
```nginx
location /storage {
    alias /path/to/your/app/storage/app/public;
    expires 1y;
    add_header Cache-Control public;
}
```

### Backup Strategy
```bash
# Backup course images
tar -czf course_images_backup.tar.gz storage/app/public/courses/
```

## 🔄 Migration from URLs

### Convert Existing URL Data
```php
// Artisan command to convert existing Cloudinary URLs to local paths
Course::whereNotNull('image')
    ->where('image', 'like', 'http%')
    ->chunk(100, function ($courses) {
        foreach ($courses as $course) {
            // Download and save locally if needed
            // Update path in database
        }
    });
```
