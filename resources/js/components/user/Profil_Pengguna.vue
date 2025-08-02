<template>
  <section class="bg-gray-50 min-h-screen text-gray-800 font-sans">
    <div class="max-w-4xl mx-auto px-8 pt-18 pb-16">

      <div class="mb-14">
        <h1 class="text-4xl lg:text-5xl font-bold text-purple-800 leading-tight">Profil Pengguna</h1>
        <p class="text-lg text-gray-600 mt-4">Perbarui informasi akunmu di bawah ini.</p>
      </div>

      <form @submit.prevent="submitProfile" enctype="multipart/form-data" class="bg-white shadow-xl rounded-2xl p-10 space-y-8">
        <div class="flex items-center gap-8 mb-10">
          <img id="avatarPreview"
               :src="currentAvatarUrl"
               alt="Avatar"
               class="w-28 h-28 md:w-32 md:h-32 rounded-full border-5 border-purple-600 shadow-lg object-cover">
          <div>
            <div class="font-bold text-purple-800 text-2xl md:text-3xl flex items-center gap-4">
              {{ formData.fullname || getUserName() }}
              <span class="bg-yellow-200 text-yellow-800 text-base px-4 py-1.5 rounded-full badge-animate">Gen Z Squad 🦄</span>
            </div>
            <div class="text-base text-gray-500 mt-2">Aktif belajar sejak {{ userCreatedAtYear }}</div>
            <label class="text-purple-600 text-base hover:underline cursor-pointer mt-3 block">
              Ganti Foto Profil
              <input type="file" id="avatarInput" name="avatar" class="hidden" accept="image/*" @change="handleAvatarChange">
            </label>
            <button v-if="formData.avatar || currentAvatarPreviewUrl !== '/image/hajisodikin.jpg'"
                    @click="removeAvatar"
                    type= "button"
                    class="text-red-600 text-base hover:underline cursor-pointer mt-2 block">
              Hapus Foto Profil
            </button>
          </div>
        </div>

        <div>
          <label class="block text-lg font-semibold text-gray-700 mb-3" for="fullname">Nama Lengkap</label>
          <input type="text" id="fullname" name="fullname"
                 v-model="formData.fullname"
                 class="form-input-custom" required>
        </div>

        <div>
          <label class="block text-lg font-semibold text-gray-700 mb-3" for="username">Username</label>
          <input type="text" id="username" name="username"
                 v-model="formData.username"
                 class="form-input-custom">
        </div>

        <div>
          <label class="block text-lg font-semibold text-gray-700 mb-3" for="dob">Tanggal Lahir</label>
          <input type="date" id="dob" name="dob"
                 v-model="formData.dob"
                 class="form-input-custom">
        </div>

        <div>
          <label class="block text-lg font-semibold text-gray-700 mb-3" for="email">Email</label>
          <input type="email" id="email" name="email"
                 v-model="formData.email"
                 class="form-input-custom" required>
        </div>

        <div>
          <label class="block text-lg font-semibold text-gray-700 mb-3" for="bio">Bio</label>
          <textarea id="bio" name="bio" rows="4"
                    v-model="formData.bio"
                    class="form-input-custom"
                    placeholder="Ceritain sedikit tentang dirimu..."></textarea>
        </div>

        <div>
          <label class="block text-lg font-semibold text-gray-700 mb-3">Hobi / Interest</label>
          <div class="flex flex-wrap gap-5 mt-3">
            <label v-for="(label, value) in availableHobbies" :key="value" class="flex items-center gap-2.5 text-lg">
              <input type="checkbox" name="hobbies[]" :value="value" v-model="formData.hobbies" class="form-checkbox h-5 w-5 text-purple-600 rounded">
              <span>{{ label }}</span>
            </label>
          </div>
        </div>

        <div class="mb-8">
          <label class="block text-lg font-semibold text-gray-700 mb-3">Badge Koleksi</label>
          <div class="flex flex-wrap gap-4 mt-2">
            <span v-for="badge in formData.badges" :key="badge"
                  :class="getBadgeClass(badge)"
                  class="px-4 py-1.5 rounded-full text-base flex items-center gap-1">
                  {{ badge }}
            </span>
            <span v-if="!formData.badges || formData.badges.length === 0" class="bg-gray-200 text-gray-700 px-4 py-1.5 rounded-full text-base flex items-center gap-1">
              ✨ Belum ada Badge
            </span>
          </div>
        </div>

        <div class="mb-8">
          <label class="block text-lg font-semibold text-gray-700 mb-3">Level Kamu</label>
          <div class="flex items-center gap-4">
            <span class="font-bold text-purple-700 text-xl">Level {{ formData.level }}</span>
            <div class="w-full max-w-md bg-gray-200 h-4 rounded-full overflow-hidden">
              <div class="bg-gradient-to-r from-purple-500 to-indigo-500 h-4 rounded-full progress-animate"
                   :style="{ width: `${formData.progress}%` }"></div>
            </div>
            <span class="text-base text-gray-500">{{ formData.progress }}% ke Level {{ formData.level + 1 }}</span>
          </div>
        </div>

        <p class="text-lg text-purple-700 mb-8 italic">"{{ randomMotivasi }}"</p>

        <div class="flex justify-between items-center pt-6">
          <router-link to="/dashboard" class="text-purple-700 hover:underline text-lg font-medium">← Kembali ke Dashboard</router-link>
          <button type="submit"
                  :disabled="isSaving"
                  class="bg-purple-800 text-white font-bold px-8 py-3.5 rounded-lg hover:bg-purple-900 transition flex items-center gap-3 text-lg">
            <span v-if="!isSaving">Simpan Perubahan</span>
            <span v-else class="flex items-center">
              <svg class="animate-spin h-6 w-6 text-white mr-3" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
              </svg>
              Menyimpan...
            </span>
          </button>
        </div>

        <div v-if="successMessage" class="mt-6 p-5 bg-green-100 text-green-700 rounded-lg text-lg">
          ✅ {{ successMessage }}
        </div>

        <div v-if="errorMessage" class="mt-6 p-5 bg-red-100 text-red-700 rounded-lg text-lg">
          ❌ {{ errorMessage }}
        </div>
      </form>

    </div>
  </section>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      formData: {
        fullname: '',
        username: '',
        dob: '',
        email: '',
        bio: '',
        hobbies: [],
        avatar: null, // File object for new avatar
        level: 1,
        progress: 0,
        badges: [],
      },
      currentAvatarPreviewUrl: '/image/hajisodikin.jpg', // Default avatar path
      userCreatedAt: null, // Untuk menyimpan created_at dari user
      isSaving: false,
      successMessage: '',
      errorMessage: '',
      availableHobbies: {
        'Ngoding': '💻 Ngoding',
        'Gaming': '🎮 Gaming',
        'Desain': '🎨 Desain',
        'Nge-vlog': '📹 Nge-vlog',
        'Ngonten': '📱 Ngonten'
      },
      motivasi: [
        "Setiap error adalah langkah menuju jago! 💪",
        "Belajar hari ini, sukses esok hari 🚀",
        "Jangan takut gagal, Gen Z selalu bangkit! 🔥",
        "Skillmu = aset masa depanmu ✨"
      ]
    };
  },
  computed: {
    userCreatedAtYear() {
      if (this.userCreatedAt) {
        return new Date(this.userCreatedAt).getFullYear();
      }
      return new Date().getFullYear(); // Fallback jika tidak ada data
    },
    randomMotivasi() {
      const randomIndex = Math.floor(Math.random() * this.motivasi.length);
      return this.motivasi[randomIndex];
    },
    currentAvatarUrl() {
      return this.currentAvatarPreviewUrl;
    }
  },
  created() {
    this.fetchProfileData();
  },
  methods: {
    formatDateForInput(dateString) {
      if (!dateString) return '';

      // Jika tanggal sudah dalam format YYYY-MM-DD, return as is
      if (/^\d{4}-\d{2}-\d{2}$/.test(dateString)) {
        return dateString;
      }

      // Konversi berbagai format tanggal ke YYYY-MM-DD
      try {
        const date = new Date(dateString);
        if (isNaN(date)) return '';

        // Format ke YYYY-MM-DD untuk input HTML
        return date.toISOString().split('T')[0];
      } catch (error) {
        console.error('Error formatting date:', error);
        return '';
      }
    },

    // *** Avatar URL Handler - Backend akan handle Cloudinary ***
    processAvatarUrl(avatarPath) {
      if (!avatarPath) {
        return '/image/hajisodikin.jpg'; // Default avatar
      }

      // Jika sudah berupa URL lengkap, gunakan langsung
      if (avatarPath.startsWith('http://') || avatarPath.startsWith('https://')) {
        return avatarPath;
      }

      // Jika berupa path relatif dari local storage, buat URL lengkap
      if (avatarPath.startsWith('storage/') || avatarPath.startsWith('/storage/')) {
        const cleanPath = avatarPath.replace(/^\//, ''); // Remove leading slash if exists
        return `${window.location.origin}/${cleanPath}`;
      }

      // Jika hanya nama file, asumsikan dari storage
      return `${window.location.origin}/storage/${avatarPath}`;
    },

    async fetchProfileData() {
      try {
        const authToken = localStorage.getItem('authToken');
        if (!authToken) {
          this.errorMessage = 'Anda belum login. Silakan login terlebih dahulu.';
          this.$router.push('/login');
          return;
        }

        console.log('🔑 Fetching profile with token:', authToken);

        const response = await axios.get('/api/profile', {
          headers: {
            'Authorization': `Bearer ${authToken}`,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          }
        });

        console.log('✅ Profile response:', response.data);

        const { user, profile } = response.data;

        // Populate formData from fetched data
        this.formData.fullname = profile.fullname || user.name;
        this.formData.username = profile.username || '';
        this.formData.dob = this.formatDateForInput(profile.dob);
        this.formData.email = profile.email || user.email;
        this.formData.bio = profile.bio || 'Coding enthusiast & Error lover 💀';
        this.formData.hobbies = profile.hobbies || [];
        this.formData.level = profile.level ?? 3;
        this.formData.progress = profile.progress ?? 60;
        this.formData.badges = profile.badges || [];

        // *** FIXED: Handle avatar URL properly ***
        this.currentAvatarPreviewUrl = this.processAvatarUrl(profile.avatar);
        console.log('🖼️ Avatar URL set to:', this.currentAvatarPreviewUrl);

        // Set user created at
        this.userCreatedAt = user.created_at;

      } catch (error) {
        console.error('❌ Error fetching profile:', error);
        console.error('📊 Error response:', error.response);

        if (error.response?.status === 401) {
          this.errorMessage = 'Token tidak valid. Silakan login ulang.';
          localStorage.removeItem('authToken');
          localStorage.removeItem('user');
          this.$router.push('/login');
        } else if (error.response?.status === 404) {
          this.errorMessage = 'Endpoint profil tidak ditemukan. Periksa route backend.';
        } else {
          this.errorMessage = error.response?.data?.message || 'Gagal memuat data profil.';
        }
      }
    },

    handleAvatarChange(event) {
      const file = event.target.files[0];
      if (file) {
        // File validation
        const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
        const maxSize = 5 * 1024 * 1024; // 5MB

        if (!validTypes.includes(file.type)) {
          this.errorMessage = 'Format file tidak didukung. Gunakan JPEG, PNG, JPG, GIF, atau WebP.';
          event.target.value = '';
          return;
        }

        if (file.size > maxSize) {
          this.errorMessage = 'Ukuran file terlalu besar. Maksimal 5MB.';
          event.target.value = '';
          return;
        }

        // Simpan file untuk upload
        this.formData.avatar = file;

        // Buat preview
        const reader = new FileReader();
        reader.onload = (e) => {
          this.currentAvatarPreviewUrl = e.target.result;
        };
        reader.readAsDataURL(file);

        // Clear error message jika ada
        this.errorMessage = '';
      }
    },

    async submitProfile() {
      this.isSaving = true;
      this.successMessage = '';
      this.errorMessage = '';

      // *** Validasi tanggal sebelum submit ***
      if (this.formData.dob && !/^\d{4}-\d{2}-\d{2}$/.test(this.formData.dob)) {
        this.errorMessage = 'Format tanggal lahir tidak valid. Gunakan format YYYY-MM-DD.';
        this.isSaving = false;
        return;
      }

      // *** Validasi field required ***
      if (!this.formData.fullname || this.formData.fullname.trim() === '') {
        this.errorMessage = 'Nama lengkap harus diisi.';
        this.isSaving = false;
        return;
      }

      if (!this.formData.email || this.formData.email.trim() === '') {
        this.errorMessage = 'Email harus diisi.';
        this.isSaving = false;
        return;
      }

      const dataToSubmit = new FormData();

      // Append all form data
      for (const key in this.formData) {
        if (key === 'hobbies' && Array.isArray(this.formData.hobbies)) {
          this.formData.hobbies.forEach(hobby => {
            dataToSubmit.append('hobbies[]', hobby);
          });
        } else if (key === 'avatar' && this.formData.avatar instanceof File) {
          dataToSubmit.append(key, this.formData.avatar);
        } else if (key !== 'badges') {
          const value = this.formData[key];
          if (value !== null && value !== undefined) {
            dataToSubmit.append(key, value);
          }
        }
      }

      // Tambahkan _method untuk Laravel method spoofing
      dataToSubmit.append('_method', 'PUT');

      try {
        const authToken = localStorage.getItem('authToken');
        if (!authToken) {
          this.errorMessage = 'Autentikasi diperlukan untuk menyimpan perubahan.';
          this.$router.push('/login');
          return;
        }

        console.log('🚀 Sending request to /api/user/profile');
        const response = await axios.post('/api/user/profile', dataToSubmit, {
          headers: {
            'Authorization': `Bearer ${authToken}`,
            'Content-Type': 'multipart/form-data'
          }
        });

        this.successMessage = response.data.message || 'Data berhasil disimpan!';

        // Handle avatar URL from response
        if (response.data.avatar_url) {
          this.currentAvatarPreviewUrl = this.processAvatarUrl(response.data.avatar_url);
          console.log('🎉 Avatar updated to:', this.currentAvatarPreviewUrl);
        }

        // Update tanggal lahir dari response jika ada
        if (response.data.profile && response.data.profile.dob) {
          this.formData.dob = this.formatDateForInput(response.data.profile.dob);
        }

        // Update local user data
        const currentUserData = JSON.parse(localStorage.getItem('user'));
        if (currentUserData) {
          currentUserData.name = this.formData.fullname;
          currentUserData.email = this.formData.email;
          localStorage.setItem('user', JSON.stringify(currentUserData));
          window.dispatchEvent(new Event('localStorageUpdated'));
        }

        // Dispatch event untuk update navbar dan clear cache
        window.dispatchEvent(new Event('profileUpdated'));
        sessionStorage.removeItem('userProfile');

        this.successMessage = 'Profil berhasil diperbarui! Halaman akan di-refresh dalam 2 detik...';

        // Refresh halaman setelah 2 detik untuk memastikan semua perubahan terlihat
        setTimeout(() => {
          window.location.reload();
        }, 2000);

      } catch (error) {
        console.error('❌ Error submitting profile:', error);

        if (error.response?.data?.errors) {
          const errors = error.response.data.errors;
          this.errorMessage = Object.values(errors).flat().join('<br>');
        } else {
          this.errorMessage = error.response?.data?.message || 'Terjadi kesalahan saat menyimpan data.';
        }

        if (error.response?.status === 401) {
          localStorage.removeItem('authToken');
          localStorage.removeItem('user');
          this.$router.push('/login');
        }
      } finally {
        this.isSaving = false;
        setTimeout(() => {
          this.successMessage = '';
          this.errorMessage = '';
        }, 5000);
      }
    },

    async removeAvatar() {
      if (!confirm('Apakah Anda yakin ingin menghapus foto profil?')) {
        return;
      }

      try {
        const authToken = localStorage.getItem('authToken');
        if (!authToken) {
          this.errorMessage = 'Autentikasi diperlukan.';
          this.$router.push('/login');
          return;
        }

        const response = await axios.delete('/api/user/profile/avatar', {
          headers: {
            'Authorization': `Bearer ${authToken}`,
            'Content-Type': 'application/json'
          }
        });

        // Reset avatar di UI
        this.formData.avatar = null;
        this.currentAvatarPreviewUrl = '/image/hajisodikin.jpg';

        // Clear file input
        const fileInput = document.getElementById('avatarInput');
        if (fileInput) {
          fileInput.value = '';
        }

        this.successMessage = response.data.message || 'Foto profil berhasil dihapus!';

        // Dispatch event untuk update navbar
        window.dispatchEvent(new Event('profileUpdated'));

        this.successMessage = 'Foto profil berhasil dihapus! Halaman akan di-refresh dalam 2 detik...';

        setTimeout(() => {
          window.location.reload();
        }, 2000);

      } catch (error) {
        console.error('Error removing avatar:', error);
        this.errorMessage = error.response?.data?.message || 'Gagal menghapus foto profil.';
      }
    },

    getBadgeClass(badgeName) {
      switch (badgeName) {
        case 'Aktif':
          return 'bg-green-100 text-green-700 badge-animate';
        case 'Fast Learner':
          return 'bg-blue-100 text-blue-700';
        case 'Top 3':
          return 'bg-yellow-100 text-yellow-700';
        case 'Gen Z Squad 🦄':
          return 'bg-yellow-200 text-yellow-800 badge-animate';
        default:
          return 'bg-gray-100 text-gray-700';
      }
    },

    getUserName() {
      try {
        const userData = localStorage.getItem('user');
        if (userData) {
          const user = JSON.parse(userData);
          return user.name || 'Pengguna';
        }
      } catch (e) {
        console.error('Error getting user name:', e);
      }
      return 'Pengguna';
    }
  }
};
</script>

