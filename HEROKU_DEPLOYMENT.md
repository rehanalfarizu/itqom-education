# Panduan Deployment Heroku dengan Hybrid Storage

## Penjelasan Hybrid Storage

Sistem hybrid storage memungkinkan aplikasi untuk:
- **Development**: Menggunakan local storage untuk kecepatan dan kemudahan development
- **Production (Heroku)**: Otomatis beralih ke Cloudinary untuk persistensi data
- **Fallback**: Sistem fallback otomatis jika salah satu service tidak tersedia

## Environment Variables untuk Heroku

### Required Variables

```bash
# Database (JawsDB MySQL sudah otomatis ter-set)
JAWSDB_URL=mysql://username:password@hostname:port/database

# Cloudinary Configuration
CLOUDINARY_CLOUD_NAME=your_cloud_name
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret

# Laravel Configuration
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:your-app-key
APP_URL=https://your-app.herokuapp.com

# Storage Configuration (untuk hybrid)
FILESYSTEM_DISK=public
```

### Optional Variables (untuk fine-tuning)

```bash
# Force Cloudinary usage (default: auto-detect Heroku)
FORCE_CLOUDINARY=true

# Cloudinary upload options
CLOUDINARY_FOLDER=production/courses
CLOUDINARY_QUALITY=auto
CLOUDINARY_FETCH_FORMAT=auto
```

## Deployment Steps

### 1. Setup Cloudinary Account

1. Daftar di [Cloudinary](https://cloudinary.com/users/register/free)
2. Dapatkan Cloud Name, API Key, dan API Secret dari dashboard
3. Setup upload presets (optional, untuk kontrol lebih)

### 2. Configure Heroku App

```bash
# Login ke Heroku
heroku login

# Set environment variables
heroku config:set CLOUDINARY_CLOUD_NAME=your_cloud_name -a your-app-name
heroku config:set CLOUDINARY_API_KEY=your_api_key -a your-app-name
heroku config:set CLOUDINARY_API_SECRET=your_api_secret -a your-app-name

# Set Laravel configurations
heroku config:set APP_ENV=production -a your-app-name
heroku config:set APP_DEBUG=false -a your-app-name
heroku config:set APP_KEY=$(php artisan key:generate --show) -a your-app-name
```

### 3. Database Setup (JawsDB)

JawsDB sudah otomatis ter-configure, tapi pastikan migrasi berjalan:

```bash
# Di local, test koneksi database
php artisan migrate --env=production

# Atau setup di Heroku
heroku run php artisan migrate -a your-app-name
```

### 4. Deploy Application

```bash
# Push ke Heroku
git add .
git commit -m "Add hybrid storage system for Heroku"
git push heroku main

# Run post-deployment commands
heroku run php artisan config:cache -a your-app-name
heroku run php artisan route:cache -a your-app-name
heroku run php artisan view:cache -a your-app-name
```

## Cara Kerja Hybrid System

### Development Environment
```php
// Local development - menggunakan storage/app/public
$service = new CloudinaryService();
$result = $service->uploadImageHybrid($file);
// Result: { "success": true, "path": "courses/course_1234567890_image.jpg", "storage_type": "local" }
```

### Production Environment (Heroku)
```php
// Heroku production - menggunakan Cloudinary
$service = new CloudinaryService();
$result = $service->uploadImageHybrid($file);
// Result: { "success": true, "path": "courses/course_1234567890_image", "storage_type": "cloudinary", "backup_path": "..." }
```

### Automatic Fallback
```php
// Jika Cloudinary down di production
$service = new CloudinaryService();
$result = $service->uploadImageHybrid($file);
// Result: { "success": true, "path": "courses/course_1234567890_image.jpg", "storage_type": "local", "warning": "Cloudinary unavailable" }
```

## Testing Deployment

### 1. Test Upload di Admin Panel

1. Login ke `/admin`
2. Buat course baru dengan upload gambar
3. Cek log untuk memastikan upload berhasil:

```bash
heroku logs --tail -a your-app-name
```

### 2. Test Image Display

1. Lihat course di frontend
2. Inspect element untuk cek URL gambar
3. Pastikan gambar load dengan benar

### 3. Test Fallback Mechanism

Temporary disable Cloudinary untuk test fallback:

```bash
# Disable Cloudinary temporarily
heroku config:unset CLOUDINARY_API_KEY -a your-app-name

# Test upload (should fallback to local)
# Re-enable after test
heroku config:set CLOUDINARY_API_KEY=your_api_key -a your-app-name
```

## Monitoring & Maintenance

### Logs Monitoring

```bash
# Real-time logs
heroku logs --tail -a your-app-name

# Filter untuk storage logs
heroku logs --tail -a your-app-name | grep -i cloudinary
heroku logs --tail -a your-app-name | grep -i "hybrid upload"
```

### Storage Usage Monitoring

1. **Cloudinary Dashboard**: Cek usage di dashboard Cloudinary
2. **Heroku Metrics**: Monitor app performance di Heroku dashboard
3. **Database Usage**: Monitor JawsDB usage

### Troubleshooting

#### Image tidak muncul
```bash
# Cek environment variables
heroku config -a your-app-name

# Cek log upload
heroku logs --tail -a your-app-name | grep "image\|upload\|cloudinary"
```

#### Upload gagal
```bash
# Test Cloudinary connection
heroku run php artisan tinker -a your-app-name
# Di tinker:
# $service = app(\App\Services\CloudinaryService::class);
# dd($service->shouldUseCloudinary());
```

#### Database connection error
```bash
# Cek JAWSDB_URL
heroku config:get JAWSDB_URL -a your-app-name

# Test database connection
heroku run php artisan migrate:status -a your-app-name
```

## Cost Optimization

### Cloudinary Free Tier
- 25GB storage
- 25GB bandwidth/month
- 1000 transformations/month

### Tips Hemat Kuota
1. Optimize gambar sebelum upload
2. Gunakan transformasi Cloudinary secara efisien
3. Set quality='auto' untuk kompresi otomatis
4. Gunakan fetch_format='auto' untuk format optimal

### Monitoring Usage
```bash
# Cek usage via API
curl -X GET \
  https://api.cloudinary.com/v1_1/your_cloud_name/usage \
  -u your_api_key:your_api_secret
```

## Backup Strategy

### Local Backup (Development)
```php
// Backup semua gambar local ke Cloudinary
php artisan make:command BackupImagesToCloudinary
```

### Cloudinary Backup (Production)
```php
// Download semua gambar dari Cloudinary (untuk backup local)
php artisan make:command DownloadCloudinaryImages
```

Sistem hybrid ini memberikan fleksibilitas maksimum dengan cost optimization untuk deployment Heroku Students plan.
