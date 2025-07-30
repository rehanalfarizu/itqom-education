<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log; // ← TAMBAHKAN INI
use Illuminate\Support\Facades\Hash;

class UserProfileController extends Controller
{
    public function show()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                Log::error('No authenticated user found');
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            Log::info('Authenticated user:', ['user_id' => $user->id, 'email' => $user->email]);

            // Cari atau buat profil pengguna
            $profile = $user->profile;

            // Jika belum ada profil, buat profil baru
            if (!$profile) {
                Log::info('Creating new profile for user:', ['user_id' => $user->id]);

                $profile = UserProfile::create([
                    'user_id' => $user->id,
                    'fullname' => $user->name,
                    'email' => $user->email,
                    'level' => 1,
                    'progress' => 0,
                    'hobbies' => [],
                    'badges' => []
                ]);

                Log::info('Profile created:', ['profile_id' => $profile->id]);
            }

            // Pastikan data penting terisi
            $needsSave = false;
            if (empty($profile->fullname)) {
                $profile->fullname = $user->name;
                $needsSave = true;
            }
            if (empty($profile->email)) {
                $profile->email = $user->email;
                $needsSave = true;
            }

            if ($needsSave) {
                $profile->save();
                Log::info('Profile updated with missing data');
            }

            Log::info('Returning profile data:', [
                'profile_id' => $profile->id,
                'user_id' => $profile->user_id
            ]);

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at
                ],
                'profile' => [
                    'id' => $profile->id, // Pastikan ID profile ada
                    'user_id' => $profile->user_id,
                    'fullname' => $profile->fullname,
                    'username' => $profile->username,
                    'dob' => $profile->dob,
                    'email' => $profile->email,
                    'bio' => $profile->bio,
                    'hobbies' => $profile->hobbies ?: [],
                    'badges' => $profile->badges ?: [],
                    'avatar' => $profile->avatar,
                    'level' => $profile->level ?: 1,
                    'progress' => $profile->progress ?: 0
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error in UserProfileController@show:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Method khusus untuk payment jika diperlukan
    public function getProfileForPayment()
    {
        return $this->show(); // Gunakan method show yang sama
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // Debug: log semua data yang diterima
        Log::info('Profile update request data:', [
            'all_data' => $request->all(),
            'fullname' => $request->input('fullname'),
            'email' => $request->input('email'),
            'has_avatar' => $request->hasFile('avatar'),
            'user_id' => $user->id
        ]);

        // Temukan profil atau buat yang baru jika belum ada
        $profile = $user->profile;
        if (!$profile) {
            $profile = new UserProfile(['user_id' => $user->id]);
        }

        $validator = Validator::make($request->all(), [
            'fullname' => 'required|string|max:255',
            'username' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('users_profile')->ignore($profile->id),
            ],
            'dob' => 'nullable|date',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users_profile')->ignore($profile->id),
            ],
            'bio' => 'nullable|string',
            'hobbies' => 'nullable|array',
            'hobbies.*' => 'string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'level' => 'nullable|integer',
            'progress' => 'nullable|integer|between:0,100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'message' => 'Validasi gagal.'], 422);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            if ($profile->avatar && Storage::disk('public')->exists($profile->avatar)) {
                Storage::disk('public')->delete($profile->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $profile->avatar = $avatarPath;
        }

        // Update data profil
        $profile->fullname = $request->fullname;
        $profile->username = $request->username;
        $profile->dob = $request->dob;
        $profile->email = $request->email;
        $profile->bio = $request->bio;
        $profile->hobbies = $request->input('hobbies', []);
        $profile->level = $request->input('level', $profile->level ?: 1);
        $profile->progress = $request->input('progress', $profile->progress ?: 0);

        // Pastikan user_id terisi
        if (!$profile->exists) {
            $profile->user_id = $user->id;
        }

        $profile->save();

        // Update user table juga
        if ($user->name !== $request->fullname) {
            $user->name = $request->fullname;
        }
        if ($user->email !== $request->email) {
            $user->email = $request->email;
        }
        $user->save();

        return response()->json([
            'message' => 'Profil berhasil diperbarui!',
            'profile' => $profile,
            'avatar_url' => $profile->avatar ? Storage::url($profile->avatar) : null,
        ]);
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'message' => 'Validasi gagal.'], 422);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Password lama salah.'], 403);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password berhasil diubah!']);
    }

    public function removeAvatar()
    {
        try {
            $user = Auth::user();
            $profile = $user->profile;

            if (!$profile) {
                return response()->json(['message' => 'Profil tidak ditemukan.'], 404);
            }

            // Hapus file avatar dari storage
            if ($profile->avatar && Storage::disk('public')->exists($profile->avatar)) {
                Storage::disk('public')->delete($profile->avatar);
            }

            // Update database
            $profile->avatar = null;
            $profile->save();

            return response()->json([
                'message' => 'Avatar berhasil dihapus!',
                'profile' => $profile
            ]);

        } catch (\Exception $e) {
            Log::error('Error removing avatar:', [
                'user_id' => $user->id ?? 'unknown',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Gagal menghapus avatar.'], 500);
        }
    }
}

