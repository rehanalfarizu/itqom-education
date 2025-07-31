<template>
  <section class="bg-gray-50 min-h-screen text-gray-800 font-sans">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 sm:pt-12 lg:pt-18 pb-8 sm:pb-12 lg:pb-16">

      <!-- Header Section -->
      <div class="mb-8 sm:mb-12 lg:mb-14">
        <h1 class="text-2xl sm:text-3xl lg:text-4xl xl:text-5xl font-bold text-purple-800 leading-tight">Profil Pengguna</h1>
        <p class="text-base sm:text-lg text-gray-600 mt-2 sm:mt-4">Perbarui informasi akunmu di bawah ini.</p>
      </div>

      <!-- Main Form -->
      <form @submit.prevent="submitProfile" enctype="multipart/form-data" class="bg-white shadow-xl rounded-2xl p-4 sm:p-6 lg:p-10 space-y-6 sm:space-y-8">

        <!-- Avatar Section -->
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 sm:gap-6 lg:gap-8 mb-6 sm:mb-8 lg:mb-10">
          <div class="flex-shrink-0">
            <img id="avatarPreview"
                 :src="currentAvatarUrl"
                 alt="Avatar"
                 class="w-24 h-24 sm:w-28 sm:h-28 lg:w-32 lg:h-32 rounded-full border-4 border-purple-600 shadow-lg object-cover">
          </div>
          <div class="text-center sm:text-left flex-1 min-w-0">
            <div class="font-bold text-purple-800 text-lg sm:text-xl lg:text-2xl xl:text-3xl flex flex-col sm:flex-row items-center gap-2 sm:gap-4">
              <span class="truncate">{{ formData.fullname || getUserName() }}</span>
              <span class="bg-yellow-200 text-yellow-800 text-xs sm:text-sm lg:text-base px-2 sm:px-3 lg:px-4 py-1 sm:py-1.5 rounded-full badge-animate whitespace-nowrap">Gen Z Squad 🦄</span>
            </div>
            <div class="text-sm sm:text-base text-gray-500 mt-1 sm:mt-2">Aktif belajar sejak {{ userCreatedAtYear }}</div>

            <!-- Avatar Controls -->
            <div class="mt-3 space-y-2">
              <label class="text-purple-600 text-sm sm:text-base hover:underline cursor-pointer block">
                Ganti Foto Profil
                <input type="file" id="avatarInput" name="avatar" class="hidden" accept="image/*" @change="handleAvatarChange">
              </label>
              <button v-if="formData.avatar || currentAvatarPreviewUrl !== '/image/hajisodikin.jpg'"
                      @click="removeAvatar"
                      type="button"
                      class="text-red-600 text-sm sm:text-base hover:underline cursor-pointer block">
                Hapus Foto Profil
              </button>
            </div>
          </div>
        </div>

        <!-- Form Fields -->
        <div class="space-y-6">
          <!-- Full Name -->
          <div>
            <label class="block text-base sm:text-lg font-semibold text-gray-700 mb-2 sm:mb-3" for="fullname">
              Nama Lengkap <span class="text-red-500">*</span>
            </label>
            <input type="text" id="fullname" name="fullname"
                   v-model="formData.fullname"
                   class="form-input-custom"
                   required
                   placeholder="Masukkan nama lengkap">
          </div>

          <!-- Username -->
          <div>
            <label class="block text-base sm:text-lg font-semibold text-gray-700 mb-2 sm:mb-3" for="username">Username</label>
            <input type="text" id="username" name="username"
                   v-model="formData.username"
                   class="form-input-custom"
                   placeholder="Masukkan username">
          </div>

          <!-- Date of Birth -->
          <div>
            <label class="block text-base sm:text-lg font-semibold text-gray-700 mb-2 sm:mb-3" for="dob">Tanggal Lahir</label>
            <input type="date" id="dob" name="dob"
                   v-model="formData.dob"
                   class="form-input-custom">
          </div>

          <!-- Email -->
          <div>
            <label class="block text-base sm:text-lg font-semibold text-gray-700 mb-2 sm:mb-3" for="email">
              Email <span class="text-red-500">*</span>
            </label>
            <input type="email" id="email" name="email"
                   v-model="formData.email"
                   class="form-input-custom"
                   required
                   placeholder="Masukkan email">
          </div>

          <!-- Bio -->
          <div>
            <label class="block text-base sm:text-lg font-semibold text-gray-700 mb-2 sm:mb-3" for="bio">Bio</label>
            <textarea id="bio" name="bio" rows="4"
                      v-model="formData.bio"
                      class="form-input-custom resize-none"
                      placeholder="Ceritain sedikit tentang dirimu..."></textarea>
          </div>

          <!-- Hobbies -->
          <div>
            <label class="block text-base sm:text-lg font-semibold text-gray-700 mb-2 sm:mb-3">Hobi / Interest</label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mt-3">
              <label v-for="(label, value) in availableHobbies" :key="value"
                     class="flex items-center gap-2 sm:gap-2.5 text-sm sm:text-base p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                <input type="checkbox" name="hobbies[]" :value="value" v-model="formData.hobbies"
                       class="form-checkbox h-4 w-4 sm:h-5 sm:w-5 text-purple-600 rounded">
                <span class="select-none">{{ label }}</span>
              </label>
            </div>
          </div>

          <!-- Badges Collection -->
          <div class="mb-6 sm:mb-8">
            <label class="block text-base sm:text-lg font-semibold text-gray-700 mb-2 sm:mb-3">Badge Koleksi</label>
            <div class="flex flex-wrap gap-2 sm:gap-3 lg:gap-4 mt-2">
              <span v-for="badge in formData.badges" :key="badge"
                    :class="getBadgeClass(badge)"
                    class="px-3 sm:px-4 py-1 sm:py-1.5 rounded-full text-sm sm:text-base flex items-center gap-1">
                    {{ badge }}
              </span>
              <span v-if="!formData.badges || formData.badges.length === 0"
                    class="bg-gray-200 text-gray-700 px-3 sm:px-4 py-1 sm:py-1.5 rounded-full text-sm sm:text-base flex items-center gap-1">
                ✨ Belum ada Badge
              </span>
            </div>
          </div>

          <!-- Level Progress -->
          <div class="mb-6 sm:mb-8">
            <label class="block text-base sm:text-lg font-semibold text-gray-700 mb-2 sm:mb-3">Level Kamu</label>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4">
              <span class="font-bold text-purple-700 text-lg sm:text-xl whitespace-nowrap">Level {{ formData.level }}</span>
              <div class="w-full max-w-full sm:max-w-md bg-gray-200 h-3 sm:h-4 rounded-full overflow-hidden">
                <div class="bg-gradient-to-r from-purple-500 to-indigo-500 h-3 sm:h-4 rounded-full progress-animate"
                     :style="{ width: `${formData.progress}%` }"></div>
              </div>
              <span class="text-sm sm:text-base text-gray-500 whitespace-nowrap">{{ formData.progress }}% ke Level {{ formData.level + 1 }}</span>
            </div>
          </div>

          <!-- Motivational Quote -->
          <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500">
            <p class="text-sm sm:text-base lg:text-lg text-purple-700 italic font-medium">"{{ randomMotivasi }}"</p>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row justify-between items-center pt-4 sm:pt-6 gap-4 sm:gap-0">
          <router-link to="/dashboard"
                       class="text-purple-700 hover:underline text-base sm:text-lg font-medium order-2 sm:order-1">
            ← Kembali ke Dashboard
          </router-link>
          <button type="submit"
                  :disabled="isSaving"
                  class="w-full sm:w-auto bg-purple-800 text-white font-bold px-6 sm:px-8 py-3 sm:py-3.5 rounded-lg hover:bg-purple-900 disabled:opacity-50 disabled:cursor-not-allowed transition flex items-center justify-center gap-3 text-base sm:text-lg order-1 sm:order-2">
            <span v-if="!isSaving">Simpan Perubahan</span>
            <span v-else class="flex items-center">
              <svg class="animate-spin h-5 w-5 sm:h-6 sm:w-6 text-white mr-2 sm:mr-3" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
              </svg>
              Menyimpan...
            </span>
          </button>
        </div>

        <!-- Success/Error Messages -->
        <div v-if="successMessage" class="mt-4 sm:mt-6 p-4 sm:p-5 bg-green-100 text-green-700 rounded-lg text-sm sm:text-base lg:text-lg">
          ✅ {{ successMessage }}
        </div>

        <div v-if="errorMessage" class="mt-4 sm:mt-6 p-4 sm:p-5 bg-red-100 text-red-700 rounded-lg text-sm sm:text-base lg:text-lg">
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
        avatar: null,
        level: 1,
        progress: 0,
        badges: [],
      },
      currentAvatarPreviewUrl: '/image/hajisodikin.jpg',
      userCreatedAt: null,
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
      return new Date().getFullYear();
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

      if (/^\d{4}-\d{2}-\d{2}$/.test(dateString)) {
        return dateString;
      }

      try {
        const date = new Date(dateString);
        if (isNaN(date)) return '';
        return date.toISOString().split('T')[0];
      } catch (error) {
        console.error('Error formatting date:', error);
        return '';
      }
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

        this.formData.fullname = profile.fullname || user.name;
        this.formData.username = profile.username || '';
        this.formData.dob = this.formatDateForInput(profile.dob);
        this.formData.email = profile.email || user.email;
        this.formData.bio = profile.bio || 'Coding enthusiast & Error lover 💀';
        this.formData.hobbies = profile.hobbies || [];
        this.formData.level = profile.level ?? 3;
        this.formData.progress = profile.progress ?? 60;
        this.formData.badges = profile.badges || [];

        if (profile.avatar) {
          this.currentAvatarPreviewUrl = `/storage/${profile.avatar}`;
        } else {
          this.currentAvatarPreviewUrl = '/image/hajisodikin.jpg';
        }

        this.userCreatedAt = user.created_at;

      } catch (error) {
        console.error('❌ Error fetching profile:', error);

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

    async submitProfile() {
      this.isSaving = true;
      this.successMessage = '';
      this.errorMessage = '';

      const dataToSubmit = new FormData();

      if (this.formData.dob && !/^\d{4}-\d{2}-\d{2}$/.test(this.formData.dob)) {
        this.errorMessage = 'Format tanggal lahir tidak valid. Gunakan format YYYY-MM-DD.';
        this.isSaving = false;
        return;
      }

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

      if (!dataToSubmit.has('fullname')) {
        dataToSubmit.append('fullname', this.formData.fullname || '');
      }
      if (!dataToSubmit.has('email')) {
        dataToSubmit.append('email', this.formData.email || '');
      }

      dataToSubmit.append('_method', 'PUT');

      try {
        const authToken = localStorage.getItem('authToken');
        if (!authToken) {
          this.errorMessage = 'Autentikasi diperlukan untuk menyimpan perubahan.';
          this.$router.push('/login');
          return;
        }

        const response = await axios.post('/api/user/profile', dataToSubmit, {
          headers: {
            'Authorization': `Bearer ${authToken}`,
            'Content-Type': 'multipart/form-data'
          }
        });

        this.successMessage = response.data.message || 'Data berhasil disimpan!';

        if (response.data.profile && response.data.profile.dob) {
          this.formData.dob = this.formatDateForInput(response.data.profile.dob);
        }

        if (response.data.avatar_url) {
          this.currentAvatarPreviewUrl = response.data.avatar_url;
        }

        const currentUserData = JSON.parse(localStorage.getItem('user'));
        if (currentUserData) {
          currentUserData.name = this.formData.fullname;
          currentUserData.email = this.formData.email;
          localStorage.setItem('user', JSON.stringify(currentUserData));
          window.dispatchEvent(new Event('localStorageUpdated'));
        }

        window.dispatchEvent(new Event('profileUpdated'));
        sessionStorage.removeItem('userProfile');

        this.successMessage = 'Profil berhasil diperbarui! Halaman akan di-refresh dalam 1 detik...';

        setTimeout(() => {
          window.location.reload();
        }, 1000);

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
        }, 3000);
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
    },

    handleAvatarChange(event) {
      const file = event.target.files[0];
      if (file) {
        const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml'];
        const maxSize = 2 * 1024 * 1024; // 2MB

        if (!validTypes.includes(file.type)) {
          this.errorMessage = 'Format file tidak didukung. Gunakan JPEG, PNG, JPG, GIF, atau SVG.';
          event.target.value = '';
          return;
        }

        if (file.size > maxSize) {
          this.errorMessage = 'Ukuran file terlalu besar. Maksimal 2MB.';
          event.target.value = '';
          return;
        }

        this.formData.avatar = file;

        const reader = new FileReader();
        reader.onload = (e) => {
          this.currentAvatarPreviewUrl = e.target.result;
        };
        reader.readAsDataURL(file);

        this.errorMessage = '';
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

        this.formData.avatar = null;
        this.currentAvatarPreviewUrl = '/image/hajisodikin.jpg';

        const fileInput = document.getElementById('avatarInput');
        if (fileInput) {
          fileInput.value = '';
        }

        this.successMessage = response.data.message || 'Foto profil berhasil dihapus!';
        window.dispatchEvent(new Event('profileUpdated'));

        this.successMessage = 'Foto profil berhasil dihapus! Halaman akan di-refresh dalam 1 detik...';

        setTimeout(() => {
          window.location.reload();
        }, 1000);

      } catch (error) {
        console.error('Error removing avatar:', error);
        this.errorMessage = error.response?.data?.message || 'Gagal menghapus foto profil.';
      }
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
  padding: 0.75rem 1rem;
  font-size: 1rem;
  line-height: 1.5rem;
  border-radius: 0.5rem;
  outline: none;
  transition: all 0.2s ease-in-out;
  border: 1px solid #d8b4fe;
  background-color: white;
}

