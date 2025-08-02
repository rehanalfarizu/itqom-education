# Test Image Handling

## Quick Test Commands

### 1. Test Storage Link
```bash
# Check if storage link exists
ls -la public/storage
# Should show symlink to storage/app/public
```

### 2. Test Course Image Path
```php
// In tinker
php artisan tinker

// Test Course model
$course = new App\Models\Course();
$course->image = 'courses/test_image.jpg';
echo $course->image_url;
// Should return: /storage/courses/test_image.jpg

$course->image = 'courses/sample';
echo $course->image_url;
// Should return: /storage/courses/sample
```

### 3. Test File Upload
1. Go to `/admin/courses`
2. Create new course
3. Upload image
4. Check `storage/app/public/courses/` for uploaded file
5. Check database `courses` table for saved path

### 4. Test Frontend Display
1. Upload course with image via admin
2. Visit course page in frontend
3. Check browser developer tools for image URL
4. Should show `/storage/courses/filename.jpg`

## Expected Behavior

### File Upload Process
1. **Upload**: File uploaded to `storage/app/public/courses/`
2. **Database**: Path saved as `courses/filename.jpg`
3. **Display**: Accessed via `/storage/courses/filename.jpg`
4. **Fallback**: Default image if file not found

### Path Resolution
```
Database: "courses/course_1691234567_image.jpg"
Model Accessor: "/storage/courses/course_1691234567_image.jpg"
Browser URL: "https://yourapp.com/storage/courses/course_1691234567_image.jpg"
```
