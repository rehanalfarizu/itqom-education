# CORS Issue Fix Documentation

## Problem
The application was experiencing CORS errors in production where the frontend was trying to access Vite development server at `http://localhost:5173` instead of using the built production assets.

**Error messages:**
```
Access to script at 'http://localhost:5173/@vite/client' from origin 'https://itqom-platform.tech' has been blocked by CORS policy: No 'Access-Control-Allow-Origin' header is present on the requested resource.
```

## Root Cause
1. **Environment Detection**: The application was set to production (`APP_ENV=production`) but the Vite helper was not finding the correct manifest file.
2. **Manifest Location**: Vite 7.x generates manifest at `public/build/.vite/manifest.json` but Laravel expects it at `public/build/manifest.json`.
3. **Build Configuration**: The Vite configuration needed adjustment for proper manifest generation in production.

## Solution Applied

### 1. Fixed Vite Configuration (`vite.config.js`)
```javascript
export default defineConfig({
  // ... other config
  build: {
    manifest: 'manifest.json', // Explicit manifest filename
    outDir: 'public/build',
    // ... other build config
  },
  server: {
    host: 'localhost',
    port: 5173,
    cors: {
      origin: ['http://localhost', 'https://itqom-platform.tech', 'https://itqom-platform-aa0ffce6a276.herokuapp.com'],
      credentials: true
    },
    hmr: {
      host: 'localhost',
      port: 5173,
    },
  },
  // ... rest of config
});
```

### 2. Fixed Tailwind CSS Issues
- Replaced `@apply` directives with native CSS in `Navbar.vue` component
- This resolved build errors that were preventing proper asset compilation

### 3. Rebuilt Production Assets
```bash
npm run production
```

### 4. Cleared Laravel Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## Verification
After the fix, the Vite helper correctly generates production asset URLs:
```php
// Before (causing CORS errors):
http://localhost:5173/@vite/client
http://localhost:5173/resources/js/app.js

// After (working correctly):
https://itqom-platform-aa0ffce6a276.herokuapp.com/build/assets/app-BMCmOGt_.js
https://itqom-platform-aa0ffce6a276.herokuapp.com/build/assets/app-Ck3XWLXv.css
```

## Prevention for Future
1. Use the provided deployment scripts (`deploy-production.bat` or `deploy-production.sh`)
2. Always run `npm run production` before deploying to production
3. Ensure `APP_ENV=production` in production environment
4. Verify manifest file exists at `public/build/manifest.json` after building

## Files Modified
- `vite.config.js` - Updated build and server configuration
- `resources/js/components/Navbar.vue` - Replaced @apply with native CSS
- Added deployment scripts for future use

## Environment Variables
Ensure these are set correctly in production:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-production-domain.com
```