.form-input-custom:focus {
  border: 2px solid #9333ea;
  box-shadow: 0 0 0 2px rgba(147, 51, 234, 0.25);
}

/* Responsive font sizes */
@media (min-width: 640px) {
  .form-input-custom {
    padding: 0.875rem 1.25rem;
    font-size: 1.125rem;
    line-height: 1.75rem;
  }
}

.form-checkbox {
  appearance: none;
  background-color: #fff;
  margin: 0;
  font: inherit;
  color: currentColor;
  width: 1em;
  height: 1em;
  border: 0.15em solid #a78bfa;
  border-radius: 0.25em;
  transform: translateY(-0.075em);
  display: grid;
  place-content: center;
  flex-shrink: 0;
}

.form-checkbox::before {
  content: "";
  width: 0.65em;
  height: 0.65em;
  transform: scale(0);
  transition: 120ms transform ease-in-out;
  box-shadow: inset 1em 1em var(--form-control-color, #8b5cf6);
  clip-path: polygon(14% 44%, 0 65%, 50% 100%, 100% 16%, 80% 0%, 43% 62%);
}

.form-checkbox:checked::before {
  transform: scale(1);
}

/* Responsive checkbox sizes */
@media (min-width: 640px) {
  .form-checkbox {
    width: 1.25em;
    height: 1.25em;
    border: 0.175em solid #a78bfa;
  }

  .form-checkbox::before {
    width: 0.75em;
    height: 0.75em;
  }
}

/* Touch-friendly hover states */
@media (hover: hover) {
  .form-input-custom:hover {
    border-color: #a78bfa;
  }

  .hover\:bg-gray-50:hover {
    background-color: #f9fafb;
  }
}

/* Improve readability on small screens */
@media (max-width: 639px) {
  .truncate {
    max-width: 200px;
  }
}
</style>
