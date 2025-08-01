<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Halaman utama (Vue mount di sini misalnya)
Route::get('/', function () {
    return view('homepage');
});

// Halaman tambahan (jika pakai blade)
Route::get('/produk', function () {
    return view('produk');
});

Route::get('/tentang', function () {
    return view('tentang');
});

// Route fallback untuk semua frontend SPA (Vue)
Route::get('/{any}', function () {
    return view('homepage');
})->where('any', '^(?!api|admin|livewire).*$');

// Auth (kalau ini ditujukan untuk frontend, lebih baik dipindah ke api.php)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Comment out admin routes yang konflik dengan Filament
// Route::prefix('admin')->middleware('auth')->name('admin.')->group(function () {
//     Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
//     Route::resource('users', UserProfileController::class);
// });

Route::get('/login', function () {
    return response()->json([
        'message' => 'This is an API microservice. Please use frontend login.'
    ]);
})->name('login');

Route::middleware('auth:sanctum')->get('/debug/quick-fix', function(Request $request) {
    $user = $request->user();

    // Check semua data mentah
    return response()->json([
        'user_id' => $user->id,
        'payments' => DB::table('payments')->where('user_profile_id', $user->id)->get(),
        'courses' => DB::table('courses')->get(),
        'course_desc_table_exists' => Schema::hasTable('course_description'),
        'course_descs_table_exists' => Schema::hasTable('course_descriptions'),
    ]);
});
