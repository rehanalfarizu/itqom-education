<template>
  <div class="flex justify-center items-center min-h-[80vh] bg-gray-50">
    <div class="w-full max-w-md p-8 bg-white rounded-2xl shadow-lg border border-gray-100">
      <h2 class="text-3xl font-bold mb-8 text-center text-blue-700 tracking-tight">Ganti Password</h2>
      <form @submit.prevent="handleChangePassword" class="space-y-6">
        <transition name="fade">
          <div v-if="successMessage" class="flex items-center gap-2 bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded-lg mb-2 animate-fade-in">
            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ successMessage }}</span>
          </div>
        </transition>
        <transition name="fade">
          <div v-if="errorMessage" class="flex items-center gap-2 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-2 animate-fade-in">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ errorMessage }}</span>
          </div>
        </transition>
        <div>
          <label for="currentPassword" class="block text-sm font-medium text-gray-700 mb-1">Password Lama</label>
          <div class="relative">
            <input
              :type="showCurrent ? 'text' : 'password'"
              id="currentPassword"
              v-model="currentPassword"
              class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
              placeholder="Masukkan password lama"
              required
            />
            <button type="button" @click="showCurrent = !showCurrent" class="absolute right-2 top-2 text-gray-400 hover:text-blue-500 focus:outline-none">
              <svg v-if="showCurrent" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.522 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z" />
              </svg>
              <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L8.464 8.464M9.878 9.878l4.242 4.242m0 0L16.536 16.536M14.12 14.12l4.242 4.242" />
              </svg>
            </button>
          </div>
        </div>
        <div>
          <label for="newPassword" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
          <div class="relative">
            <input
              :type="showNew ? 'text' : 'password'"
              id="newPassword"
              v-model="newPassword"
              class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
              placeholder="Masukkan password baru"
              required
            />
            <button type="button" @click="showNew = !showNew" class="absolute right-2 top-2 text-gray-400 hover:text-blue-500 focus:outline-none">
              <svg v-if="showNew" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.522 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z" />
              </svg>
              <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L8.464 8.464M9.878 9.878l4.242 4.242m0 0L16.536 16.536M14.12 14.12l4.242 4.242" />
              </svg>
            </button>
          </div>
        </div>
        <div>
          <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
          <div class="relative">
            <input
              :type="showConfirm ? 'text' : 'password'"
              id="confirmPassword"
              v-model="confirmPassword"
              class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
              placeholder="Ulangi password baru"
              required
            />
            <button type="button" @click="showConfirm = !showConfirm" class="absolute right-2 top-2 text-gray-400 hover:text-blue-500 focus:outline-none">
              <svg v-if="showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.522 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z" />
              </svg>
              <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L8.464 8.464M9.878 9.878l4.242 4.242m0 0L16.536 16.536M14.12 14.12l4.242 4.242" />
              </svg>
            </button>
          </div>
        </div>
        <button
          type="submit"
          class="w-full flex justify-center items-center bg-gradient-to-r from-blue-600 to-blue-400 hover:from-blue-700 hover:to-blue-500 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none relative overflow-hidden group"
          :disabled="loading"
        >
          <span v-if="!loading" class="relative z-10">Ganti Password</span>
          <span v-else class="relative z-10 flex items-center justify-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Mengganti...
          </span>
          <div class="absolute inset-0 bg-white/20 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
        </button>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'ChangePassword',
  data() {
    return {
      currentPassword: '',
      newPassword: '',
      confirmPassword: '',
      loading: false,
      errorMessage: '',
      successMessage: '',
      showCurrent: false,
      showNew: false,
      showConfirm: false
    };
  },
  methods: {
    async handleChangePassword() {
      this.errorMessage = '';
      this.successMessage = '';
      if (!this.currentPassword || !this.newPassword || !this.confirmPassword) {
        this.errorMessage = 'Semua field harus diisi.';
        return;
      }
      if (this.newPassword !== this.confirmPassword) {
        this.errorMessage = 'Konfirmasi password tidak cocok.';
        return;
      }
      this.loading = true;
      try {
        const token = localStorage.getItem('authToken');
        await axios.post(
          'https://itqom-platform-aa0ffce6a276.herokuapp.com/api/profile/change-password',
          {
            current_password: this.currentPassword,
            new_password: this.newPassword,
            new_password_confirmation: this.confirmPassword
          },
          {
            headers: {
              Authorization: `Bearer ${token}`
            }
          }
        );
        this.successMessage = 'Password berhasil diganti.';
        this.currentPassword = '';
        this.newPassword = '';
        this.confirmPassword = '';
      } catch (error) {
        if (error.response && error.response.data && error.response.data.message) {
          this.errorMessage = error.response.data.message;
        } else {
          this.errorMessage = 'Terjadi kesalahan saat mengganti password.';
        }
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>

<style scoped>
@keyframes fade-in {
  0% { opacity: 0; transform: translateY(20px);}
  100% { opacity: 1; transform: translateY(0);}
}
.animate-fade-in {
  animation: fade-in 0.5s ease;
}
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
