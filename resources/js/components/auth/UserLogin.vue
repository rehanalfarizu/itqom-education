<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primaryLight via-white to-primaryDark p-6 relative overflow-hidden">
    <!-- Background animated elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute top-10 left-10 w-20 h-20 bg-primaryLight rounded-full opacity-20 animate-float"></div>
      <div class="absolute top-1/2 right-20 w-32 h-32 bg-primaryDark rounded-full opacity-10 animate-float delay-1000"></div>
      <div class="absolute bottom-20 left-1/3 w-16 h-16 bg-primaryLight rounded-full opacity-15 animate-float delay-2000"></div>
    </div>

    <!-- Main container -->
    <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-2xl flex w-full max-w-4xl overflow-hidden relative animate-fade-in border border-white/20">
      <!-- Left side with animation -->
      <div class="hidden md:flex w-1/2 bg-gradient-to-br from-primaryDark to-primaryLight items-center justify-center relative overflow-hidden">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="text-center relative z-10 animate-slide-in">
          <div class="animate-float">
            <!-- Solusi 1: dotlottie-player -->
            <dotlottie-player
              src="https://lottie.host/da3cda18-274e-4907-b0c7-710b6d159bf2/I88cIwlTEp.lottie"
              background="transparent"
              speed="1"
              style="width: 450px; height: 450px"
              loop
              autoplay></dotlottie-player>
          </div>
          <div class="mt-4 text-white">
            <h3 class="text-2xl font-bold mb-2 animate-glow">Welcome Back!</h3>
            <p class="text-white/80 text-sm">Sign in to continue your journey</p>
          </div>
        </div>

        <!-- Decorative elements -->
        <div class="absolute top-5 right-5 w-24 h-24 border-2 border-white/20 rounded-full animate-pulse-slow"></div>
        <div class="absolute bottom-5 left-5 w-16 h-16 border-2 border-white/20 rounded-full animate-pulse-slow delay-1000"></div>
      </div>

      <!-- Right side with form -->
      <div class="w-full md:w-1/2 p-10 relative">
        <div class="animate-fade-in delay-300">
          <h2 class="text-3xl font-bold text-center mb-2 text-primaryDark">Login to Your Account</h2>
          <p class="text-center text-gray-600 mb-8">Enter your credentials to access your dashboard</p>

          <form @submit.prevent="submitForm" class="space-y-6">
            <!-- Email field -->
            <div class="relative">
              <label for="email" class="block mb-2 text-sm font-medium text-gray-700 transition-all duration-200"
                     :class="{ 'text-primaryDark': emailFocused }">
                Email Address
              </label>
              <div class="relative">
                <input
                  v-model="form.email"
                  type="email"
                  id="email"
                  required
                  @focus="emailFocused = true"
                  @blur="emailFocused = false"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primaryDark focus:border-transparent focus:outline-none transition-all duration-200 hover:border-primaryLight"
                  placeholder="Enter your email">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                  <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                  </svg>
                </div>
              </div>
            </div>

            <!-- Password field -->
            <div class="relative">
              <label for="password" class="block mb-2 text-sm font-medium text-gray-700 transition-all duration-200"
                     :class="{ 'text-primaryDark': passwordFocused }">
                Password
              </label>
              <div class="relative">
                <input
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  id="password"
                  required
                  @focus="passwordFocused = true"
                  @blur="passwordFocused = false"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primaryDark focus:border-transparent focus:outline-none transition-all duration-200 hover:border-primaryLight pr-12"
                  placeholder="Enter your password">
                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute inset-y-0 right-0 pr-3 flex items-center hover:text-primaryDark transition-colors duration-200">
                  <svg v-if="!showPassword" class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  <svg v-else class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L8.464 8.464M9.878 9.878l4.242 4.242m0 0L16.536 16.536M14.12 14.12l4.242 4.242" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Submit button -->
            <button
              type="submit"
              :disabled="isLoading"
              class="w-full bg-gradient-to-r from-primaryDark to-primaryLight hover:from-primaryLight hover:to-primaryDark text-white font-semibold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none relative overflow-hidden group">
              <span v-if="!isLoading" class="relative z-10">Login</span>
              <span v-else class="relative z-10 flex items-center justify-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Logging in...
              </span>
              <div class="absolute inset-0 bg-white/20 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
            </button>

            <!-- Error message -->
            <div v-if="errorMessage" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg animate-fade-in">
              <p class="text-red-600 text-sm text-center flex items-center justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ errorMessage }}
              </p>
            </div>
          </form>

          <!-- Register link -->
          <div class="text-center mt-8 p-4 bg-gray-50 rounded-lg">
            <p class="text-sm text-gray-600">
              Don't have an account?
              <router-link to="/register" class="text-primaryDark hover:text-primaryLight font-medium transition-colors duration-200 hover:underline">
                Register here
              </router-link>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'LoginPage',
  data() {
    return {
      form: {
        email: '',
        password: ''
      },
      errorMessage: '',
      isLoading: false,
      showPassword: false,
      emailFocused: false,
      passwordFocused: false
    };
  },
  mounted() {
    // Load dotlottie-player script if not already loaded
    this.loadLottiePlayer();
  },
  methods: {
    loadLottiePlayer() {
      // Check if dotlottie-player is already loaded
      if (window.customElements && window.customElements.get('dotlottie-player')) {
        return;
      }

      // Create and load the script
      const script = document.createElement('script');
      script.src = 'https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs';
      script.type = 'module';
      script.onload = () => {
        console.log('Lottie player loaded successfully');
      };
      script.onerror = (error) => {
        console.error('Failed to load Lottie player:', error);
      };
      document.head.appendChild(script);
    },
    async submitForm() {
      this.errorMessage = '';
      this.isLoading = true;

      try {
        const response = await axios.post('http://localhost:8000/api/login', this.form);

        // Simpan token dan data user
        localStorage.setItem('authToken', response.data.token);
        localStorage.setItem('user', JSON.stringify(response.data.user));

        // Dispatch event custom untuk memberitahu Navbar bahwa localStorage telah diupdate
        window.dispatchEvent(new Event('localStorageUpdated'));

        // Redirect ke dashboard
        this.$router.push('/');

      } catch (error) {
        this.errorMessage = error.response?.data?.message || 'Login failed. Please try again.';
      } finally {
        this.isLoading = false;
      }
    }
  }
}
</script>

<style scoped>
@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-10px); }
}

@keyframes glow {
  0% { text-shadow: 0 0 5px rgba(255, 255, 255, 0.5); }
  100% { text-shadow: 0 0 20px rgba(255, 255, 255, 0.8), 0 0 30px rgba(255, 255, 255, 0.6); }
}

@keyframes slide-in {
  0% { transform: translateX(-100%); opacity: 0; }
  100% { transform: translateX(0); opacity: 1; }
}

@keyframes fade-in {
  0% { opacity: 0; transform: translateY(20px); }
  100% { opacity: 1; transform: translateY(0); }
}

.animate-float {
  animation: float 6s ease-in-out infinite;
}

.animate-glow {
  animation: glow 2s ease-in-out infinite alternate;
}

.animate-slide-in {
  animation: slide-in 0.8s ease-out;
}

.animate-fade-in {
  animation: fade-in 0.6s ease-out;
}

.animate-pulse-slow {
  animation: pulse 3s ease-in-out infinite;
}

.delay-300 {
  animation-delay: 300ms;
}

.delay-1000 {
  animation-delay: 1000ms;
}

.delay-2000 {
  animation-delay: 2000ms;
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
  background: #564AB1;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #B0ABDB;
}
</style>