<style scoped>
.badge-animate {
  animation: bounce 1.2s infinite alternate;
}

@keyframes bounce {
  to {
    transform: translateY(-6px);
  }
}

.progress-animate {
  animation: progressBar 1.2s cubic-bezier(.4, 2, .6, 1) forwards;
}

@keyframes progressBar {
  from {
    width: 0;
  }
}

.form-input-custom {
  display: block;
  width: 100%;
  padding: 0.875rem 1.25rem;
  font-size: 1.125rem;
  line-height: 1.75rem;
  border-radius: 0.5rem;
  outline: none;
  transition: all 0.2s ease-in-out;
  border: 1px solid #d8b4fe;

  &:focus {
    border: 2px solid #9333ea;
    box-shadow: 0 0 0 2px rgba(147, 51, 234, 0.25);
  }
}

.form-checkbox {
  appearance: none;
  background-color: #fff;
  margin: 0;
  font: inherit;
  color: currentColor;
  width: 1.25em;
  height: 1.25em;
  border: 0.175em solid #a78bfa;
  border-radius: 0.25em;
  transform: translateY(-0.075em);
  display: grid;
  place-content: center;
}

.form-checkbox::before {
  content: "";
  width: 0.75em;
  height: 0.75em;
  transform: scale(0);
  transition: 120ms transform ease-in-out;
  box-shadow: inset 1em 1em var(--form-control-color, #8b5cf6);
  clip-path: polygon(14% 44%, 0 65%, 50% 100%, 100% 16%, 80% 0%, 43% 62%);
}

.form-checkbox:checked::before {
  transform: scale(1);
}
</style>
