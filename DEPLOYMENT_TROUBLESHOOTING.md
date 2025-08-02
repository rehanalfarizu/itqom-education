# Troubleshooting Deployment Issues

## Problem Identified
Setelah deployment ke Heroku, tampilan Course.vue rusak dan tidak sesuai dengan code.

## Root Causes Found

### 1. **Duplikasi @vite Directive di homepage.blade.php**
- **Problem**: File `homepage.blade.php` memiliki duplikasi `@vite(['resources/css/app.css', 'resources/js/app.js'])`
- **Location**: Line 12 dan line 34 dalam kondisi production
- **Impact**: Menyebabkan asset dimuat dua kali, potentially causing conflicts
- **Fix**: Menghapus duplikasi di development section (line 34)

### 2. **Database Seeding Issues**
- **Problem**: CourseDescriptionSeeder memiliki method yang tidak ada `createOrUpdateCourse()`
- **Impact**: Hanya 1 course tersedia di database production
- **Fix**: 
  - Memperbaiki seeder dengan menghapus method yang error
  - Menambahkan 4 course data tambahan
  - Total courses sekarang: 5

### 3. **Environment Configuration**
- **Status**: ✅ Sudah benar
  - `APP_ENV=production`
  - `APP_DEBUG=false`
  - URL production sudah sesuai

### 4. **Asset Building**
- **Status**: ✅ Sudah diperbaiki
  - Manifest file ada di `public/build/manifest.json`
  - Assets ter-build dengan benar

## Files Fixed

### 1. `resources/views/homepage.blade.php`
```blade
// BEFORE (Error - duplikasi)
@if(app()->environment('production'))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@else
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])  // <- DUPLIKASI
@endif

// AFTER (Fixed)
@if(app()->environment('production'))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@else
    <script src="https://cdn.tailwindcss.com"></script>
@endif
```

### 2. `database/seeders/CourseDescriptionSeeder.php`
```php
// BEFORE (Error)
$cd->createOrUpdateCourse(); // Method tidak ada

// AFTER (Fixed)
// Ditambahkan 4 course data tambahan untuk testing
CourseDescription::create([...]);
```

## Deployment Checklist

Untuk mencegah masalah serupa di masa depan:

### Pre-Deployment
- [ ] Check duplikasi @vite directives di template files
- [ ] Pastikan semua seeder berjalan tanpa error
- [ ] Verify environment variables di Heroku
- [ ] Test API endpoints secara lokal

### Build Process
- [ ] Run `npm run production`
- [ ] Verify manifest.json terbuat di `public/build/`
- [ ] Check asset files di `public/build/assets/`

### Post-Deployment
- [ ] Run database seeders di production
- [ ] Clear Laravel caches
- [ ] Test frontend functionality
- [ ] Check browser console untuk errors

## Commands untuk Deployment

```bash
# 1. Build assets
npm run production

# 2. Clear caches
php artisan cache:clear
php artisan view:clear
php artisan config:clear

# 3. Seed database (jika diperlukan)
php artisan db:seed --class=CourseDescriptionSeeder

# 4. Push to Heroku
git add .
git commit -m "Fix deployment issues"
git push heroku main
```

## Monitoring

Setelah deployment, monitor:
- Browser console untuk JavaScript errors
- Network tab untuk failed asset requests
- Laravel logs untuk backend errors
- Database content untuk missing data
